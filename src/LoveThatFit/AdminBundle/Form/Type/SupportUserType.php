<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
class SupportUserType extends AbstractType {

    public function __construct($mode) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('user_name', 'text');
        $builder->add('email', 'text');
        if($this->mode == 'add') {
           $builder->add('password', 'password');
        }
    }

//    public function getDefaultOptions(array $options) {
//
//            return array(
//                'data_class' => 'LoveThatFit\AdminBundle\Entity\SupportAdminUser',
//                'cascade_validation' => true,
//                'validation_groups' => array($this->mode)
//            );
//
//    }
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('registration_step_one')
        ));
    }

    public function getName() {
        return 'support_user';
    }

}

?>
