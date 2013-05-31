<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileMeasurementFemaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('height','number', array('precision' => 2));
        $builder->add('neck', 'number', array('precision' => 2));
        $builder->add('sleeve', 'number', array('precision' => 2));
        $builder->add('bust', 'number', array('precision' => 2));
        $builder->add('waist', 'number', array('precision' => 2));
        $builder->add('hip', 'number', array('precision' => 2));        
        $builder->add('inseam', 'number', array('precision' => 2));
        $builder->add('outseam', 'number', array('precision' => 2));
        $builder->add('shoulder_height', 'number', array('precision' => 2));
        $builder->add('weight', 'number', array('precision' => 2));
        $builder->add('back', 'number', array('precision' => 2));
        
        
    }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('profile_measurement_female'),
        );
    }


 
    
    public function getName()
    {
        return 'measurement';
    }
}
?>
