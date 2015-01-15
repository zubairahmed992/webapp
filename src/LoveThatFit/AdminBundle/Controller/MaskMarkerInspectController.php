<?php

namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;


class MaskMarkerInspectController extends Controller {

    public function indexAction(){
        $form=$this->createForm(new \LoveThatFit\UserBundle\Form\Type\UserDropdownType());
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:index.html.twig', array(
                    'form' => $form->createView(),                    
                ));
    }   
    
    public function userAction($id, $mode=null){
        $user = $this->get('user.helper.user')->find($id);
        $mm_specs=$this->getMaskedMarkerSpecs();
        $ub_specs=$user->getMeasurement()->getArray();
        $user_mm_comparison = $this->get('user.marker.helper')->getComparisionArray($user, $mm_specs);
        
        if ($mode && $mode=='json'){
            return new Response(json_encode($user_mm_comparison));
        }
                
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:_summary.html.twig', array(
                    'user' => $user,                    
                    'specs'=>$mm_specs,
                    'body_measurement'=>$ub_specs,
                    'specs_comparison'=>$user_mm_comparison,
                ));
    }  
    
     public function pathAxisArrayAction($id){
        $user = $this->get('user.helper.user')->find($id);
        $mm_specs=$this->getMaskedMarkerSpecs();        
        $mm_cordinates = $this->get('user.marker.helper')->getAxisArray($user, $mm_specs);
        return new Response(json_encode($mm_cordinates));
        
    }  
    
    private function getMaskedMarkerSpecs() {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/mask_marker.yml'));
    }
     
    public function simAction($id=6){
        $user = $this->get('user.helper.user')->find($id);
        $measurement = $user->getMeasurement();
        $marker = $this->get('user.marker.helper')->getByUser($user);
                        
        return $this->render('LoveThatFitAdminBundle:MaskMarkerInspect:_masked_marker_sim.html.twig', array(
                    'entity' => $user,
                    'measurement' => $measurement,                    
                    'marker' => $marker,
                ));
    }   
}
