<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 2/20/2017
 * Time: 6:12 PM
 */

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\FNFUserForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FNFUserController extends Controller
{
    public function indexAction()
    {

        $totalUserRecords  = $this->get('fnfuser.helper.fnfuser')->countAllFNFUserRecord();
        $totalGroupRecords = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord();

        return $this->render('LoveThatFitAdminBundle:FNFUser:index_new.html.twig',
            array(
                'rec_count'       => count($totalUserRecords),
                'rec_group_count' => count($totalGroupRecords),
            ));
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output      = $this->get('fnfuser.helper.fnfuser')->searchFNFUser($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function groupPaginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output      = $this->get('fnfgroup.helper.fnfgroup')->searchFNFGroup($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function groupDeleteAction($id)
    {
        $fnfGroup = $groups = $this->get('fnfgroup.helper.fnfgroup')->findById($id);
        $group    = $this->get('fnfgroup.helper.fnfgroup')->markedGroupAsArchived($fnfGroup);

        $this->get('session')->setFlash('success', 'FNF Group deleted!');
        return $this->redirect($this->generateUrl('fnf_groups'));
    }

    public function getGroupDataAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output      = $this->get('fnfgroup.helper.fnfgroup')->getGroupDataById($requestData['groupId']);
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function addAction(Request $request)
    {

        $fnfUserEntity  = $this->get('fnfuser.helper.fnfuser')->createNew();
        $fnfGroupEntity = $this->get('fnfgroup.helper.fnfgroup')->createNew();

        $discountArray = array();

        $fnfUserEntity->addGroup($fnfGroupEntity);

        $form = $this->createForm(new FNFUserForm('add', $fnfUserEntity, $discountArray), $fnfUserEntity);

        $user_list = $this->get('user.helper.user')->getListWithPagination(0, 'email');
        $groups    = $this->get('fnfgroup.helper.fnfgroup')->getGroups();

        return $this->render('LoveThatFitAdminBundle:FNFUser:new.html.twig',
            array(
                'users'  => $user_list['users'],
                'form'   => $form->createView(),
                'groups' => $groups,
            ));

    }

    public function createAction(Request $request)
    {
        $fnfUserEntity  = $this->get('fnfuser.helper.fnfuser')->createNew();
        $fnfGroupEntity = $this->get('fnfgroup.helper.fnfgroup')->createNew();
        $fnfUserEntity->addGroup($fnfGroupEntity);
        $discountArray = array();

        /*$adminConfig = $this->getDoctrine()
        ->getRepository('LoveThatFitAdminBundle:AdminConfig')
        ->findBy(array('config_key' => 'discount'))[0];

        $discountOptions = $adminConfig->getChildren()[0];
        $discountArray = array(
        'discount' => $adminConfig->getConfigValue(),
        'min_amount' => ( $discountOptions->getConfigKey() == 'min_amount' ? $discountOptions->getConfigValue() : 0)
        );*/
        $selectedGroup = $request->request->get('sel_group');
        $postData      = $request->request->get('FNFUser');

        $groupData = $postData['groups'][0];
        $userData  = $postData['users'];

        /*$entity = $this->get('fnfuser.helper.fnfuser')->createNew();
        $form = $this->createForm(new FNFUserForm('add',$entity, $discountArray), $entity);

        $form->bind($request);
        $data = $form->getData();*/

        if (!empty($userData)) {
            if ($selectedGroup == 0) {
                /**Code By babar*/
                //Check if any group exists. Then make is it archive
                $groupToArchive = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord();
                //Iterate each group
                foreach ($groupToArchive as $groupInfo) {
                    //make group archived
                    $this->get('fnfgroup.helper.fnfgroup')->markedGroupAsArchived($groupInfo);
                }
                /**End Code By babar*/
                // var_dump( $groupData ); die;

                $newGroup    = $groups    = $this->get('fnfgroup.helper.fnfgroup')->addNewGroup($groupData);
                $userCreated = $this->get('fnfuser.helper.fnfuser')->saveFNFUsers($newGroup, $userData);
                if ($userCreated) {
                    $this->get('session')->setFlash('success', 'User created and added to group!');
                    return $this->redirect($this->generateUrl('fnf_users'));
                }
            } else if ($selectedGroup > 0) {
                // var_dump( $userData ); die;
                $fnfGroup    = $groups    = $this->get('fnfgroup.helper.fnfgroup')->findById($selectedGroup);
                $userCreated = $this->get('fnfuser.helper.fnfuser')->saveFNFUsers($fnfGroup, $userData);

                return $this->redirect($this->generateUrl('fnf_users'));
            }
        }

        $this->get('session')->setFlash('warning', 'You forget to select users!');
        $form = $this->createForm(new FNFUserForm('add', $fnfUserEntity, $discountArray), $fnfUserEntity);

        $groups    = $this->get('fnfgroup.helper.fnfgroup')->getGroups();
        $user_list = $this->get('user.helper.user')->getListWithPagination(0, 'email');

        return $this->render('LoveThatFitAdminBundle:FNFUser:new.html.twig',
            array(
                'users'  => $user_list['users'],
                'form'   => $form->createView(),
                'groups' => $groups,
            ));
    }

    public function getCsvFnfImportAction(Request $request)
    {
        $fnfCsvform = $this->createFormBuilder()
            ->add('submitFile', 'file', array('label' => 'Upload CSV file'))
            ->getForm();

        // Check if we are posting stuff
        if ($request->getMethod('post') == 'POST') {
            try {
                // Bind request to the form
                $fnfCsvform->bindRequest($request);

                // If form is valid
                if ($fnfCsvform->isValid()) {
                    // Get file
                    $file         = $fnfCsvform->get('submitFile');
                    $fileInfo     = $file->getData();
                    $fileNameInfo = explode('.', $fileInfo->getClientOriginalName());
                    if (!is_array($fileNameInfo) || !isset($fileNameInfo[1]) || !in_array('csv', $fileNameInfo)) {
                        $this->get('session')->setFlash('warning', 'Invalid File');
                        return $this->redirect($this->generateUrl('admin_csv_fnf_create_user'));
                    }

                    if (($handle = fopen($fileInfo->getPathName(), "r")) !== false) {
                        //user based info
                        $userInfo = array();
                        //group information
                        $groupTitle          = false;
                        $groupDiscountAmount = false;
                        $groupMinAmount      = false;
                        $groupStartDate      = false;
                        $groupEndDate        = false;
                        //Group information
                        $groupInfoNew = array();

                        while (($row = fgetcsv($handle)) !== false) {
                            //skip first row
                            if (isset($row[0]) && $row[0] !== 'user_id') {

                                //get group info
                                $groupTitle          = ($groupTitle === false) ? $row[3] : $groupTitle;
                                $groupDiscountAmount = ($groupDiscountAmount === false) ? $row[4] : $groupDiscountAmount;
                                $groupMinAmount      = ($groupMinAmount === false) ? $row[5] : $groupMinAmount;
                                $groupStartDate      = ($groupStartDate === false) ? $row[6] : $groupStartDate;
                                $groupEndDate        = ($groupEndDate === false) ? $row[7] : $groupEndDate;

                                //get user info
                                $userInfo[$row[0]]['first_name'] = $row[1];
                                $userInfo[$row[0]]['last_name']  = $row[2];

                            }

                        }

                    } else {
                        $this->get('session')->setFlash('warning', 'Invalid File');
                        return $this->redirect($this->generateUrl('admin_csv_fnf_create_user'));
                    }

                    //Craete New group
                    if ($groupTitle && is_numeric($groupDiscountAmount) && is_numeric($groupMinAmount) && $groupStartDate && $groupEndDate) {

                        $expStartDate   = explode('/', trim($groupStartDate));
                        $newStartFormat = $expStartDate[1] . '/' . $expStartDate[0] . '/' . $expStartDate[2];

                        $expEndDate   = explode('/', trim($groupEndDate));
                        $newEndFormat = $expEndDate[1] . '/' . $expEndDate[0] . '/' . $expEndDate[2];

                        //Arrange in array
                        $groupInfoNew['groupTitle'] = $groupTitle;
                        $groupInfoNew['discount']   = $groupDiscountAmount;
                        $groupInfoNew['min_amount'] = $groupMinAmount;
                        $groupInfoNew['start_at']   = trim($newStartFormat);
                        $groupInfoNew['end_at']     = trim($newEndFormat);

                        //Check if any group exists. Then make is it archive
                        $groupToArchive = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord();
                        //Iterate each group
                        foreach ($groupToArchive as $groupInfo) {
                            //make group archived
                            $this->get('fnfgroup.helper.fnfgroup')->markedGroupAsArchived($groupInfo);
                        }

                        //Add new group
                        $newGroup = $this->get('fnfgroup.helper.fnfgroup')->addNewGroup($groupInfoNew);

                        //Insert Users in group
                        if (is_array($userInfo) && count($userInfo) > 0) {

                            $userID = array_keys($userInfo);
                            //Assign user to group
                            $this->get('fnfuser.helper.fnfuser')->saveFNFUsers($newGroup, $userID);

                            //Update user first & last name
                            foreach ($userInfo as $userToUpdateKey => $userToUpdateValue) {

                                $updateUserInfo = $this->get('user.helper.user')->find($userToUpdateKey);
                                //Validate if value is given
                                if ($updateUserInfo && isset($userToUpdateValue['first_name'])
                                    && isset($userToUpdateValue['last_name'])
                                    && $userToUpdateValue['first_name'] != ""
                                    && $userToUpdateValue['last_name'] != ""
                                ) {
                                    $this->get('user.helper.user')->updateUserFirstAndLastName($updateUserInfo, $userToUpdateValue['first_name'], $userToUpdateValue['last_name']);
                                }

                            }
                        }
                    } else {
                        $this->get('session')->setFlash('warning', 'Invalid File');
                        return $this->redirect($this->generateUrl('admin_csv_fnf_create_user'));
                    }

                    $this->get('session')->setFlash('success', 'Group created successfully');
                    return $this->redirect($this->generateUrl('fnf_users'));

                }

            } catch (\Exception $e) {
                $this->get('session')->setFlash('warning', 'Invalid File');
                return $this->redirect($this->generateUrl('admin_csv_fnf_create_user'));
            }

        }

        return $this->render('LoveThatFitAdminBundle:FNFUser:fnf-upload.html.twig', array(
            'fvfImportform' => $fnfCsvform->createView(),
        )
        );

    }

    public function editAction($fnf_id)
    {
        if ($fnf_id) {
            $entity = $this->get('fnfuser.helper.fnfuser')->findById($fnf_id);
            if ($entity) {
                $user_list  = $this->get('user.helper.user')->getListWithPagination(0, 'email');
                $form       = $this->createForm(new FNFUserForm('edit', $entity), $entity);
                $deleteForm = $this->createForm(new DeleteType(), $entity);

                return $this->render('LoveThatFitAdminBundle:FNFUser:edit.html.twig', array(
                    'form'        => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity'      => $entity,
                    'users'       => $user_list['users'],
                ));
            }
            $this->get('session')->setFlash('warning', 'FNF User Not Found!');
        }

        $this->get('session')->setFlash('warning', 'FNF User Not Found!');
        return $this->redirect($this->generateUrl('fnf_users'));
    }

    public function updateAction(Request $request, $fnf_id)
    {
        $entity    = $this->get('fnfuser.helper.fnfuser')->findById($fnf_id);
        $user_list = $this->get('user.helper.user')->getListWithPagination(0, 'email');
        if ($entity) {
            $form = $this->createForm(new FNFUserForm('edit', $entity), $entity);
            $form->bind($request);

            $entity = $this->get('fnfuser.helper.fnfuser')->saveFNFUser($entity);
            return $this->redirect($this->generateUrl('fnf_users'));
        }

        $this->get('session')->setFlash('warning', 'Some thing went wrong FNF User not updated!');
        return $this->redirect($this->generateUrl('fnf_users'));
    }

    public function deleteAction($user_id, $group_id)
    {
        $fnfGroup = $groups = $this->get('fnfgroup.helper.fnfgroup')->findById($group_id);
        $this->get('fnfuser.helper.fnfuser')->removeUsers($fnfGroup, array($user_id));

        $this->get('session')->setFlash('success', 'FNF User deleted!');
        return $this->redirect($this->generateUrl('fnf_users'));
    }

    public function getApplicableFNFUserAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user    = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);

            // var_dump($fnfUser->getGroups()[0]->getDiscount()); die;

            if (is_object($fnfUser)) {
                $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                    'discount_amount' => $fnfUser->getGroups()[0]->getDiscount(),
                    'min_amount'      => $fnfUser->getGroups()[0]->getMinAmount(),
                ));
            } else {
                $res = $this->get('webservice.helper')->response_array(false, 'user in not applicable for discount.');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response($res);
    }
}
