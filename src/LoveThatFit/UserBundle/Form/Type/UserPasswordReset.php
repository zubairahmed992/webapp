<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserPasswordReset extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('old_password','password');
            
        $builder->add('password', 'repeated', array(
            'first_name' => 'password','label'=>'New Password',
            'second_name' => 'confirm',
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('password_reset')
        ));
    }

    public function getName() {
        return 'user';
    }

}

?>
