<?php

namespace LoveThatFit\WebServiceBundle;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Templating\EngineInterface;

class WebServiceHelper {

  public function requestArray(){
        
       // $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        return $decoded = json_decode($jsonInput, true);
        
      
  } 
}
