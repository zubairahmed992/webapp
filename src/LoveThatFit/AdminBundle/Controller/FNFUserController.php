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
        $totalUserRecords = $this->get('fnfuser.helper.fnfuser')->countAllFNFUserRecord();
        $totalGroupRecords = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord();

        return $this->render('LoveThatFitAdminBundle:FNFUser:index_new.html.twig',
            array(
                'rec_count' => count($totalUserRecords),
                'rec_group_count' => count($totalGroupRecords)
            ));
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('fnfuser.helper.fnfuser')->searchFNFUser($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function groupPaginateAction( Request $request )
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('fnfgroup.helper.fnfgroup')->searchFNFGroup($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function groupDeleteAction( $id )
    {
        $fnfGroup = $groups = $this->get('fnfgroup.helper.fnfgroup')->findById( $id );
        $group = $this->get('fnfgroup.helper.fnfgroup')->markedGroupAsArchived( $fnfGroup );

        $this->get('session')->setFlash('success', 'FNF Group deleted!');
        return $this->redirect($this->generateUrl('fnf_groups'));
    }

    public function getGroupDataAction( Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('fnfgroup.helper.fnfgroup')->getGroupDataById($requestData['groupId']);
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function addAction()
    {
        $fnfUserEntity  = $this->get('fnfuser.helper.fnfuser')->createNew();
        $fnfGroupEntity = $this->get('fnfgroup.helper.fnfgroup')->createNew();

        /*$adminConfig = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:AdminConfig')
            ->findBy(array('config_key' => 'discount'))[0];

        $discountOptions = $adminConfig->getChildren()[0];
        $discountArray = array(
            'discount' => $adminConfig->getConfigValue(),
            'min_amount' => ( $discountOptions->getConfigKey() == 'min_amount' ? $discountOptions->getConfigValue() : 0)
        );*/

        $discountArray = array();

        $fnfUserEntity->addGroup( $fnfGroupEntity );

        $form = $this->createForm(new FNFUserForm('add',$fnfUserEntity, $discountArray), $fnfUserEntity);

        $user_list = $this->get('user.helper.user')->getListWithPagination(0,'email');
        $groups = $this->get('fnfgroup.helper.fnfgroup')->getGroups();


        return $this->render('LoveThatFitAdminBundle:FNFUser:new.html.twig',
            array(
                'users' => $user_list['users'],
                'form' => $form->createView(),
                'groups' => $groups
            ));

    }

    public function createAction(Request $request)
    {
        $fnfUserEntity = $this->get('fnfuser.helper.fnfuser')->createNew();
        $fnfGroupEntity = $this->get('fnfgroup.helper.fnfgroup')->createNew();
        $fnfUserEntity->addGroup( $fnfGroupEntity );

        $adminConfig = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:AdminConfig')
            ->findBy(array('config_key' => 'discount'))[0];

        $discountOptions = $adminConfig->getChildren()[0];
        $discountArray = array(
            'discount' => $adminConfig->getConfigValue(),
            'min_amount' => ( $discountOptions->getConfigKey() == 'min_amount' ? $discountOptions->getConfigValue() : 0)
        );
        $selectedGroup = $request->request->get('sel_group');
        $postData = $request->request->get('FNFUser');

        $groupData = $postData['groups'][0];
        $userData = $postData['users'];


        /*$entity = $this->get('fnfuser.helper.fnfuser')->createNew();
        $form = $this->createForm(new FNFUserForm('add',$entity, $discountArray), $entity);

        $form->bind($request);
        $data = $form->getData();*/

        if(!empty($userData)){
            if($selectedGroup == 0){
                /**Code By babar*/
                //Check if any group exists. Then make is it archive
                $groupToArchive = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord();
                //Iterate each group 
                foreach ($groupToArchive as $groupInfo) {
                    //make group archived
                    $this->get('fnfgroup.helper.fnfgroup')->markedGroupAsArchived( $groupInfo );
                }
                /**End Code By babar*/
                // var_dump( $groupData ); die;

                $newGroup = $groups = $this->get('fnfgroup.helper.fnfgroup')->addNewGroup( $groupData );
                $userCreated = $this->get('fnfuser.helper.fnfuser')->saveFNFUsers($newGroup, $userData);
                if($userCreated){
                    $this->get('session')->setFlash('success', 'User created and added to group!');
                    return $this->redirect($this->generateUrl('fnf_users'));
                }
            } else if( $selectedGroup > 0 ) {
                // var_dump( $userData ); die;
                $fnfGroup = $groups = $this->get('fnfgroup.helper.fnfgroup')->findById( $selectedGroup );
                $userCreated = $this->get('fnfuser.helper.fnfuser')->saveFNFUsers($fnfGroup, $userData);

                return $this->redirect($this->generateUrl('fnf_users'));
            }
        }

        $this->get('session')->setFlash('warning', 'You forget to select users!');
        $form = $this->createForm(new FNFUserForm('add',$fnfUserEntity, $discountArray), $fnfUserEntity);

        $groups = $this->get('fnfgroup.helper.fnfgroup')->getGroups();
        $user_list = $this->get('user.helper.user')->getListWithPagination(0,'email');

        return $this->render('LoveThatFitAdminBundle:FNFUser:new.html.twig',
            array(
                'users' => $user_list['users'],
                'form' => $form->createView(),
                'groups' => $groups
            ));
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

    public function deleteAction( $user_id, $group_id)
    {
        $fnfGroup = $groups = $this->get('fnfgroup.helper.fnfgroup')->findById( $group_id );
        $this->get('fnfuser.helper.fnfuser')->removeUsers( $fnfGroup, array( $user_id) );

        $this->get('session')->setFlash('success', 'FNF User deleted!');
        return $this->redirect($this->generateUrl('fnf_users'));
    }

    public function getApplicableFNFUserAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);

            // var_dump($fnfUser->getGroups()[0]->getDiscount()); die;

            if(is_object($fnfUser)){
                $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                    'discount_amount' => $fnfUser->getGroups()[0]->getDiscount(),
                    'min_amount'      => $fnfUser->getGroups()[0]->getMinAmount()
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