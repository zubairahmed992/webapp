<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AutocompleteController extends Controller {

    public function AutoCompleteUserIndexAction() {
	  return $this->render('LoveThatFitSupportBundle:AutoComplete:user_index.html.twig', array(
		'user' => '',
	  ));
    }

	public function AutoCompleteUserSearchResultAction(Request $request) {
	  $decoded  = $request->request->all();
	  $search_result_user_data = $this->get('user.helper.user')->getSearchData($decoded["term"]);
	  //echo $search_result_user_data[0]["email"];
	  //echo json_encode($search_result_user_data);
	  //print_r($search_result_user_data);die;
	  //$data = array("0" => "alpaca", "1" => "buffalo", "2" => "cat","3"=>"tiger");
	  return new Response(json_encode($search_result_user_data));
	}

  public function AutoCompleteProductIndexAction() {
	return $this->render('LoveThatFitSupportBundle:AutoComplete:product_index.html.twig', array(
	  'produt' => '',
	));
  }

	public function AutoCompleteProductSearchResultAction(Request $request) {
	  $decoded  = $request->request->all();
	  $search_result_user_data = $this->get('admin.helper.product')->getSearchData($decoded["term"]);
	  return new Response(json_encode($search_result_user_data));
	}


}
