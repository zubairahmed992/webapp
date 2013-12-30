<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegistrationMeasurementFemaleType extends AbstractType
{
    private $top_brands;
    private $bottom_brands;
    private $dress_brands;
    private $body_types;
    private $container;
    private $body_shape;    
    private $bra_numbers;
    private $bra_letters;   

     public function __construct($container,$body_shape,$bra_letters,$bra_numbers,$body_types)             
    {
        $this->container= $container;
        $this->body_types=$body_types;
        
        $this->top_brands=$this->container->getBrandArray('Top');
        $this->bottom_brands=$this->container->getBrandArray('Bottom');
        $this->dress_brands=$this->container->getBrandArray('Dress');
        $this->body_shape=$body_shape;        
        $this->bra_letters=$bra_letters;  
        $this->bra_numbers=$bra_numbers;  
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   $builder->add('body_shape', 'choice', array('choices' => $this->body_shape, 'required' => false,'empty_value' => 'Body Shape',));      
        $builder->add('bra_letters', 'choice', array('choices' => $this->bra_letters, 'required' => false,'empty_value' => 'cup',));
        $builder->add('bra_numbers', 'choice', array('choices' => $this->bra_numbers, 'required' => false,'empty_value' => 'size',));
        $builder->add('body_types', 'choice', array('choices' => $this->body_types,'expanded' => true));
        $builder->add('top_brand', 'choice', array('choices' => $this->top_brands, 'required' => false,'empty_value' => 'Brand',));
        $builder->add('bottom_brand', 'choice', array('choices' => $this->bottom_brands, 'required' => false,'empty_value' => 'Brand',));
        $builder->add('dress_brand', 'choice', array('choices' => $this->dress_brands, 'required' => false,'empty_value' => 'Brand',));

        $builder->add('top_size', 'choice', array('required' => false));
        $builder->add('bottom_size', 'choice', array('required' => false));
        $builder->add('dress_size', 'choice', array('required' => false));
        
        
        $builder->add('weight');
        $builder->add('bust');
        $builder->add('height');
        $builder->add('waist');
        $builder->add('sleeve');
        $builder->add('outseam');
        $builder->add('hip');
        $builder->add('shoulder_across_back');
    }
  
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\UserBundle\Entity\Measurement',
            'cascade_validation' => true,
            'validation_groups' => array('registration_measurement_female'),
        );
    }

    
    public function getName()
    {
        return 'measurement';
    }
}
?>
