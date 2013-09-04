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

     public function __construct($container)             
    {
        $this->container= $container;
        $this->body_types=array('Regular'=>'Regular','Petite'=>'Petite');
        
        $this->top_brands=$this->container->getBrandArray('Top');
        $this->bottom_brands=$this->container->getBrandArray('Bottom');
        $this->dress_brands=$this->container->getBrandArray('Dress');
     
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('body_types', 'choice', array('choices' => $this->body_types,'expanded' => true,'data'=>'Regular'));
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
        $builder->add('back');
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
