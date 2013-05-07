<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileMeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('height');
        $builder->add('weight', 'number', array('precision' => 2));
        $builder->add('bust', 'number', array('precision' => 2));
        $builder->add('hip', 'number', array('precision' => 2));        
        $builder->add('waist', 'number', array('precision' => 2));
        $builder->add('inseam', 'number', array('precision' => 2));
        $builder->add('back', 'number', array('precision' => 2));
        $builder->add('arm', 'number', array('precision' => 2));
        
    }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('profile_measurement'),
        );
    }


 
    
    public function getName()
    {
        return 'measurement';
    }
}
?>
