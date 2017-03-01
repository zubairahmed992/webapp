<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 2/20/2017
 * Time: 6:12 PM
 */

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Form\Type\FNFUserForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;


class FNFUserController extends Controller
{
    public function indexAction()
    {
        $totalRecords = $this->get('fnfuser.helper.fnfuser')->countAllFNFUserRecord();

        return $this->render('LoveThatFitAdminBundle:FNFUser:index_new.html.twig',
            array('rec_count' => count($totalRecords))
        );
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('fnfuser.helper.fnfuser')->searchFNFUser($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function addAction()
    {
        $entity = $this->get('fnfuser.helper.fnfuser')->createNew();
        $form = $this->createForm(new FNFUserForm('add',$entity), $entity);

        $user_list = $this->get('user.helper.user')->getListWithPagination(0,'email');
        return $this->render('LoveThatFitAdminBundle:FNFUser:new.html.twig',
            array('users' => $user_list['users'], 'form' => $form->createView())
        );

    }

    public function createAction(Request $request)
    {
        $entity = $this->get('fnfuser.helper.fnfuser')->createNew();
        $selectedUser = $request->request->get('sel_user');
        $user = $this->get('webservice.helper')->findUserByAuthToken($selectedUser);

        $fnfUser = $this->get('fnfuser.helper.fnfuser')->getFNFUserById( $user );

        if(!is_object( $fnfUser )){
            $entity->setUsers( $user );
            $form = $this->createForm(new FNFUserForm('add',$entity), $entity);
            $form->bind($request);

            $this->get('session')->setFlash('success', 'FNF User Added!');
            $entity = $this->get('fnfuser.helper.fnfuser')->saveFNFUser( $entity );
            return $this->redirect($this->generateUrl('fnf_users'));
        }

        $this->get('session')->setFlash('warning', 'User already exists!');

        $form = $this->createForm(new FNFUserForm('add',$entity), $entity);
        $form->bind($request);
        $user_list = $this->get('user.helper.user')->getListWithPagination(0,'email');

        return $this->render('LoveThatFitAdminBundle:FNFUser:new.html.twig',
            array('users' => $user_list['users'], 'form' => $form->createView())
        );

    }

    public function editAction($fnf_id)
    {
        if($fnf_id)
        {
            $entity = $this->get('fnfuser.helper.fnfuser')->findById( $fnf_id );
            if($entity)
            {
                $user_list = $this->get('user.helper.user')->getListWithPagination(0,'email');
                $form = $this->createForm(new FNFUserForm('edit',$entity), $entity);
                $deleteForm = $this->createForm(new DeleteType(), $entity);

                return $this->render('LoveThatFitAdminBundle:FNFUser:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,
                    'users' => $user_list['users']
                ));
            }
            $this->get('session')->setFlash('warning', 'FNF User Not Found!');
        }

        $this->get('session')->setFlash('warning', 'FNF User Not Found!');
        return $this->redirect($this->generateUrl('fnf_users'));
    }

    public function updateAction( Request $request, $fnf_id)
    {
        $entity = $this->get('fnfuser.helper.fnfuser')->findById( $fnf_id );
        $user_list = $this->get('user.helper.user')->getListWithPagination(0,'email');
        if($entity){
            $form = $this->createForm(new FNFUserForm('edit',$entity), $entity);
            $form->bind($request);

            $entity = $this->get('fnfuser.helper.fnfuser')->saveFNFUser( $entity );
            return $this->redirect($this->generateUrl('fnf_users'));
        }

        $this->get('session')->setFlash('warning', 'Some thing went wrong FNF User not updated!');
        return $this->redirect($this->generateUrl('fnf_users'));
    }

    public function deleteAction( $fnf_id)
    {
        $entity = $this->get('fnfuser.helper.fnfuser')->findById( $fnf_id );
        $this->get('fnfuser.helper.fnfuser')->removeFNFUser( $entity );

        $this->get('session')->setFlash('success', 'FNF User deleted!');
        return $this->redirect($this->generateUrl('fnf_users'));
    }

    public function getApplicableFNFUserAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);
            if(is_object($fnfUser)){
                $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                    'discount_amount' => $fnfUser->getDiscount(),
                    'min_amount'      => $fnfUser->getMinAmount()
                ));
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'user in not applicable for discount.');
            }
        }
        else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }
}