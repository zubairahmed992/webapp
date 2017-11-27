<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Form\Type\MannequinTestType;
use LoveThatFit\AdminBundle\Form\Type\RetailerSiteUserType;
use LoveThatFit\AdminBundle\Form\Type\UserMeasurementType;
use LoveThatFit\AdminBundle\Form\Type\UserProfileSettingsType;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    //--------------------------User List-------------------------------------------------------------
    /*
    public function indexAction($page_number, $sort = 'id') {

    $size_with_pagination = $this->get('user.helper.user')->getListWithPagination($page_number, $sort);
    return $this->render('LoveThatFitAdminBundle:User:index.html.twig', array('pagination' => $size_with_pagination, 'searchform' => $this->userSearchFrom()->createView()));
    }
     */

    public function indexAction()
    {
        $totalRecords = $this->get('user.helper.user')->countAllUser();
        $femaleUsers  = $this->get('user.helper.user')->countUsersByGender('f');
        $maleUsers    = $this->get('user.helper.user')->countUsersByGender('m');

        return $this->render('LoveThatFitAdminBundle:User:index_new.html.twig',
            array(
                'rec_count'   => $totalRecords,
                'femaleUsers' => $femaleUsers,
                'maleUsers'   => $maleUsers,
            )
        );
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output      = $this->get('user.helper.user')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    //--------------------------User List-------------------------------------------------------------
    public function jsonAction($id)
    {
        $user                     = $this->get('user.helper.user')->find($id);
        $ar['actual_measurement'] = $user->getMeasurement()->getArray();
        $ar['json_stored']        = json_decode($user->getMeasurement()->getMeasurementJson());
        $ar['masked_marker']      = $this->get('user.marker.helper')->getPridictedMeasurementArray($user);
        return new Response(json_encode($ar));
    }

    private function getMaskedMarkerSpecs()
    {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/mask_marker.yml'));
    }

    //-------------------------Show user detail-------------------------------------------------------
    public function showAction($id)
    {
        $entity      = $this->get('user.helper.user')->find($id);
        $log_count   = $this->get('user.helper.userappaccesslog')->getAppAccessLogCount($entity);
        $user_limit  = $this->get('user.helper.user')->getRecordsCountWithCurrentUserLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($user_limit[0]['id']));
        $page_number = $page_number == 0 ? 1 : $page_number;
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'User not found!');
        }
        if (!$entity->getOriginalUser()) {
            $duplicate_user  = '0';
            $duplicate_list  = $entity->getDuplicateUsers();
            $duplicate_count = count($duplicate_list);
        } else {
            $duplicate_user  = '1';
            $duplicate_list  = $entity->getOriginalUser();
            $duplicate_count = 0;
        }
//      foreach($duplicate_list as $val){
        //        echo $val->getId();
        //      }
        //          die;

        return $this->render('LoveThatFitAdminBundle:User:show.html.twig', array(
            'user'            => $entity,
            'duplicate_user'  => $duplicate_user,
            'duplicate_list'  => $duplicate_list,
            'duplicate_count' => $duplicate_count,
            'page_number'     => $page_number,
            'log_count'       => $log_count,
            'product'         => $this->get('site.helper.usertryitemhistory')->countUserTiredProducts($entity),
            'brand'           => $this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity),
            'brandtried'      => count($this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity)),
        ));
    }

    //-------------------------Show user detail-------------------------------------------------------
    public function setDefaultUserAction($id, $demo = false)
    {
        $user = $this->get('user.helper.user')->find($id);
        if ($user) {
            $d = $this->get('user.marker.helper')->setDefaultUserAs($user, $demo === 'true' ? true : false);
        }
        $user_limit  = $this->get('user.helper.user')->getRecordsCountWithCurrentUserLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($user_limit[0]['id']));
        $page_number = $page_number == 0 ? 1 : $page_number;

        return $this->render('LoveThatFitAdminBundle:User:show.html.twig', array(
            'user'        => $user,
            'page_number' => $page_number,
            'product'     => $this->get('site.helper.usertryitemhistory')->countUserTiredProducts($user),
            'brand'       => $this->get('site.helper.usertryitemhistory')->findUserTiredBrands($user),
            'brandtried'  => count($this->get('site.helper.usertryitemhistory')->findUserTiredBrands($user)),
        ));
    }

    //--------------------------User Serach------------------------------------------------------------
    public function searchAction(Request $request)
    {
        $em        = $this->getDoctrine()->getManager();
        $data      = $request->request->all();
        $gender    = $data['form']['gender'];
        $firstname = $data['form']['firstname'];
        $lastname  = $data['form']['firstname'];
        if ($data['form']['age'] == '') {
            $age = '';
        } else {
            $age           = $data['form']['age'];
            $endDate       = $this->get('user.helper.user')->getUserBirthDate($age);
            $new_timestamp = strtotime('-12 months', strtotime($endDate));
            $beginDate     = date("Y-m-d", $new_timestamp);
        }
        if ($firstname == '' and $gender == '') {
            $entity = $this->get('user.helper.user')->findByBirthDateRange($beginDate, $endDate);
        }
        if ($firstname == '' and $age == '') {
            $entity = $this->get('user.helper.user')->findByGender($gender);
        }
        if ($gender == '' and $age == '') {
            $entity = $this->get('user.helper.user')->findByName($firstname, $lastname);
        }
        if ($gender != '' and $firstname != '') {
            $entity = $this->get('user.helper.user')->findByGenderName($firstname, $lastname, $gender);
        }
        if ($gender != '' and $firstname != '' and $age != '') {
            $entity = $this->get('user.helper.user')->findByNameGenderBirthDateRange($firstname, $lastname, $gender, $beginDate, $endDate);
        }
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find User.');
            return $this->render('LoveThatFitAdminBundle:User:search.html.twig', array(
                'user'       => $entity,
                'searchform' => $this->userSearchFrom()->createView(),
            ));
        } else {
            return $this->render('LoveThatFitAdminBundle:User:search.html.twig', array(
                'user'       => $entity,
                'searchform' => $this->userSearchFrom()->createView(),
            ));
        }
    }

    //-------------------------------Edit User-----------------------------------------------------------
    public function editAction($id)
    {
        $entity          = $this->get('user.helper.user')->find($id);
        $measurement     = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        $userForm        = $this->createForm(new UserProfileSettingsType(), $entity);
        $password_form   = $this->password_update_form($entity);
        return $this->render('LoveThatFitAdminBundle:User:edit.html.twig', array(
            'form'          => $measurementForm->createView(),
            'userform'      => $userForm->createView(),
            'measurement'   => $measurement,
            'entity'        => $entity,
            'password_form' => $password_form->createView(),
        ));
    }

    public function editEmailAction($id){
        $entity          = $this->get('user.helper.user')->find($id);
        return $this->render('LoveThatFitAdminBundle:User:edit_email.html.twig', array(
            'entity'        => $entity,
        ));
    }

    public function editAccountTypeAction($id){
        $entity          = $this->get('user.helper.user')->find($id);
        return $this->render('LoveThatFitAdminBundle:User:edit_accounttype.html.twig', array(
            'entity'        => $entity,
        ));
    }


    public function updateEmailAction(Request $request){
        $data       = $request->request->all();
        $user       = $this->get('user.helper.user')->find($data['id']);

        $updated    = $this->get('user.helper.user')->updateUserEmail( $user, $data['user_email'] );
        if($updated){
            $this->get('session')->setFlash('success', 'Email Updated Successfully');
        }else{
            $this->get('session')->setFlash('error', 'Some thing went wrong try again later!');
        }


        return $this->redirect($this->generateUrl('admin_edit_user_email', array('id' => $data['id'])));

    }

    public function updateAccountTypeAction(Request $request){
        $data       = $request->request->all();
        $user       = $this->get('user.helper.user')->find($data['id']);

        $updated    = $this->get('user.helper.user')->updateAccountType( $user, $data['acct_type'] );
        if($updated){
            $this->get('session')->setFlash('success', 'Account Type Updated Successfully');
        }else{
            $this->get('session')->setFlash('error', 'Some thing went wrong try again later!');
        }


        return $this->redirect($this->generateUrl('admin_edit_user_account_type', array('id' => $data['id'])));

    }



    #----------------------------------------------------
    private function password_update_form($user)
    {
        $password_form = $this->createFormBuilder($user)
            ->add('password', 'repeated', array(
                'first_name'      => 'password',
                'second_name'     => 'confirm',
                'type'            => 'password',
                'invalid_message' => 'The password fields must match.',
            ))
            ->getForm();
        return $password_form;
    }

    #---------------------------------------------------
    public function passwordUpdateAction($id)
    {
        $user          = $this->get('user.helper.user')->find($id);
        $password_form = $this->password_update_form($user);
        $password_form->bind($this->getRequest());
        $data = $password_form->getData();
        $user->setPassword($data->getPassword());
        $password = $this->get('user.helper.user')->encodePassword($user);
        $user->setPwd($data->getPassword());
        $user->setPassword($password);
        $this->get('user.helper.user')->saveUser($user);
        $this->get('session')->setFlash('Success', 'Password Updated Successfully');
        return $this->redirect($this->generateUrl('admin_user_detail_edit', array('id' => $id)));
    }

    //--------------------------Update User--------------
    public function updateAction($id)
    {
        $entity          = $this->get('user.helper.user')->find($id);
        $measurement     = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        $measurementForm->bind($this->getRequest());
        $measurement->setUpdatedAt(new \DateTime('now'));
        $this->get('user.helper.measurement')->saveMeasurement($measurement);
        $this->get('session')->setFlash('success', 'Updated Successfuly');
        $userForm = $this->createForm(new UserProfileSettingsType(), $entity);
        $password_form   = $this->password_update_form($entity);
        return $this->render('LoveThatFitAdminBundle:User:edit.html.twig', array(
            'form'        => $measurementForm->createView(),
            'userform'    => $userForm->createView(),
            'measurement' => $measurement,
            'entity'      => $entity,
            'password_form' => $password_form->createView(),
        ));
    }

    //-----------------------------Delete User-----------------------------------------------------------
    public function deleteAction($id)
    {
        try {
            $message_array = $this->get('user.helper.user')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_users'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Size cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

//----------------------User profile update-------------------------------------------------------------
    public function updateUserProfileAction(Request $request, $id)
    {
        $entity          = $this->get('user.helper.user')->find($id);
        $measurement     = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        $userForm        = $this->createForm(new UserProfileSettingsType(), $entity);
        $userForm->bind($this->getRequest());

        $password_form   = $this->password_update_form($entity);
        $this->get('user.helper.user')->saveUser($entity);
        $this->get('session')->setFlash('success', 'Updated Successfuly');
        return $this->render('LoveThatFitAdminBundle:User:edit.html.twig', array(
            'form'        => $measurementForm->createView(),
            'userform'    => $userForm->createView(),
            'measurement' => $measurement,
            'entity'      => $entity,
            'password_form' => $password_form->createView(),
        ));
    }

    //-------------------------------------Compare User Form--------------------------------------------
    public function comapareUserAction()
    {
        $form = $this->createForm(new MannequinTestType());
        return $this->render('LoveThatFitAdminBundle:User:compare.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    //---------------------Compare User Mannequin-------------------------------------------------------

    public function comapareUserSizeAction(Request $request)
    {
        $form  = $this->createForm(new MannequinTestType());
        $data  = $request->request->all();
        $email = $data['user']['User'];
        if (strlen($email) == 0) {
            $form = $this->createForm(new MannequinTestType());
            return $this->render('LoveThatFitAdminBundle:User:compare.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        $entity        = $this->get('user.helper.user')->find($email);
        $manequin_size = $this->get('admin.helper.user.mannequin')->userMannequin($entity);
        return new Response(json_encode($manequin_size));
        return $this->render('LoveThatFitAdminBundle:User:compare.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    #######################################################################################
    #######################################################################################
    #######################################################################################

    //-----------------------Add New Retailer Site user---------------------------------------------------
    public function newRetailerSiteUserAction($id)
    {
        $entity               = $this->get('user.helper.user')->find($id);
        $RetailerSiteUser     = $this->get('admin.helper.retailer.site.user')->createNew();
        $RetailerSiteUserForm = $this->createForm(new RetailerSiteUserType('add'), $RetailerSiteUser);
        return $this->render('LoveThatFitAdminBundle:User:new_retailer_site_user.html.twig', array(
            'user' => $entity,
            'form' => $RetailerSiteUserForm->createView(),
        ));

    }

    //-----------------------Create New Retailer Site user---------------------------------------------------
    public function createRetailerSiteUserAction(Request $request, $id)
    {
        $data              = $request->request->all();
        $retailerId        = $data['retailer_site_user']['Retailer'];
        $user_reference_id = $data['retailer_site_user']['user_reference_id'];
        $retailer          = $this->get('admin.helper.retailer')->find($retailerId);
        $user              = $this->get('user.helper.user')->find($id);
        $this->get('admin.helper.retailer.site.user')->addNew($user, $user_reference_id, $retailer);
        return $this->redirect($this->generateUrl('admin_user_detail_show', array('id' => $user->getId())));
    }

    //-------------------------------Edit Retailer Site Users--------------------------------------------
    public function editRetailerSiteUserAction($user_id, $id)
    {
        $entity = $this->get('admin.helper.retailer.site.user')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retailer Site User.');
        }
        $user = $this->get('user.helper.user')->find($user_id);
        $form = $this->createForm(new RetailerSiteUserType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:User:edit_retailer_site_user.html.twig', array(
            'user'   => $user,
            'form'   => $form->createView(),
            'entity' => $entity,
        ));
    }

    //-------------------------------Update Retailer Site Users--------------------------------------------
    public function updateRetailerSiteUserAction(Request $request, $user_id, $id)
    {
        $data              = $request->request->all();
        $retailerId        = $data['retailer_site_user']['Retailer'];
        $user_reference_id = $data['retailer_site_user']['user_reference_id'];
        $retailer          = $this->get('admin.helper.retailer')->find($retailerId);
        $entity            = $this->get('user.helper.user')->find($user_id);
        $retailerSiteUser  = $this->get('admin.helper.retailer.site.user')->find($id);
        $this->get('admin.helper.retailer.site.user')->update($retailerSiteUser, $retailer, $entity, $user_reference_id);
        return $this->redirect($this->generateUrl('admin_user_detail_show', array('id' => $entity->getId())));
    }

    //----------------------------Delete Retailer Site Users---------------------------------------------
    public function deleteRetailerSiteUserAction($id)
    {
        try {
            $message_array = $this->get('admin.helper.retailer.site.user')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_users'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Site user cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //download file action
    public function downloadAccessLogsAction($id)
    {
        //$path = $this->get('kernel')->getRootDir(). "/../web/uploads/ltf/users/".$id."/";
        $path     = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/uploads/ltf/users/' . $id . "/";
        $filename = 'logs.txt';
        $content  = file_get_contents($path . $filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename);

        $response->setContent($content);
        return $response;
    }

    //-------------------------------User Search Form-----------------------------------------------------------

    private function userSearchFrom()
    {
        $user   = new User();
        $gender = array('' => 'Select Gender', 'm' => 'Male', 'f' => 'Female');
        $age    = array('' => 'Select Age', '15' => 15, '16' => 16, '17' => 17, '18' => 18, '19' => 19, '20' => 20, '21' => 21, '22' => 22, '23' => 23, '24' => 24, '25' => 25, '26' => 26, '27' => 27, '28' => 28, '29' => 29, '30' => 30, '31' => 31, '32' => 32, '33' => 33, '34' => 34, '35' => 35, '36' => 36, '37' => 37, '38' => 38, '39' => 39, '40' => 40, '41' => 41, '42' => 42, '43' => 43, '44' => 44, '45' => 45, '46' => 46, '47' => 47, '48' => 48, '49' => 49, '50' => 50);
        return $this->createFormBuilder($user)
            ->add('firstname', 'text', array('required' => false))
            ->add('gender', 'choice', array('choices' => $gender,
                'multiple'                                => false,
                'expanded'                                => false,
                'required'                                => false,
            ))
            ->add('age', 'choice', array('choices' => $age,
                'multiple'                             => false,
                'expanded'                             => false,
                'required'                             => false,
            ))
            ->getForm();
    }

    //-------------------------Selfieshare -------------------------------------------------------
    public function selfieshareListAction($id)
    {
        $user = $this->get('user.helper.user')->find($id);
        return $this->render('LoveThatFitAdminBundle:User:selfieshare_list.html.twig', array(
            'user' => $user));
    }

    public function exportAction(Request $request)
    {
        $decoded    = $request->request->all();
        $start_date = $decoded['from'];
        $end_date   = $decoded['to'];
        $type       = $decoded['type'];
         
        if (isset($type) && $type == "single") {
            $users = $this->get('user.helper.user')->findUserListExport($start_date, $end_date);
            if (!empty($users)) {
                header('Content-Type: application/csv');
                //header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachement; filename="users.csv";');
                $output = fopen('php://output', 'w');
                fputcsv($output, array(
                    'UserID', 'Name', 'Email', 'Phone No', 'Gender', 'Zip Code', 'Created At',
                    'Status', 'Weight', 'Height','Waist', 'Hip', 
                    'High Hip', 'Low Hip', 'Bust', 'Chest', 'Arm', 'Inseam', 'Outseam',
                    'Shoulder Height', 'Shoulder Across Front', 'Shoulder Across Back',
                    'Sleeve', 'Neck', 'Thigh', 'High Thigh', 'Low Thigh',
                    'Bicep', 'Tricep', 'Wrist', 'Center Front Waist', 'Back Waist', 'Abdomen',
                    'Waist Hip', 'Knee', 'Calf', 'Ankle', 'Body Shape', 'Body Types', 'Bra size', 
                    'Iphone Outseam', 'Iphone Shoulder Height', 'Iphone Foot Height',
                    'Device Name', 'Device Type', 'Device User Per Inch Pixel Height',
                    'Log Count', 'Photo Uploaded', 'Photo Marked', 'Size Entered',
                    'Measurements Entered', 'Friend Name', 'Friend Email'
                    )
                );
                $csv = array();
                foreach ($users as $user) {
                    $csv['id']                           = $user["id"];
                    $csv['user_name']                    = ($user["firstName"] . " " . $user["lastName"]);
                    $csv['email']                        = $user["email"];
                    $csv['phoneNumber']                  = $user["phoneNumber"];
                    $csv['gender']                       = ($user["gender"] == "f" ? "Female" : "Male");
                    $csv['zipcode']                      = $user["zipcode"];
                    $csv['created_at']                   = ($user["createdAt"]->format('d/m/Y'));
                    $csv['status']                       = $this->get('user.helper.user')->getUpdatedUserArchiveTime($user["id"])['status'];
                    $csv['weight']                       = $user["weight"];
                    $csv['height']                       = $user["height"];
                    $csv['waist']                        = $user["waist"];
                    $csv['hip']                          = $user["hip"];
                    $csv['high_hip']                     = $user["high_hip"];
                    $csv['low_hip']                      = $user["low_hip"];
                    $csv['bust']                         = $user["bust"];
                    $csv['chest']                        = $user["chest"];
                    $csv['arm']                          = $user["arm"];
                    $csv['inseam']                       = $user["inseam"];
                    $csv['outseam']                      = $user["outseam"];
                    $csv['shoulder_height']              = $user["shoulder_height"];
                    $csv['shoulder_across_front']        = $user["shoulder_across_front"];
                    $csv['shoulder_across_back']         = $user["shoulder_across_back"];
                    $csv['sleeve']                       = $user["sleeve"];
                    $csv['neck']                         = $user["neck"];
                    $csv['thigh']                        = $user["thigh"];
                    $csv['high_thigh']                   = $user["high_thigh"];
                    $csv['low_thigh']                    = $user["low_thigh"];
                    $csv['bicep']                        = $user["bicep"];
                    $csv['tricep']                       = $user["tricep"];
                    $csv['wrist']                        = $user["wrist"];
                    $csv['center_front_waist']           = $user["center_front_waist"];
                    $csv['back_waist']                   = $user["back_waist"];
                    $csv['abdomen']                      = $user["abdomen"];
                    $csv['waist_hip']                    = $user["waist_hip"];
                    $csv['knee']                         = $user["knee"];
                    $csv['calf']                         = $user["calf"];
                    $csv['ankle']                        = $user["ankle"];
                    $csv['body_shape']                   = $user["body_shape"];
                    $csv['body_types']                   = $user["body_types"];
                    $csv['bra_size']                     = $user["bra_size"];
                    $csv['iphone_outseam']               = $user["iphone_outseam"];
                    $csv['iphone_shoulder_height']       = $user["iphone_shoulder_height"];
                    $csv['iphone_foot_height']           = $user["iphone_foot_height"];
                    $csv['device_name']                  = $user["device_name"];
                    $csv['deviceType']                   = $user["deviceType"];
                    $csv['deviceUserPerInchPixelHeight'] = $user["deviceUserPerInchPixelHeight"];
                    $csv['log_count']                    = $this->get('user.helper.userappaccesslog')->countUserByUserId($user["id"]);
                    $csv['photo_uploaded']               = ($user["image"] == "")?'Not Uploaded':'Uploaded';
                    $csv['marker']                       = ($user["marker_id"] == "")? "Not":"Yes";
                    
                    $csv['size_entered']                 = $this->get('user.helper.user')->sizeExport($user["gender"], $user['top_brand'], $user['bottom_brand'], $user['dress_brand']);
                    
                    $csv['measurements_entered']         = $this->get('user.helper.user')->accuracyExport(
                        $user['weight'], $user['height'], $user['bust_height'], $user['thigh'], 
                        $user['body_types'], $user['body_shape'], $user['inseam'], $user['outseam'], 
                        $user['neck'], $user['sleeve'], $user['chest'], $user['shoulder_across_front'], 
                        $user['shoulder_across_back'], $user['bicep'], $user['tricep'], $user['wrist'], 
                        $user['center_front_waist'], $user['back_waist'], $user['waist_hip'],  
                        $user['knee'], $user['calf'], $user['ankle'], $user['bra_size']);

                    $csv['friend_name']                  = $user["friend_name"];
                    $csv['friend_email']                 = $user["friend_email"];

                    fputcsv($output, $csv);
                }
                # Close the stream off
                fclose($output);
                return new Response('');
            } else {
                $this->get('session')->setFlash('warning', 'No Record Found!');

                $totalRecords = $this->get('user.helper.user')->countAllUser();
                $femaleUsers  = $this->get('user.helper.user')->countUsersByGender('f');
                $maleUsers    = $this->get('user.helper.user')->countUsersByGender('m');

                return $this->render('LoveThatFitAdminBundle:User:index_new.html.twig',
                    array(
                        'rec_count'   => $totalRecords,
                        'femaleUsers' => $femaleUsers,
                        'maleUsers'   => $maleUsers,
                    )
                );
            }

        } elseif (isset($type) && $type == "brands") {
            $users = $this->get('user.helper.user')->findUserListExportBrands($start_date, $end_date);
            if (!empty($users)) {
                header('Content-Type: application/csv');
                //header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachement; filename="users_brands.csv";');
                $output = fopen('php://output', 'w');
                fputcsv($output, array(
                    'UserID', 'Name', 'Email', 'Phone No', 'Gender', 'Zip Code', 
                    'Created At','Brands Tried', 'Brands'
                    )
                );
                foreach ($users as $user) {
                    $csv['id']           = $user["id"];
                    $csv['user_name']    = ($user["firstName"] . " " . $user["lastName"]);
                    $csv['email']        = $user["email"];
                    $csv['phoneNumber']  = $user["phoneNumber"];
                    $csv['gender']       = ($user["gender"] == "f" ? "Female" : "Male");
                    $csv['zipcode']      = $user["zipcode"];
                    $csv['created_at']   = ($user["createdAt"]->format('d/m/Y'));
                    $csv['brands_tried'] = $this->get('site.helper.usertryitemhistory')->brandsTiredExportCount($user["id"]);
                    $csv['brands']       = $this->get('site.helper.usertryitemhistory')->brandsTiredExport($user["id"]);
                    fputcsv($output, $csv);
                }
                # Close the stream off
                fclose($output);
                return new Response('');
            } else {
                $this->get('session')->setFlash('warning', 'No Record Found!');

                $totalRecords = $this->get('user.helper.user')->countAllUser();
                $femaleUsers  = $this->get('user.helper.user')->countUsersByGender('f');
                $maleUsers    = $this->get('user.helper.user')->countUsersByGender('m');

                return $this->render('LoveThatFitAdminBundle:User:index_new.html.twig',
                    array(
                        'rec_count'   => $totalRecords,
                        'femaleUsers' => $femaleUsers,
                        'maleUsers'   => $maleUsers,
                    )
                );
            }
        } elseif (isset($type) && $type == "products") {
            $users = $this->get('user.helper.user')->findUserListExportProducts($start_date, $end_date);
            if (!empty($users)) {
                header('Content-Type: application/csv');
                //header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachement; filename="users_products.csv";');
                $output = fopen('php://output', 'w');
                fputcsv($output, array(
                    'UserID', 'Name', 'Email', 'Phone No', 'Gender', 'Zip Code', 
                    'Created At','Products Tried', 'Products'
                    )
                );
                foreach ($users as $user) {
                    $csv['id']             = $user["id"];
                    $csv['user_name']      = ($user["firstName"] . " " . $user["lastName"]);
                    $csv['email']          = $user["email"];
                    $csv['phoneNumber']    = $user["phoneNumber"];
                    $csv['gender']         = ($user["gender"] == "f" ? "Female" : "Male");
                    $csv['zipcode']        = $user["zipcode"];
                    $csv['created_at']     = ($user["createdAt"]->format('d/m/Y'));
                    $csv['products_tried'] = $this->get('site.helper.usertryitemhistory')->productsTiredExportCount($user["id"]);
                    $csv['products']       = $this->get('site.helper.usertryitemhistory')->productsTiredExport($user["id"]);
                    
                    fputcsv($output, $csv);
                }
                # Close the stream off
                fclose($output);
                return new Response('');
            } else {
                $this->get('session')->setFlash('warning', 'No Record Found!');

                $totalRecords = $this->get('user.helper.user')->countAllUser();
                $femaleUsers  = $this->get('user.helper.user')->countUsersByGender('f');
                $maleUsers    = $this->get('user.helper.user')->countUsersByGender('m');

                return $this->render('LoveThatFitAdminBundle:User:index_new.html.twig',
                    array(
                        'rec_count'   => $totalRecords,
                        'femaleUsers' => $femaleUsers,
                        'maleUsers'   => $maleUsers,
                    )
                );
            }

        } elseif (isset($type) && $type == "friends_invited") {
            $users = $this->get('user.helper.user')->findUserListExportFInvited($start_date, $end_date);
            
            if (!empty($users)) {
                header('Content-Type: application/csv');
                //header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachement; filename="users_invited.csv";');
                $output = fopen('php://output', 'w');
                fputcsv($output, array('UserID', 'Name', 'Email', 
                    'Phone No', 'Gender', 'Zip Code', 'Created At'
                    )
                );
                foreach ($users as $user) {
                    $csv['id']             = $user["id"];
                    $csv['user_name']      = ($user["firstName"] . " " . $user["lastName"]);
                    $csv['email']          = $user["email"];
                    $csv['phoneNumber']    = $user["phoneNumber"];
                    $csv['gender']         = ($user["gender"] == "f" ? "Female" : "Male");
                    $csv['zipcode']        = $user["zipcode"];
                    $csv['created_at']     = ($user["createdAt"]->format('d/m/Y'));
                    
                    fputcsv($output, $csv);
                }
                # Close the stream off
                fclose($output);
                return new Response('');
            } else {
                $this->get('session')->setFlash('warning', 'No Record Found!');

                $totalRecords = $this->get('user.helper.user')->countAllUser();
                $femaleUsers  = $this->get('user.helper.user')->countUsersByGender('f');
                $maleUsers    = $this->get('user.helper.user')->countUsersByGender('m');

                return $this->render('LoveThatFitAdminBundle:User:index_new.html.twig',
                    array(
                        'rec_count'   => $totalRecords,
                        'femaleUsers' => $femaleUsers,
                        'maleUsers'   => $maleUsers,
                    )
                );
            }

        } elseif (isset($type) && $type == "reffered_member") {
            $users = $this->get('user.helper.user')->findUserListExporRMember($start_date, $end_date);
            // ==Invite Friends List - Email
            if (!empty($users)) {
                header('Content-Type: application/csv');
                //header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachement; filename="users_referred.csv";');
                $output = fopen('php://output', 'w');
                fputcsv($output, array('UserID', 'Name', 'Email', 
                    'Phone No', 'Gender', 'Zip Code', 'Created At', 
                    'Referred Name', 'Referred Email'
                    )
                );
                foreach ($users as $user) {
                    $csv['id']           = $user["id"];
                    $csv['user_name']    = ($user["firstName"] . " " . $user["lastName"]);
                    $csv['email']        = $user["email"];
                    $csv['phoneNumber']  = $user["phoneNumber"];
                    $csv['gender']       = ($user["gender"] == "f" ? "Female" : "Male");
                    $csv['zipcode']      = $user["zipcode"];
                    $csv['created_at']   = ($user["createdAt"]->format('d/m/Y'));
                    $csv['friend_name']  = $user["friend_name"];
                    $csv['friend_email'] = $user["friend_email"];
                    
                    fputcsv($output, $csv);
                }
                # Close the stream off
                fclose($output);
                return new Response('');
            } else {
                $this->get('session')->setFlash('warning', 'No Record Found!');

                $totalRecords = $this->get('user.helper.user')->countAllUser();
                $femaleUsers  = $this->get('user.helper.user')->countUsersByGender('f');
                $maleUsers    = $this->get('user.helper.user')->countUsersByGender('m');

                return $this->render('LoveThatFitAdminBundle:User:index_new.html.twig',
                    array(
                        'rec_count'   => $totalRecords,
                        'femaleUsers' => $femaleUsers,
                        'maleUsers'   => $maleUsers,
                    )
                );
            }
        }
    }

}