<?php

namespace LoveThatFit\OneloginSamlBundle\Security\Authentication\Provider;

use LoveThatFit\OneloginSamlBundle\Security\Authentication\Token\SamlToken;
use LoveThatFit\OneloginSamlBundle\Security\Authentication\Token\SamlTokenFactoryInterface;
use LoveThatFit\OneloginSamlBundle\Security\Authentication\Token\SamlTokenInterface;
use LoveThatFit\OneloginSamlBundle\Security\User\SamlUserFactoryInterface;
use LoveThatFit\OneloginSamlBundle\Security\User\SamlUserInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SamlProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $userFactory;
    private $tokenFactory;
    private $entityManager;
    private $options;

    public function __construct(UserProviderInterface $userProvider, array $options = array())
    {
        $this->userProvider = $userProvider;
        $this->options = array_merge(array(
            'persist_user' => false
        ), $options);
    }

    public function setUserFactory(SamlUserFactoryInterface $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    public function setTokenFactory(SamlTokenFactoryInterface $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->retrieveUser($token);

        $assignRole = $this->findRoleAdminInArray($token->getAttribute('RoleInfo'));

        if ($user) {
            $authenticatedToken = $this->tokenFactory->createToken($user, $token->getAttributes(), array($assignRole));
            $authenticatedToken->setAuthenticated(true);

            if ($user instanceof SamlUserInterface) {
                $user->setSamlAttributes($token->getAttributes());
            }

            return $authenticatedToken;
        }

        throw new AuthenticationException('The authentication failed.');
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

    public function supports(TokenInterface $token)
    {
        return $token instanceof SamlTokenInterface;
    }

    protected function retrieveUser($token)
    {
        try {
            return $this->userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            if ($this->userFactory instanceof SamlUserFactoryInterface) {
                return $this->generateUser($token);
            }
            
            throw $e;
        }
    }

    protected function generateUser($token)
    {
        $user = $this->userFactory->createUser($token);

        if ($this->options['persist_user'] && $this->entityManager) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }
}