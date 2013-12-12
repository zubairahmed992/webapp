<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MeasurementVerticalPositionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('shoulder_height', 'hidden');
       $builder->add('bust_height', 'hidden');
       $builder->add('waist_height', 'hidden');
       $builder->add('hip_height', 'hidden');
    }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('registration_step_four'),
        );
    }

    
    public function getName()
    {
        return 'measurement';
    }
}
?>
