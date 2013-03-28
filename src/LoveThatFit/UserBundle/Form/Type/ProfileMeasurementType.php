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
        $builder->add('weight');
        $builder->add('bust');
        $builder->add('hip');        
        $builder->add('waist');
        $builder->add('inseam');
        $builder->add('leg');        
        $builder->add('back');
        $builder->add('arm');
        
    }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('profileMeasurement'),
        );
    }

    
    public function getName()
    {
        return 'measurement';
    }
}
?>
