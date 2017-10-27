<?php

namespace LoveThatFit\OneloginSamlBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface, ContainerAwareInterface
{
    private $container;
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $request = $event->getRequest();
        $this->onAuthenticationSuccess($request, $token);
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        try{
            $assignRole = $this->findRoleAdminInArray($token->getAttribute('RoleInfo'));
            $user->setRoles(
                array($assignRole)
            );

            switch ($assignRole){
                case 'ROLE_ADMIN':
                    $url = "/admin/dashboard";
                    break;
                case 'ROLE_SUPPORT':
                    $url = "/support/dashboard";
                    break;
            }

            return new RedirectResponse($url);
        }catch (\Exception $e){}
    }

    public function findRoleAdminInArray( array $userRoles)
    {
        foreach ($userRoles as $role){
            if($role == 'ROLE_ADMIN')
                return 'ROLE_ADMIN';
            elseif ($role == 'ROLE_SUPPORT')
                return 'ROLE_SUPPORT';
        }

        return 'ROLE_USER';
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
