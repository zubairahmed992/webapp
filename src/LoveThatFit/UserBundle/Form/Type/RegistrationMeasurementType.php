<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationMeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('height');
    }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('registration'),
        );
    }

    
    public function getName()
    {
        return 'measurement';
    }
}
?>
