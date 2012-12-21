<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationStepTwoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder->add('firstName', 'text');
        $builder->add('lastName', 'text');
        
        $builder->add('gender', 'choice', array(
            'choices'=> array(
                'M'=>'Male',
                'F'=>'Female',
                ), 
            'multiple'  => false, 
            'expanded'  => true,
            
            ));
        
        $builder->add('birthdate','date');
    }

    public function getName()
    {
        return 'user';
    }
}
?>
