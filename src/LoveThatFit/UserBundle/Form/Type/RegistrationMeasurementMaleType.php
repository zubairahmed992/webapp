<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationMeasurementMaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('chest', 'hidden');
        $builder->add('height', 'hidden');
        $builder->add('waist', 'hidden');
        $builder->add('neck', 'hidden');
        $builder->add('sleeve', 'hidden');
        $builder->add('outseam', 'hidden');
         }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('registrationMeasurement'),
        );
    }

    
    public function getName()
    {
        return 'measurement';
    }
}
?>
