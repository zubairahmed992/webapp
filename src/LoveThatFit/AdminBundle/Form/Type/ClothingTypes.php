<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ClothingTypes extends AbstractType {
    private $entity;

    public function __construct($mode,$entity) {
        $this->mode = $mode;
        $this->target = $entity->getTarget();

    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text');
        $builder->add('target', 'choice', array(
            'data'	=> $this->target,
            'choices'=> array('Top'=>'Top','Bottom'=>'Bottom', 'Dress'=>'Dress'),
            'multiple' => false,
            'expanded' => false
            ));
        $builder->add('file',null,array('required'=>false));
        $builder->add('gender', new GenderType(), array('multiple' => false,'expanded' => true));
        $builder->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false));
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\ClothingType',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }
    public function getName() {
        return 'ClothingType';
    }

}

?>
