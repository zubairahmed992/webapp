<?php

namespace LoveThatFit\CartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BillingShippingType extends AbstractType {

  	private $billing_shipping_info;
    public function __construct($billing_shipping_info) {
	  //print_r($billing_shipping_info);die;
        $this->billing_shipping_info = $billing_shipping_info;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('billing_first_name', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_first_name"])?$this->billing_shipping_info["billing"]["billing_first_name"]:''));
        $builder->add('billing_last_name', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_last_name"])?$this->billing_shipping_info["billing"]["billing_last_name"]:''));
        $builder->add('billing_address1', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_address1"])?$this->billing_shipping_info["billing"]["billing_address1"]:''));
		$builder->add('billing_address2', 'text', array(
		  'required' => false,
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_address2"])?$this->billing_shipping_info["billing"]["billing_address2"]:''));
	  $builder->add('billing_phone', 'text', array(
		'data'	=> isset($this->billing_shipping_info["billing"]["billing_phone"])?$this->billing_shipping_info["billing"]["billing_phone"]:''));
		$builder->add('billing_city', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_city"])?$this->billing_shipping_info["billing"]["billing_city"]:''));
		$builder->add('billing_postcode', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_postcode"])?$this->billing_shipping_info["billing"]["billing_postcode"]:''));
		$builder->add('billing_country', new CountryType(), array(
		  'multiple' => false,
		  'expanded' => false,
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_country"])?$this->billing_shipping_info["billing"]["billing_country"]:''));
		$builder->add('billing_state', new StateType(), array(
		  'multiple' => false,
		  'expanded' => false,
		  'data'	=> isset($this->billing_shipping_info["billing"]["billing_state"])?$this->billing_shipping_info["billing"]["billing_state"]:''));

		$builder->add('shipping_first_name', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["shipping_first_name"])?$this->billing_shipping_info["billing"]["shipping_first_name"]:''));
		$builder->add('shipping_last_name', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["shipping_last_name"])?$this->billing_shipping_info["billing"]["shipping_last_name"]:''));
		//$builder->add('shipping_company','text');
		$builder->add('shipping_address1', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["shipping_address1"])?$this->billing_shipping_info["billing"]["shipping_address1"]:''));
		$builder->add('shipping_address2', 'text', array(
		  'required' => false,
		'data'	=> isset($this->billing_shipping_info["billing"]["shipping_address2"])?$this->billing_shipping_info["billing"]["shipping_address2"]:''));

	  $builder->add('shipping_phone', 'text', array(
		'data'	=> isset($this->billing_shipping_info["billing"]["shipping_phone"])?$this->billing_shipping_info["billing"]["shipping_phone"]:''));

		$builder->add('shipping_city', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["shipping_city"])?$this->billing_shipping_info["billing"]["shipping_city"]:''));
		$builder->add('shipping_postcode', 'text', array(
		  'data'	=> isset($this->billing_shipping_info["billing"]["shipping_postcode"])?$this->billing_shipping_info["billing"]["shipping_postcode"]:''));
		$builder->add('shipping_country', new CountryType(), array(
		  'multiple' => false,
		  'expanded' => false,
		  'data'	=> isset($this->billing_shipping_info["billing"]["shipping_country"])?$this->billing_shipping_info["billing"]["shipping_country"]:''));

		$builder->add('shipping_state', new StateType(), array(
		  'multiple' => false,
		  'expanded' => false,
		  'data'	=> isset($this->billing_shipping_info["billing"]["shipping_state"])?$this->billing_shipping_info["billing"]["shipping_state"]:''));

		
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\CartBundle\Entity\UserOrder',
                'cascade_validation' => true
            );
        
    }

    public function getName() {
        return 'billing';
    }

}

?>
