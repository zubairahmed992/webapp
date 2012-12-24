<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationStepThreeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('weight', 'text');
        $builder->add('height', 'text');
        $builder->add('waist', 'text');
        $builder->add('hip', 'text');
        $builder->add('bust', 'text');
        $builder->add('arm', 'text');
        $builder->add('leg', 'text');
        $builder->add('inseam', 'text');
        $builder->add('back', 'text');
    }

    public function getName() {
        return 'measurement';
    }

}

?>
