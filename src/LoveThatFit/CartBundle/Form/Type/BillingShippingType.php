<?php

namespace LoveThatFit\CartBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BillingShippingType extends AbstractType {

    public function __construct($mode) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('billing_first_name', 'text');  
        $builder->add('billing_last_name', 'text');    
        //$builder->add('billing_company','text');
        $builder->add('billing_address1', 'text'); 
		$builder->add('billing_address2', 'text');
		$builder->add('billing_city', 'text');
		$builder->add('billing_postcode', 'text');
		$builder->add('billing_country', new CountryType(), array(
		  'multiple' => false,
		  'expanded' => false));
		$builder->add('billing_state', new StateType(), array(
		  'multiple' => false,
		  'expanded' => false));

		$builder->add('shipping_first_name', 'text');
		$builder->add('shipping_last_name', 'text');
		//$builder->add('shipping_company','text');
		$builder->add('shipping_address1', 'text');
		$builder->add('shipping_address2', 'text');
		$builder->add('shipping_city', 'text');
		$builder->add('shipping_postcode', 'text');
		$builder->add('shipping_country', new CountryType(), array(
		  'multiple' => false,
		  'expanded' => false));
		$builder->add('shipping_state', new StateType(), array(
		  'multiple' => false,
		  'expanded' => false));

		
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\CartBundle\Entity\UserOrder',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }

    public function getName() {
        return 'billing';
    }

}

?>
