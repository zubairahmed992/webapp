<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationMeasurementMaleType extends AbstractType
{
private $top_brands;
private $bottom_brands;
private $body_types;
private $container;
private $neck;
private $sleeve;
private $waist;
private $inseam;
private $brandHelper;
     public function __construct($container,$neck,$sleeve,$waist,$inseam,$brandHelper)             
    {
        $this->container= $container;
        $this->body_types=array('Regular'=>'Regular','Petite'=>'Petite'); 
        $this->neck=$neck;
        $this->sleeve=$sleeve;
        $this->waist=$waist;
        $this->inseam=$inseam;
        $this->brandHelper=$brandHelper;
        $this->top_brands=$this->brandHelper->getTopBrandForMaleBaseOnSizeChart();
        $this->bottom_brands=$this->brandHelper->getBottomBrandForMaleBaseOnSizeChart();
       
        
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('body_types', 'choice', array('choices' => $this->body_types,'expanded' => true,'data'=>'Regular'));
        $builder->add('top_brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'choices' => $this->top_brands,
                ));
        $builder->add('bottom_brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'choices' => $this->bottom_brands,
                ));
      //  $builder->add('top_brand', 'choice', array('choices' => $this->top_brands, 'required' => false,'empty_value' => 'Brand',));
        //$builder->add('bottom_brand', 'choice', array('choices' => $this->bottom_brands, 'required' => false,'empty_value' => 'Brand',));
        
        $builder->add('top_size', 'choice', array('required' => false));
        $builder->add('bottom_size', 'choice', array('required' => false));
        $builder->add('neck', 'choice', array('choices' => $this->neck, 'required' => true,'empty_value' => 'Neck sizes',));
        $builder->add('sleeve', 'choice', array('choices' => $this->sleeve, 'required' => true,'empty_value' => 'Sleeve sizes',));
        $builder->add('waist', 'choice', array('choices' => $this->waist, 'required' => true,'empty_value' => 'Waist sizes',));
        $builder->add('inseam', 'choice', array('choices' => $this->inseam, 'required' => true,'empty_value' => 'Inseam sizes',));
        //$builder->add('inseam');
        $builder->add('weight');
        $builder->add('chest');
        $builder->add('height');
        $builder->add('outseam');
        $builder->add('shoulder_across_back');
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
