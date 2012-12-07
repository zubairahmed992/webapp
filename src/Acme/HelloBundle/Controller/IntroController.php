<?php
// src/Acme/HelloBundle/Controller/HelloController.php
namespace Acme\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IntroController extends Controller
{
    public function indexAction()
    {
        return new Response('<html><body>Got to !</body></html>');
    }
    
    
}
?>
