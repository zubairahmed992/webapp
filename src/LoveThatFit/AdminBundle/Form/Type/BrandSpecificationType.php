<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandSpecificationType extends AbstractType {
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('gender', 'choice', array('choices'=> array('m'=>'Male','f'=>'Female')
                    ,'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        $builder->add('fit_type', 'choice', array('choices'=> array('regular'=>'Regular','petite'=>'Petite','tall'=>'Tall'),'expanded' => true,
                    'multiple' => true,'required'  => true,));
       
        $builder->add('size_title_type', 'choice', array('choices'=> array('letters'=>'Letters','numbers'=>'Numbers','Waist'=>'Waist'),'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        
        $builder->add('male_numbers', 'choice', array('choices'=> array('00'=> '00','0'=>'0','2'=>'2','4'=> '4','6'=>'6','8'=> '8','10'=>'10','12'=>'12','14'=>'14','16'=>'16','18'=>'18','20'=> '20','22'=> '22', '24'=> '24','26'=>'26','28'=> '28','30'=>'30'),'expanded' => true,
                    'multiple' => true,'required'  => true,));
        
        $builder->add(
                'male_letters', 'choice', 
                array('choices'=>array(
        'XXS'=>'XXS', 
           'XS'=>'XS', 
           'S'=>'S', 
           'M'=>'M', 
           'L'=>'L',
           'X'=>'X',
           'XL'=>'XL',
           'XXL'=>'XXL',
           'XXXL'=>'XXXL',
           'XXXXL'=>'XXXXL',
           '1XL'=>'1XL', 
           '2XL'=>'2XL', 
           '3XL'=>'3XL',
           '4XL'=>'4XL',
           '1X'=>'1X', 
           '2X'=>'2X',
           '3X'=>'3X',
           '4X'=>'4X',
           ),
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'male_waists', 'choice', 
                array('choices'=>array('28'=> '28','29'=>'29','30'=>'30','31'=> '31','32'=>'32','33'=> '33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38','39'=> '39','40'=> '40', '41'=> '41','42'=>'42'),                       'multiple'  => true,
                       'expanded'  => true, 
                ));        
        $builder->add(
                'female_numbers', 'choice', 
                array('choices'=>array('00'=> '00','0'=>'0','2'=>'2','4'=> '4','6'=>'6','8'=> '8','10'=>'10','12'=>'12','14'=>'14','16'=>'16','18'=>'18','20'=> '20','22'=> '22', '24'=> '24','26'=>'26','28'=> '28','30'=>'30'),
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'female_letters', 'choice', 
                array('choices'=>array(
        'XXS'=>'XXS', 
           'XS'=>'XS', 
           'S'=>'S', 
           'M'=>'M', 
           'L'=>'L',
           'X'=>'X',
           'XL'=>'XL',
           'XXL'=>'XXL',
           'XXXL'=>'XXXL',
           'XXXXL'=>'XXXXL',
           '1XL'=>'1XL', 
           '2XL'=>'2XL', 
           '3XL'=>'3XL',
           '4XL'=>'4XL',
           '1X'=>'1X', 
           '2X'=>'2X',
           '3X'=>'3X',
           '4X'=>'4X',
           ),
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
        $builder->add(
                'female_waists', 'choice', 
                array('choices'=>array('23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29','30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36'),
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
