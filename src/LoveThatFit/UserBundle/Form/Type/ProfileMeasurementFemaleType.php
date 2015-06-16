<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileMeasurementFemaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('height','number', array('precision' => 2,'required'=>false));
        $builder->add('neck', 'number', array('precision' => 2,'required'=>false));
        $builder->add('sleeve', 'number', array('precision' => 2,'required'=>false));
        $builder->add('bust', 'number', array('precision' => 2,'required'=>false));
        $builder->add('waist', 'number', array('precision' => 2,'required'=>false));
        $builder->add('hip', 'number', array('precision' => 2,'required'=>false));        
        $builder->add('inseam', 'number', array('precision' => 2,'required'=>false));        
        $builder->add('outseam', 'number', array('precision' => 2,'required'=>false));
        
        $builder->add('weight', 'number', array('precision' => 2,'required'=>false));       
        $builder->add('thigh', 'number', array('precision' => 2,'required'=>false));
        $builder->add('shoulder_across_front', 'number', array('precision' => 2,'required'=>false));
        $builder->add('shoulder_across_back', 'number', array('precision' => 2,'required'=>false));
        $builder->add('bicep', 'number', array('precision' => 2,'required'=>false));
        $builder->add('tricep', 'number', array('precision' => 2,'required'=>false));
        $builder->add('wrist', 'number', array('precision' => 2,'required'=>false));
        $builder->add('centerFrontWaist', 'number', array('precision' => 2,'required'=>false));
        $builder->add('backWaist', 'number', array('precision' => 2,'required'=>false));
        $builder->add('waistHip', 'number', array('precision' => 2,'required'=>false));
        $builder->add('knee', 'number', array('precision' => 2,'required'=>false));        
        $builder->add('calf', 'number', array('precision' => 2,'required'=>false));
        $builder->add('ankle', 'number', array('precision' => 2,'required'=>false));
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
