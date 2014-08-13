<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\UserMarker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use \DateTime;

class MaskedMarkerController extends Controller {

    public function userMarkerAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        $maskMarker=$this->get('user.marker.helper')->findMarkerByUser($user);
        if(count($maskMarker)>0){
        return new Response(json_encode($this->get('user.marker.helper')->getArray($maskMarker)));
        }else
        {
            return new response(json_encode('not exists'));
        }
    }
    
    public function getDefaultMarkerAction(){
        
       $specs['svg_path']='M248.38994,215.74298c-0.365,-10.31108 -0.333,-35.23269 -4.747,-55.85983c-0.885,-4.13407 14.227,-24.34369 12.754,-34.75534c-3.121,-22.06085 -20.018,-29.97191 -27.835,-32.31738c-3.276,-0.75384 -11.881,-8.42912 -15.39,-12.28476c-2.309,-2.53746 -20.146,0.9082 -20.248,-1.0059c2.16,-3.64536 2.555,-5.69162 3.497,-10.38338c0.711,-0.98323 1.663,2.43772 3.998,-4.98349c0.099,-1.42623 2.549,-5.71156 -0.25,-5.60684c1.033,-1.89832 8.401,-22.01181 -19.238,-22.01181c-23.091,0 -22.547,13.53672 -19.238,22.01181c-0.332,0.47956 -2.855,-1.14697 -0.25,5.60684c0.351,0.19947 1.378,5.91271 3.998,4.98349c0.919,5.60768 3.041,9.41095 3.498,10.38338c-0.226,4.0493 -0.328,8.58564 -2.499,11.0059c-3.212,3.95703 -24.689,8.22576 -25.235,8.30638c-6.486,2.61392 -14.171,0.89014 -16.567,24.13536c-0.521,5.0533 -5.412,32.60132 -6.245,36.95314c-3.959,20.69695 -4.395,45.55707 -4.761,55.86981c-0.386,10.83968 -15.46,19.33472 2.004,29.49203c6.64,3.86228 8.124,3.7318 4.762,-8.72277c0.07,-3.98281 4.86,-10.58037 2.005,-20.3537c-0.461,-0.1762 4.319,-8.86074 12.508,-42.07876c1.559,-6.32411 6.132,-51.32847 8.042,-50.99851c2.732,1.95067 2.582,5.47968 2.499,7.26828c-0.083,3.9454 1.042,12.15869 2.249,16.8197c0.912,3.52235 2.203,9.3162 1.999,12.66734c-0.699,11.48048 -2.564,12.12877 -3.248,14.12101c-1.706,4.9702 -3.806,6.47039 -4.997,15.78245c-1.016,7.93735 -2.978,31.0047 -1.499,37.17007c0.541,2.27732 5.758,34.2478 10.494,50.4616c0.499,5.95176 0.749,8.99871 1.249,14.95048c-0.082,4.9918 0.148,7.63399 -0.75,12.66734c-0.436,8.31468 -4.38,14.00132 5.375,59.24172c0.227,3.21899 0.455,12.38642 0.121,19.87579c0.165,3.9238 -1.175,6.73719 -2.867,9.46748c-4.78,7.71211 -9.375,12.80116 3.117,15.65943c17.116,2.07617 13.978,-5.55697 14.741,-15.57383c0.366,-4.80646 -1.678,-6.91423 -1.749,-9.55225c-0.365,-13.49681 3.824,-30.04723 5.497,-54.82173c0.806,-11.94094 -2.779,-23.05821 -1.999,-25.74943c4.313,-17.4256 4.629,-35.55683 5.746,-69.35748c0.043,-1.28909 -0.711,-7.32065 1.999,-7.26828c2.871,0 1.933,5.84538 1.999,7.26828c1.564,33.87794 1.189,53.25006 5.496,69.35748c1.154,4.31609 -2.659,15.11005 -1.749,25.74943c0.5,8.58231 4.425,31.72696 5.497,54.82173c0.08,1.72627 -3.045,6.32827 -1.499,9.55225c4.491,9.36441 -0.875,17.48128 14.241,15.78162c5.947,-0.66906 16.443,0.13797 2.748,-15.78162c-0.514,-2.08699 -2.616,-5.41818 -2.498,-9.55225c0.173,-6.04735 -0.337,-14.3878 0.5,-19.9348c2.994,-19.86332 8.373,-33.49146 5.246,-58.97492c-0.616,-5.02422 -0.6,-7.01895 -0.499,-13.08208c0.228,-4.29115 43.832,16.30176 43.999,13.25648c2.389,-10.15566 -33.273,-72.20564 -32.507,-78.25381c0.045,-0.61421 1.613,-14.40774 -1.249,-37.1709c-0.748,-5.95343 -4.083,-13.03803 -5.496,-15.98939c-2.321,-4.84636 -3.065,-9.3827 -2.998,-14.12101c0.031,-2.18588 0.722,-8.43437 1.998,-12.66734c0.723,-2.3945 2.248,-13.3281 2.249,-16.8197c0.012,-1.14032 -0.478,-5.47552 2.248,-7.26828c2.095,-1.37719 6.145,41.90256 8.273,50.90542c6.729,28.46392 12.767,40.81959 12.714,42.1253c-3.164,10.0368 1.929,16.36923 1.999,20.35037c-3.352,12.45291 -1.873,13.49506 4.747,9.63361c17.412,-10.15566 2.383,-19.56236 2.001,-30.40121z';
       $specs['rect_x']='32';
       $specs['rect_y']='52.5';
       $specs['rect_height']='400';
       $specs['rect_width']='300';
        return new response(json_encode(($specs)));
    }
    
    
    //--------------------------Save User Marker in database if exists then update if not then add-------------------------------
    public function saveUserMarkerAction(Request $request)
    {
        $usermaker=$request->request->all();
       
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        
        $user = $this->get('user.helper.user')->find($id);
        $maskMarker=$this->get('user.marker.helper')->findMarkerByUser($user);
      
        if(count($maskMarker)>0)
        {
            $this->get('user.marker.helper')->setArray($usermaker,$maskMarker);
            $this->get('user.marker.helper')->updateUserMarker($user,$maskMarker);
            return new response('updated');
        }else
        {             
            $this->get('user.marker.helper')->setArray($usermaker,$maskMarker);
            return $this->get('user.marker.helper')->saveUserMarker($user,$usermaker);
            return new response('added');
        }
        
    }
    
    
    
    
    
    

}

?>