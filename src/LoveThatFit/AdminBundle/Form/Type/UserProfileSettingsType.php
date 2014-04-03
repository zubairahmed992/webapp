<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserProfileSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder->add('firstName', 'text');
        $builder->add('lastName', 'text');        
        $builder->add('secretQuestion', 'text');
        $builder->add('secretAnswer', 'text');
        $builder->add('gender', new GenderType(), array(               
             'multiple'  => false, 
            'expanded'  => true));        
        $builder->add('birthdate','date', array(
            'years'=> range(date('Y')-14,date('Y')-112),  
            'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'),
            'format' => 'yyyy MM dd',
            )
                );
        
        
        
    }
    
  
  
      public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\User',
            'cascade_validation' => true,            
        );
    }


    public function getName()
    {
        return 'user';
    }
}
?>
