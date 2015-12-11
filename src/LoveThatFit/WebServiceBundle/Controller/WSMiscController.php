<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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

}

