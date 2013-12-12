<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MeasurementHorizantalPositionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shoulder_width', 'hidden');
       $builder->add('bust_width', 'hidden');
       $builder->add('waist_width', 'hidden');
       $builder->add('hip_width', 'hidden');
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
