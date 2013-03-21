<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', 'text');
        $builder->add('password', 'repeated', array(
            'first_name' => 'password',
            'second_name' => 'confirm',
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
        ));
        $builder->add('email', 'text');
        $builder->add('gender', new GenderType(), array(
            'multiple' => false,
            'expanded' => true));
        $builder->add('measurement', new RegistrationMeasurementType());
        
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('registration')
        ));
    }

    public function getName() {
        return 'user';
    }

}

?>
