<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationMeasurementMaleType extends AbstractType
{
private $top_brands;
private $bottom_brands;


     public function __construct($top_brands, $bottom_brands)             
    {
        $this->top_brands=$top_brands;
        $this->bottom_brands=$bottom_brands;
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('top_brand', 'choice', array('choices' => $this->top_brands, 'required' => false));
        $builder->add('bottom_brand', 'choice', array('choices' => $this->bottom_brands, 'required' => false));
        
        $builder->add('top_size', 'choice', array('required' => false));
        $builder->add('bottom_size', 'choice', array('required' => false));
        

        $builder->add('weight');
        $builder->add('chest');
        $builder->add('height');
        $builder->add('waist');
        $builder->add('neck');
        $builder->add('sleeve');
        $builder->add('outseam');
         }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('registration_measurement_male'),
        );
    } 

    
    public function getName()
    {
        return 'measurement';
    }
}
?>
