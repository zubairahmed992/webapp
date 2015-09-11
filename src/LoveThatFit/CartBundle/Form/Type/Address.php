<?php

namespace LoveThatFit\CartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class Address extends AbstractType {

  	private $address_info;
  	private $user;
    public function __construct($address_info,$user) {
        $this->address_info = $address_info;
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('first_name', 'text', array(
		  'data'	=> isset($this->address_info["first_name"])?$this->address_info["first_name"]:$this->user->getFirstName()));
        $builder->add('last_name', 'text', array(
		  'data'	=> isset($this->address_info["last_name"])?$this->address_info["last_name"]:$this->user->getLastName()));
        $builder->add('address1', 'text', array(
		  'data'	=> isset($this->address_info["address1"])?$this->address_info["address1"]:''));
		$builder->add('address2', 'text', array(
		  'required' => false,
		  'data'	=> isset($this->address_info["address2"])?$this->address_info["address2"]:''));
	  $builder->add('phone', 'text', array(
		'data'	=> isset($this->address_info["phone"])?$this->address_info["phone"]:''));
		$builder->add('city', 'text', array(
		  'data'	=> isset($this->address_info["city"])?$this->address_info["city"]:''));
		$builder->add('postcode', 'text', array(
		  'data'	=> isset($this->address_info["postcode"])?$this->address_info["postcode"]:''));
		$builder->add('country', new CountryType(), array(
		  'multiple' => false,
		  'expanded' => false,
		  'data'	=> isset($this->address_info["country"])?$this->address_info["country"]:''));
		$builder->add('state', new StateType(), array(
		  'multiple' => false,
		  'expanded' => false,
		  'data'	=> isset($this->address_info["state"])?$this->address_info["state"]:''));



		
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\CartBundle\Entity\UserAddresses',
                'cascade_validation' => true
            );
        
    }

    public function getName() {
        return '';
    }

}

?>
