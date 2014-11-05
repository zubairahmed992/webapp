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
    private $brandHelper;
    private $timespent;
     public function __construct($container,$sizes,$brandHelper)             
    {
        $this->container= $container;
        $this->brandHelper=$brandHelper;
        $this->body_types=$sizes['fit_types']['woman'];//$body_types;
        
        $this->top_brands=$this->brandHelper->getTopBrandForFemaleBaseOnSizeChart();
        $this->bottom_brands=$this->brandHelper->getBottomBrandForFemaleBaseOnSizeChart();
        $this->dress_brands=$this->brandHelper->getDressBrandForFemaleBaseOnSizeChart();
        $this->body_shape=$sizes['body_shapes']['woman'];        
        $this->bra_letters=$sizes['sizes']['woman']['bra_cup'];//$bra_letters;  
        $this->bra_numbers=$sizes['sizes']['woman']['bra_band'];//$bra_numbers;  
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   $builder->add('body_shape', 'choice', array('choices' => $this->body_shape, 'required' => false,'empty_value' => 'Body Shape',));      
        $builder->add('bra_letters', 'choice', array('choices' => $this->bra_letters, 'required' => false,'empty_value' => 'cup',));
        $builder->add('bra_numbers', 'choice', array('choices' => $this->bra_numbers, 'required' => false,'empty_value' => 'size',));
        $builder->add('body_types', 'choice', array('choices' => $this->body_types,'expanded' => false,'empty_value' => 'Select'));
        //$builder->add('top_brand', 'choice', array('choices' => $this->top_brands, 'required' => false,'empty_value' => 'Brand',));
        $builder->add('timespent', 'hidden');
        
         $builder->add('top_brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'choices' => $this->top_brands,
                    'empty_value' => 'Brand',
                    'required' => false,
                ));
         $builder->add('bottom_brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'choices' => $this->bottom_brands,
                    'empty_value' => 'Brand',
                    'required' => false,
                )); 
        //$builder->add('bottom_brand', 'choice', array('choices' => $this->bottom_brands, 'required' => false,'empty_value' => 'Brand',));
        
          $builder->add('dress_brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'choices' => $this->dress_brands,
                    'empty_value' => 'Brand',
                     'required' => false,
                ));
         //$builder->add('dress_brand', 'choice', array('choices' => $this->dress_brands, 'required' => false,'empty_value' => 'Brand',));

        $builder->add('top_size', 'choice', array('required' => false));
        $builder->add('bottom_size', 'choice', array('required' => false));
        $builder->add('dress_size', 'choice', array('required' => false));
        
        
        $builder->add('weight');
        $builder->add('bust');
        $builder->add('height');
        $builder->add('waist');
        $builder->add('sleeve');       
        $builder->add('hip');
        $builder->add('shoulder_across_back');
        $builder->add('birthdate','date', array(
            'years'=> range(date('Y')-8,date('Y')-110),  
            'empty_value' => array('year' => 'YY', 'month' => 'MM', 'day' => 'DD'),
            'format' => 'yyyy MM dd',
             'required' => false,
            )
                );
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
