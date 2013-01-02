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
        $builder->add('email', 'text');
        
         $builder->add('gender', new GenderType(), array(
               
             'multiple'  => false, 
            'expanded'  => true));
        
        $builder->add('birthdate','date', array(
            'years'=> range(date('Y')-14,date('Y')-60),  
            'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'),
            'format' => 'yyyy MM dd',
            )
                );
    }

    public function getName()
    {
        return 'user';
    }
}
?>
