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
        $conf= array(
            'data' => array(
            'dev'=>array('build_type'=>'dev','url'=>'dev.selfiestyler.com'),
            'beta'=>array('build_type'=>'beta','url'=>'beta.selfiestyler.com'),
            'stack'=>array('build_type'=>'stack','url'=>'stack.selfiestyler.com'),
        ),
            'count'=>3,
            'message' => 'configuration for build deployment',
            'success' => 'true',
        );
        
        
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
        
        $this->get('support.helper.supporttasklog')->saveAsNew($decoded);
        return new Response("1");
	}

    #---------------------------------- /ws/caliboration_status
    public function caliborationStatusAction(Request $request){
        $decoded = $request->request->all();
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user_email = isset($decoded["user_email"])?$decoded["user_email"]:"";
        $caliboration_status = isset($decoded["caliboration_status"])?$decoded["caliboration_status"]:"";
        if($user_email!= null && $caliboration_status!= null){
        $ss_ar['to_email'] = $user_email;
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
}

