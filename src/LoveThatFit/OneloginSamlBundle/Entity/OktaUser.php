<?php

namespace LoveThatFit\OneloginSamlBundle\Entity;
use LoveThatFit\OneloginSamlBundle\Security\User\SamlUserInterface;
use Symfony\Component\Security\Core\User\Role;

/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 5/3/2017
 * Time: 9:39 PM
 */
class OktaUser implements SamlUserInterface
{
    protected $username;
    protected $email;
    protected $roles;


    public function __construct($username, array $roles)
    {
        $this->username = $username;
        $this->roles = $roles;
    }

    /**
     * Set SAML attributes in user object.
     *
     * @param array $attributes
     */
    public function setSamlAttributes(array $attributes)
    {
        $this->email = $attributes['email'][0];
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return void
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}