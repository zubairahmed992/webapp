<?php

namespace LoveThatFit\OneloginSamlBundle\Security\User;

use LoveThatFit\OneloginSamlBundle\Security\Authentication\Token\SamlTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface SamlUserFactoryInterface
{
    /**
     * Creates a new User object from SAML Token.
     *
     * @param SamlTokenInterface $token SAML token
     * @return UserInterface
     */
    public function createUser(SamlTokenInterface $token);
}
