<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandSpecificationType extends AbstractType {
   private $allSizes;
   private $size_helper;
   private $size_title_type;
   private $fit_type;  
   public function __construct($allSizes,$size_helper)             
    {
        $this->allSizes= $allSizes;
        $this->size_helper=$size_helper;
        $this->size_title_type=$this->size_helper->getAllSizeTitleType();
        $this->fit_type= $this->size_helper->getAllFitType();
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('gender', 'choice', array('choices'=> array('m'=>'Male','f'=>'Female')
                    ,'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        $builder->add('female_fit_type', 'choice', array('choices'=> $this->fit_type['woman'],'expanded' => true,
                    'multiple' => true,'required'  => true,));
       
        $builder->add('male_fit_type', 'choice', array('choices'=> $this->fit_type['man'],'expanded' => true,
                    'multiple' => true,'required'  => true,));
       
//$builder->add('size_title_type', 'choice', array('choices'=> array('letters'=>'Letters','numbers'=>'Numbers','Waist'=>'Waist'),'expanded' => true,
                 //  'multiple' => true,'required'  => true,));
        
     $builder->add('female_size_title_type', 'choice', array('choices'=>$this->size_title_type['woman'],'expanded' => true,
                 'multiple' => true,'required'  => true,));
        
     $builder->add('male_size_title_type', 'choice', array('choices'=>$this->size_title_type['man'],'expanded' => true,
                 'multiple' => true,'required'  => true,));
     
     $builder->add('male_chest', 'choice', array('choices'=> $this->allSizes['man_chest_sizes'],'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        $builder->add(
                'male_letter', 'choice', 
                array('choices'=>$this->allSizes['man_letter_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
       
        $builder->add(
                'male_shirt', 'choice', 
                array('choices'=>$this->allSizes['man_shirt_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'male_chest', 'choice', 
                array('choices'=>$this->allSizes['man_chest_sizes'],'multiple'  => true,
                       'expanded'  => true, 
                )); 
        $builder->add(
                'male_waist', 'choice', 
                array('choices'=>$this->allSizes['man_waist_sizes'],'multiple'  => true,
                       'expanded'  => true, 
                ));  
        $builder->add(
                'male_neck', 'choice', 
                array('choices'=>$this->allSizes['man_neck_sizes'],'multiple'  => true,
                       'expanded'  => true, 
                ));  
        $builder->add(
                'female_number', 'choice', 
                array('choices'=>$this->allSizes['woman_number_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'female_letter', 'choice', 
                array('choices'=>$this->allSizes['woman_letter_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'female_bra', 'choice', 
                array('choices'=>$this->allSizes['woman_bra_sizes'],
                       'multiple'  => true,
                       'expanded'  => true, 
                )); 
     $builder->add(
                'female_waist', 'choice', 
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
