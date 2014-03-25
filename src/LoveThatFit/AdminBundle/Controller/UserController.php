<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Form\Type\UserMeasurementType;
use LoveThatFit\AdminBundle\Form\Type\ProductItemType;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;

class UserController extends Controller {

    //------------------------------------------------------------------------------------------

    public function tossAction($id) {


        $entity_item = $this->get('admin.helper.productitem')->find($id);
        if (!$entity_item) {
            throw $this->createNotFoundException('Unable to find Product Item.');
        }
        $str = var_dump($entity_item->getImagePaths());
        return new Response($str);
    }

    public function indexAction($page_number, $sort = 'id') {
        $size_with_pagination = $this->get('user.helper.user')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:User:index.html.twig', array('pagination' => $size_with_pagination, 'searchform' => $this->userSearchFrom()->createView()));
    }

    public function showAction($id) {
        $specs = $this->get('user.helper.user')->findWithSpecs($id);
        $entity = $specs['entity'];
        $user_limit = $this->get('user.helper.user')->getRecordsCountWithCurrentUserLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($user_limit[0]['id']));
        if ($page_number == 0) {
            $page_number = 1;
        }
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:User:show.html.twig', array(
                    'user' => $entity,
                    'page_number' => $page_number,
                ));
    }

    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $gender = $data['form']['gender'];
        $firstname = $data['form']['firstname'];
        $lastname = $data['form']['firstname'];
        if ($data['form']['age'] == '') {
            $age = '';
        } else {
            $age = $data['form']['age'];
            $endDate = $this->get('user.helper.user')->getUserBirthDate($age);
            $new_timestamp = strtotime('-12 months', strtotime($endDate));
            $beginDate = date("Y-m-d", $new_timestamp);
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
                        'user' => $entity,
                        'searchform' => $this->userSearchFrom()->createView(),
                    ));
        } else {
            return $this->render('LoveThatFitAdminBundle:User:search.html.twig', array(
                        'user' => $entity,
                        'searchform' => $this->userSearchFrom()->createView(),
                    ));
        }
    }

//------------------------------------------------------------------------------------------

    private function userSearchFrom() {
        $user = new User();
        $gender = array('' => 'Select Gender', 'm' => 'Male', 'f' => 'Female');
        $age = array('' => 'Select Age', '15' => 15, '16' => 16, '17' => 17, '18' => 18, '19' => 19, '20' => 20, '21' => 21, '22' => 22, '23' => 23, '24' => 24, '25' => 25, '26' => 26, '27' => 27, '28' => 28, '29' => 29, '30' => 30, '31' => 31, '32' => 32, '33' => 33, '34' => 34, '35' => 35, '36' => 36, '37' => 37, '38' => 38, '39' => 39, '40' => 40, '41' => 41, '42' => 42, '43' => 43, '44' => 44, '45' => 45, '46' => 46, '47' => 47, '48' => 48, '49' => 49, '50' => 50,);
        return $this->createFormBuilder($user)
                        ->add('firstname', 'text', array('required' => false))
                        ->add('gender', 'choice', array('choices' => $gender,
                            'multiple' => False,
                            'expanded' => False,
                            'required' => false
                        ))
                        ->add('age', 'choice', array('choices' => $age,
                            'multiple' => False,
                            'expanded' => False,
                            'required' => false
                        ))
                        ->getForm();
    }

    //------------------------------------------------------------------------------------------
    public function editAction($id) {
        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        return $this->render('LoveThatFitAdminBundle:user:edit.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                ));
    }
//------------------------------------------------------------------------------------------
    public function updateAction($id) {
        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        $measurementForm->bind($this->getRequest());
        $measurement->setUpdatedAt(new \DateTime('now'));
        $this->get('user.helper.measurement')->saveMeasurement($measurement);
        return $this->indexAction(1);
    }

}
