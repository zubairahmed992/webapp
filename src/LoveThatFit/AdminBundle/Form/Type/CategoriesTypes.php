<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoriesTypes extends AbstractType {
    private $entity;

    public function __construct($mode,$entity) {
        $this->mode = $mode;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text');
        $builder->add('parent_id', 'hidden',array(
            'data' => '0'
        ));

        if($this->mode=='add'){
            $builder->add('file',null,array('required'=>false));
        }else{
            $builder->add('file',null,array('required'=>false));
        }

        $builder->add('gender', new GenderType(), array('multiple' => false,'expanded' => true));
        $builder->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false));
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\Categories',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }
    public function getName() {
        return 'categories';
    }

}

?>
