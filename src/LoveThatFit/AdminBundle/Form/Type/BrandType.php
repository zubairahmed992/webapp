<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text');
        $builder->add('file');
        $builder->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\Brand',
            'cascade_validation' => true,
            'validation_groups' => array('brand_create'),
        );
    }

    public function getName() {
        return 'brand';
    }

}

?>
