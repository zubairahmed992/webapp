<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationMeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('height', 'hidden');
        $builder->add('back', 'hidden');
        $builder->add('bust', 'hidden');
        $builder->add('waist', 'hidden');
        $builder->add('hip', 'hidden');        
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
