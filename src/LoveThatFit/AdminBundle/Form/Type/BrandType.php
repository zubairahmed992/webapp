<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandType extends AbstractType {

    public function __construct($mode) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text');
        if($this->mode=='add'){
            $builder->add('file');
        }else{
            $builder->add('file',null,array('required'=>false));
        }
        
        $builder->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false));
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\Brand',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }

    public function getName() {
        return 'brand';
    }

}

?>
