<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RetailerSiteUserType extends AbstractType {
    public function __construct($mode) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder ->add('Retailer', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Retailer',
                    'expanded' => false,
                    'multiple' => false,
                     'required' => false,
                    'property' => 'title',
                    'empty_value' => 'Select Retailer'
                )); 
        $builder->add('user_reference_id', 'text');
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\RetailerSiteUser',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }

    public function getName() {
        return 'retailer_site_user';
    }

}

?>
