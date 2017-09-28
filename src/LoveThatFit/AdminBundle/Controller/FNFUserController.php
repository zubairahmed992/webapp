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
                'rec_count'       => $totalUserRecords,
                'rec_group_count' => $totalGroupRecords,
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
        //var_dump($request); die;
        $fnfUserEntity  = $this->get('fnfuser.helper.fnfuser')->createNew();
        $fnfGroupEntity = $this->get('fnfgroup.helper.fnfgroup')->createNew();

        $discountArray = array();

        $fnfUserEntity->addGroup($fnfGroupEntity);

        $form = $this->createForm(new FNFUserForm('add', $fnfUserEntity, $discountArray), $fnfUserEntity);

        $user_list = $this->get('user.helper.user')->getListWithPagination(0, 'email');
        $groups    = $this->get('fnfgroup.helper.fnfgroup')->getGroups();

        $get_users_group = $this->get('fnfuser.helper.fnfuser')->getUsersGroupData();
        //echo "<pre>"; print_r($get_users_group); die();

        return $this->render('LoveThatFitAdminBundle:FNFUser:new.html.twig',
            array(
                'users'  => $user_list['users'],
                'form'   => $form->createView(),
                'groups' => $groups,
                'user_groups' => $get_users_group,
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
        $group_typ = $groupData['group_type'];

        /*$entity = $this->get('fnfuser.helper.fnfuser')->createNew();
        $form = $this->createForm(new FNFUserForm('add',$entity, $discountArray), $entity);

        $form->bind($request);
        $data = $form->getData();*/

        if (!empty($userData)) {
            if ($selectedGroup == 0) {
                /**Code By babar*/
                //Check if any group exists. Then make is it archive
                //$groupToArchive = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord($group_typ);
                //Iterate each group
                /*foreach ($groupToArchive as $groupInfo) {
                    //make group archived
                    $this->get('fnfgroup.helper.fnfgroup')->markedGroupAsArchived($groupInfo);
                }*/
                /**End Code By babar*/
                // var_dump( $groupData ); die;

                $checkGroupAlreadyExist = $this->get('fnfgroup.helper.fnfgroup')->getGroupDataByName($groupData['groupTitle']);

                if($checkGroupAlreadyExist) {
                    //group title already exists
                    $this->get('session')->setFlash('warning', 'Group title is already exists!');
                    return $this->redirect($this->generateUrl('add_fnf_user'));
                } else {
                    //new group
                    $this->get('fnfgroup.helper.fnfgroup')->checkFnfUserUpdate(implode(",",$userData));

                    $newGroup    = $groups    = $this->get('fnfgroup.helper.fnfgroup')->addNewGroup($groupData);
                    $userCreated = $this->get('fnfuser.helper.fnfuser')->saveFNFUsers($newGroup, $userData);
                    if ($userCreated) {
                        $this->get('session')->setFlash('success', 'User created and added to group!');
                        return $this->redirect($this->generateUrl('fnf_users'));
                    }
                }
            } else if ($selectedGroup > 0) {
                // var_dump( $userData ); die;
                $this->get('fnfgroup.helper.fnfgroup')->checkFnfUserUpdate(implode(",",$userData));
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

        
        $ExistingGroup = array();
        $userWithPreviousGroup = array();
        $groupTitle = "";
        $existGroup = "";
        $fnfCsvform = $this->createFormBuilder()
            ->add('submitFile', 'file', array('label' => 'Upload CSV file'))
            ->getForm();

        $newStartFormat = $newEndFormat = "";

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
                        $group_type          = false;
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
                                $group_type          = ($group_type === false) ? $row[8] : $group_type;

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
                    if ($groupTitle && is_numeric($groupDiscountAmount) && is_numeric($groupMinAmount) && $groupDiscountAmount > 0
                            && ( ( $group_type == 1 && $groupMinAmount > 0 && $groupStartDate && $groupEndDate) || ($group_type == 2))) {

                        if($group_type == 1){
                            $expStartDate   = explode('/', trim($groupStartDate));
                            $newStartFormat = $expStartDate[1] . '/' . $expStartDate[0] . '/' . $expStartDate[2];

                            $expEndDate   = explode('/', trim($groupEndDate));
                            $newEndFormat = $expEndDate[1] . '/' . $expEndDate[0] . '/' . $expEndDate[2];
                        }

                        //Arrange in array
                        $groupInfoNew['groupTitle'] = $groupTitle;
                        $groupInfoNew['discount']   = $groupDiscountAmount;
                        $groupInfoNew['min_amount'] = $groupMinAmount;
                        $groupInfoNew['start_at']   = trim($newStartFormat);
                        $groupInfoNew['end_at']     = trim($newEndFormat);
                        $groupInfoNew['group_type'] = $group_type;

                        //Check if any group exists. Then make is it archive
                        //$groupToArchive = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord($group_type);
                        //Iterate each group

                        //Committed by Shakeel
                        //foreach ($groupToArchive as $groupInfo) {
                            //make group archived
                        //    $this->get('fnfgroup.helper.fnfgroup')->markedGroupAsArchived($groupInfo);
                        //}

                        //Add new group
                        

                        //Insert Users in group
                        if (is_array($userInfo) && count($userInfo) > 0) {

                            $userID = array_keys($userInfo);

                            $userWithPreviousGroup = $this->get('fnfgroup.helper.fnfgroup')->checkFnfUserToUniqueGroup(implode(",",$userID),$group_type);

                            $ExistingGroup =  $this->get('fnfgroup.helper.fnfgroup')->getExistingFnfGroups($groupTitle);

                            

                            

                            
                            if(count($userWithPreviousGroup) == 0 && count($ExistingGroup)==0)
                            {  



                                //$userWithPreviousGroup = $this->get('fnfgroup.helper.fnfgroup')->getGroupDataByName($groupTitle);

                            

                                $newGroup = $this->get('fnfgroup.helper.fnfgroup')->addNewGroup($groupInfoNew);
                             

                                 $this->get('fnfgroup.helper.fnfgroup')->checkFnfUserUpdate(implode(",",$userID));



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
                        }
                    }
                    else {
                        $this->get('session')->setFlash('warning', 'Invalid File');
                        return $this->redirect($this->generateUrl('admin_csv_fnf_create_user'));
                    }

                    if(count($userWithPreviousGroup) == 0 && count($ExistingGroup)==0)
                    { 

                    $this->get('session')->setFlash('success', 'Group created successfully');
                    return $this->redirect($this->generateUrl('fnf_users'));
                    }

                }

            } catch (\Exception $e) {
                echo $e;
                exit;
                $this->get('session')->setFlash('warning', 'Invalid File');
                return $this->redirect($this->generateUrl('admin_csv_fnf_create_user'));
            }

        }


        $existids = "";    
        foreach($userWithPreviousGroup as $rs)
        {
            $existids .= $rs['user_id']." ( ".$rs['groupTitle']." ), ";

        }

        $existGroup = ""; 
       if(count($ExistingGroup) > 0)
        {

                $existGroup = 'Group Name ( '.$groupTitle.' ) already exist. Please change the group name to procced.';

          
        }   

        return $this->render('LoveThatFitAdminBundle:FNFUser:fnf-upload.html.twig', array(
            'fvfImportform' => $fnfCsvform->createView(),
            'userInPreviousGroup' => trim($existids,", "),
            'existGroup' => $existGroup,
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

    public function ___getApplicableFNFUserAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user    = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);

            if(is_object($fnfUser)){
                foreach($fnfUser->getGroups() as $group)
                {
                    if($group->getIsArchive() == 0)
                    {
                        $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                            'discount_amount' => $group->getDiscount(),
                            'min_amount'      => $group->getMinAmount(),
                            'group_type'      => $group->getGroupType(),
                        ));
                    }
                }

            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'user in not applicable for discount.');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response($res);
    }

    public function getApplicableFNFUserAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user    = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);

            if(is_array($fnfUser)){
                if( $fnfUser['group_type'] == 1 ){
                    $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                        'discount_amount' => $fnfUser['discount'],
                        'min_amount'      => $fnfUser['minAmount'],
                        'group_type'      => $fnfUser['group_type'],
                    ));
                }else if( $fnfUser['group_type'] == 2 )
                {
                    $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                        'discount_amount' => $this->getUserDiscountAmount($fnfUser['discount'], $fnfUser['token']),
                        'min_amount'      => 0,
                        'group_type'      => $fnfUser['group_type'],
                    ));
                }

            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'user in not applicable for discount.');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response($res);
    }

    public function nwsGetApplicableFNFUserAction()
    {
        $res = "";
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user    = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);

            $order_sales_tax = 0;
            $error_sales_tax = NULL;
            try {
                //get order sales tax
                $order_sales_tax = $this->getOrderSalesTaxUserAction(1);
                if(is_float($order_sales_tax) || is_numeric($order_sales_tax)) {
                    $order_sales_tax = $order_sales_tax;
                } else {
                    $data_sales = json_decode($order_sales_tax);
                    $order_sales_tax = $data_sales->data->sales_tax;
                    $error_sales_tax = array(
                                        'error' => $data_sales->data->error,
                                        'code' => $data_sales->data->code,
                                        'message' => $data_sales->message
                                    );
                }
            } catch(\Exception $e) {
                // log $e->getMessage()
            }

            if(is_array($fnfUser)){

                if( $fnfUser['group_type'] == 1 ){
                    $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                        'discount_amount' => $fnfUser['discount'],
                        'min_amount'      => $fnfUser['minAmount'],
                        'group_type'      => $fnfUser['group_type'],
                        'percentage_amount' => 0,
                        'applicable'        => true,
                        'sales_tax'        => $order_sales_tax,
                        'error_sales_tax'  => $error_sales_tax
                    ));
                }else if( $fnfUser['group_type'] == 2 )
                {
                    $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                        'discount_amount' => (string) $this->getUserDiscountAmount($fnfUser['discount'], $fnfUser['token']),
                        'min_amount'      => 0,
                        'group_type'      => $fnfUser['group_type'],
                        'percentage_amount' => $fnfUser['discount'],
                        'applicable'        => true,
                        'sales_tax'        => $order_sales_tax,
                        'error_sales_tax'  => $error_sales_tax
                    ));
                }

            }else{
                $res = $this->get('webservice.helper')->response_array(true, 'user in not applicable for discount.', true, array(
                    'discount_amount' => 0,
                    'min_amount'      => 0,
                    'group_type'      => 0,
                    'percentage_amount' => 0,
                    'applicable'        => false,
                    'sales_tax'        => $order_sales_tax,
                    'error_sales_tax'  => $error_sales_tax
                ));
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response($res);
    }

    public function getUserDiscountAmount( $discount, $token)
    {
        $amount = 0;
        $user = $this->get('webservice.helper')->findUserByAuthToken($token);
        $user_cart = $this->container->get('cart.helper.cart')->getUserCart($user);
        foreach($user_cart as $cart){
            //$amount = $cart['price'] + $amount;
            $amount = ($cart['qty']*$cart['price']) + $amount;
        }

        $dicount_amount = ($discount / 100) * $amount;

        return $dicount_amount;
    }

    public function getOrderSalesTaxUserAction($callby=0) {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        return $this->get('webservice.helper')->getOrderSalesTaxUserAction($callby, $decoded);
    }
}
