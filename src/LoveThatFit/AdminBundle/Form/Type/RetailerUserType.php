<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RetailerUserType extends AbstractType {

    public function __construct($mode) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text');  
        $builder->add('email', 'text');    
        $builder->add('password','password');
        $builder->add('username', 'text'); 
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\RetailerUser',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }

    public function getName() {
        return 'retailer';
    }

}

?>
