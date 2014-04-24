<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandSpecificationType extends AbstractType {
   private $allSizes;
   public function __construct($allSizes)             
    {
        $this->allSizes= $allSizes;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('gender', 'choice', array('choices'=> array('m'=>'Male','f'=>'Female')
                    ,'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        $builder->add('fit_type', 'choice', array('choices'=> array('regular'=>'Regular','petite'=>'Petite','tall'=>'Tall'),'expanded' => true,
                    'multiple' => true,'required'  => true,));
       
        $builder->add('size_title_type', 'choice', array('choices'=> array('letters'=>'Letters','numbers'=>'Numbers','Waist'=>'Waist'),'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        
        $builder->add('male_numbers', 'choice', array('choices'=> $this->allSizes['man_number_sizes'],'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        $builder->add(
                'male_letters', 'choice', 
                array('choices'=>$this->allSizes['man_letter_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'male_waists', 'choice', 
                array('choices'=>$this->allSizes['man_waist_sizes'],'multiple'  => true,
                       'expanded'  => true, 
                ));        
        $builder->add(
                'female_numbers', 'choice', 
                array('choices'=>$this->allSizes['woman_number_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'female_letters', 'choice', 
                array('choices'=>$this->allSizes['women_letter_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'female_waists', 'choice', 
                array('choices'=>$this->allSizes['woman_waist_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));        
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\BrandSpecification',
                'cascade_validation' => true,              
            );
        
    }

    public function getName() {
        return 'brand_specification';
    }

}

?>
