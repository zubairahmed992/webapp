<?php

namespace LoveThatFit\UserBundle\Handler;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router; 

class AuthenticationHandler implements LogoutSuccessHandlerInterface
{
    
    protected $router;
    
    
    public function __construct(Router $router)
    {
        $this->router = $router;
    
    }
    
    public function onLogoutSuccess(Request $request) 
    {
        $referer = $request->headers->get('referer');
        $login_route=$referer;
        
        if (strpos($referer,'admin') !== false) {
            $login_route=$this->router->generate('admin_login');
            
            }else{
                $login_route=$this->router->generate('login');
            }
            
        return new RedirectResponse($login_route);
    }
}