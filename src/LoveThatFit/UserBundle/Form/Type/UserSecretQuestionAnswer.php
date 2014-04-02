<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserSecretQuestionAnswer extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('secretQuestion', 'text');
        $builder->add('secretAnswer', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('profile_settings')
        ));
    }

    public function getName() {
        return 'user';
    }

}

?>
