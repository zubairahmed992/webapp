<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class WSMiscController extends Controller {
    
    private function process_request(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;
    }
#~~~~~~~~~~~~~~~~~~~ ws_misc_faq   /ws/misc_faq

    public function faqAction() {
        $decoded = $this->process_request();
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/faq.yml'));

        if (array_key_exists('faq_type', $decoded) && $decoded['faq_type'] != null) {

            $type_faq = "";
            foreach ($conf as $k => $v) {
                if ($v['faq_type'] == $decoded['faq_type']) {
                    $type_faq[$k] = $v;
                }
            }
            return new Response(json_encode($type_faq));
        } else {
            return new Response(json_encode($conf));
        }
    }
#~~~~~~~~~~~~~~~~~~~ ws_misc_banner   /ws/misc_banner

    public function bannerAction() {
        $decoded = $this->process_request();
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/banner.yml'));
        if (array_key_exists('dashboard_banner', $conf) && $conf['dashboard_banner'] != null) {
            $dashboard_banner = "";
		  	foreach ($conf["dashboard_banner"] as $k => $v) {
			  $dashboard_banner[$k] = $v;
              if(array_key_exists('image',$v) && array_key_exists('image',$v)!=null){
				$dashboard_banner[$k]["image"] = $decoded["base_path"].$v["image"];
			  }
			}
            return new Response(json_encode($dashboard_banner));
        } else {
            return new Response(json_encode($conf));
        }
    }

    #~~~~~~~~~~~~~~~~~~~ ws_build_config   /ws/build_config

    public function buildConfigAction() {
        $decoded = $this->process_request();
        if($decoded["app_name"] == 'photo') {
            $conf = array(
                'data' => array(
                    'dev' => array('build_type' => 'dev', 'url' => 'dev.selfiestyler.com'),
                    'photoresearch' => array('build_type' => 'photoresearch', 'url' => 'photoresearch.selfiestyler.com'),
                ),
                'count' => 3,
                'message' => 'configuration for build deployment',
                'success' => 'true',
            );
        }else{
            $conf= array(
                'data' => array(
                    'dev'=>array('build_type'=>'dev','url'=>'dev.selfiestyler.com'),
                    'beta'=>array('build_type'=>'beta','url'=>'beta.selfiestyler.com'),
                    'stack'=>array('build_type'=>'stack','url'=>'stack.selfiestyler.com'),
                    'awsdev'=>array('build_type'=>'awsdev','url'=>'awsdev.selfiestyler.com'),
                    'Local Server'=>array('build_type'=>'localserver','url'=>'192.168.0.5'),
                    'QA Server'=>array('build_type'=>'qa','url'=>'qa.selfiestyler.com'),
                    'testing Server for 2.8'=>array('build_type'=>'testing for 2.8','url'=>'asif.selfiestyler.com'),
                    'devservices for 2.8'=>array('build_type'=>'devservices for 2.8','url'=>'devservices.selfiestyler.com'),
                    'qaservices for 2.8'=>array('build_type'=>'qaservices for 2.8','url'=>'qaservices.selfiestyler.com')
                ),
                'count'=>3,
                'message' => 'configuration for build deployment',
                'success' => 'true',
            );
        }
        return new Response(json_encode($conf));
    }
    
      public function response_array($success, $message = null, $json = true, $data = null) {
        $ar = array(
            'data' => $data,
            'count'=>$data?count($data):0,
            'message' => $message,
            'success' => $success,
        );
        return $json ? json_encode($ar) : $ar;
    }
    #---------------------------------- /ws/support_task_log/add
    public function logTaskAction(Request $request){
        $decoded = $request->request->all();
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $decoded['supportUsers'] = $this->get('admin.helper.support')->findOneByUserName($decoded["support_user_name"]);
        $decoded['archive'] = $this->get('user.helper.userarchives')->find($decoded["archive"]);
        $getID = $this->get('support.helper.supporttasklog')->findByAssingnedIdSupportIDMemberEmail(
                $decoded['archive'],
                $decoded['supportUsers'],
                $decoded['member_email']
            );

        if (!empty($getID)) {
            $decoded['id'] = $getID[0]['id'];
            if ($getID[0]['start_time'] == "") {
                $decoded['start_time'] = '';
            } elseif($getID[0]['start_time'] != "" && $getID[0]['start_time']->format("H:i:s") == "00:00:00") {
                $decoded['start_time'] = '';
            } else {
                $decoded['start_time'] = $getID[0]['start_time'];
            }
            $this->get('support.helper.supporttasklog')->update($decoded);
            return new Response("1");
        } else {
            
            $res= $this->get('webservice.helper')->response_array(false, "Record is unavailable.");
            return new Response($res);
        }
	}

    #---------------------------------- /ws/image_approval
    public function imageApprovalAction(Request $request){
        $decoded = $request->request->all();
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        //task log start

        $support_user_name = isset($decoded['support_user_name'])?$decoded['support_user_name']:'';
        $archive = isset($decoded['archive'])?$decoded['archive']:'';
        $duration = isset($decoded['archive'])?$decoded['duration']:'';
        if($support_user_name !='' && $archive!='' && $duration!=''){
        $decoded['supportUsers'] = $this->get('admin.helper.support')->findOneByUserName($decoded["support_user_name"]);
        $decoded['archive'] = $this->get('user.helper.userarchives')->find($decoded["archive"]);
        $getID   = $this->get('support.helper.supporttasklog')->findByAssingnedIdSupportIDMemberEmail(
            $decoded['archive'],
            $decoded['support_user_name'],
            $decoded['member_email']
        );

        if (!empty($getID)) {
            $decoded['id'] = $getID[0]['id'];
        }

        $this->get('support.helper.supporttasklog')->saveAsNew($decoded);
        }
        //task log end

        $user_email = isset($decoded["member_email"])?$decoded["member_email"]:"";
        $caliboration_status = isset($decoded["caliboration_status"])?$decoded["caliboration_status"]:"";
        if($user_email!= null && $caliboration_status!= null){
        $ss_ar['to_email'] = "membersupport@selfiestyler.com";
        $ss_ar['template'] = 'LoveThatFitAdminBundle::email/caliboration_status.html.twig';
        $ss_ar['template_array'] = array('cs' => $decoded);
        $ss_ar['subject'] = 'Caliboration Status';
        $this->get('mail_helper')->sendEmailWithTemplate($ss_ar);
        return new Response($this->get('webservice.helper')->response_array(true, 'Caliboration Email sent'));
        }else{
            $res = $this->get('webservice.helper')->response_array(false, 'Parameters must be enter correctly');
            return new Response($res);
        }

    }

    public function eventsListAction(Request $request)
    {
        $decoded = $request->request->all();
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $eventsList = $this->get('admin.helper.eventsManagement')->findAll();

        $conf= array(
            'data' => $eventsList,
            'count'=> count($eventsList),
            'message' => 'event list',
            'success' => 'true',
        );
        return new Response(json_encode($conf));
    }

    public function shopTheLookAction(Request $request)
    {
        $decoded = $request->request->all();
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $shopLook = $this->get('admin.helper.shoplook')->findAll();
        $path = "http://" . $_SERVER['HTTP_HOST'].'/uploads/ltf/shop_Look/';

        foreach($shopLook as $key => $value){
            $shopLook[$key]['id'] = (int)$shopLook[$key]['id'];
            $shopLook[$key]['sorting'] = (int)$shopLook[$key]['sorting'];
            $shopLook[$key]['shop_model_image'] = $path.$shopLook[$key]['shop_model_image'];
            $shoplook_id = $shopLook[$key]['id'];
            $product_information = $this->get('admin.helper.shoplookproduct')->getShopLookProductsById($shoplook_id);

            foreach($product_information as $product_key => $product_value){
                $product_information[$product_key]['id'] = (int)$product_information[$product_key]['id'];
                $product_information[$product_key]['shop_look_id'] = (int)$product_information[$product_key]['shop_look_id'];
                $product_information[$product_key]['sorting'] = (int)$product_information[$product_key]['sorting'];

                if(isset($product_information[$product_key]['product_id'])){
                    $product_information[$product_key]['product_id'] = (int)$product_information[$product_key]['product_id'];
                    $product_images = $this->get('webservice.helper')->productImageById($product_information[$product_key]['product_id']);
                    if (!empty($product_images)) {
                        $product_information[$product_key]['product_image'] = $product_images[0]['product_image'];
                    }
                }
            }
            //$product_images = $this->get('webservice.helper')->productImageById($shop_look_products_information[$key]['product_id']);
            $shopLook[$key]['products_id'] = $product_information;
        }

        $conf= array(
            'data' => $shopLook,
            'count'=> count($shopLook),
            'message' => 'shop the look list',
            'success' => 'true',
        );
        return new Response(json_encode($conf));
    }
}

