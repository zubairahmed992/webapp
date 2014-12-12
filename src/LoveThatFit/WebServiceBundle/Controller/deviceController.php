<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class DeviceController extends Controller {

      public function ConstantsAction() {
        #$data=$this->get('admin.helper.size')->getByGender();
        $data = $this->get('admin.helper.size')->getDefaultArray();
        $data['fittingStatus'] = $this->get('webservice.helper.product')->getFittingStatus();
        $data['resolution_scale'] = $this->get('admin.helper.utility')->getDeviceResolutionSpecs();
        return new response(json_encode(($data)));
        }
    
}

// End of Class