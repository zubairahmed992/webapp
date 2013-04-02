<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationStepThreeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('weight', 'hidden');
        $builder->add('height', 'hidden');
        $builder->add('waist', 'hidden');
        $builder->add('hip', 'hidden');
        $builder->add('bust', 'hidden');
        $builder->add('arm', 'hidden');
        $builder->add('leg', 'hidden');
        $builder->add('inseam', 'hidden');
        $builder->add('back', 'hidden');
    }

    public function getName() {
        return 'measurement';
    }
     public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('profile_measurement')
        ));
    }

}

?>
