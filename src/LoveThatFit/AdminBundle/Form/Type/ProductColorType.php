<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductColorType extends AbstractType
{
    private $container;
    private $sizes_number_petite;
    private $sizes_number_regular;
    private $sizes_number_tall;
    private $women_waist;
    
    //private $gender;
     public function __construct($sizes=Null)             
    {
        //$this->container= $container;
       if(isset($sizes['petite'])){
         $this->sizes_number_petite= $sizes['petite'];//$sizes_number_petite;        
       }
       if(isset($sizes['regular'])){
        $this->sizes_number_regular=$sizes['regular'];//$sizes_number_regular;        
       }
       if(isset($sizes['tall'])){
        $this->sizes_number_tall=$sizes['tall'];//$sizes_number_tall;        
       }
       if(isset($sizes['women_waist'])){
        $this->women_waist=$sizes['women_waist'];//$women_waist;
       }
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $displayProductColor=array('Yes'=>'Yes');
        $builder->add('title');
        $builder->add('tempImage','hidden');
        $builder->add('tempPattern','hidden');
        if($this->sizes_number_petite){
        $builder->add(
                'petiteSizes', 'choice', 
                array('choices'=>$this->sizes_number_petite,
                       'multiple'  => true,
                       'expanded'  => true, 
                )
                
                );}
        if($this->sizes_number_regular){
        $builder->add(
                'regularSizes', 'choice', 
                array('choices'=>$this->sizes_number_regular,
                       'multiple'  => true,
                       'expanded'  => true, 
                )
                
                );}
       if($this->sizes_number_tall){
        $builder->add(
                'tallSizes', 'choice', 
                array('choices'=>$this->sizes_number_tall,
                       'multiple'  => true,
                       'expanded'  => true, 
                )
                
                );
       }
       if($this->women_waist){
        $builder->add(
                'womenWaistSizes', 'choice', 
                array('choices'=>$this->women_waist,
                       'multiple'  => true,
                       'expanded'  => true, 
                ));
       }
         $builder ->add('displayProductColor', 'choice', array(
                    'choices'   => array('1'=>' '),
                    'expanded' => true,
                    'multiple' => true,
                    'required' =>true,
                  
                ));  
       
                

//$builder->add('Brand', 'choice',array('choices'=>$brand_list) );
        //$builder->add('ClothingType', 'choice', array('choices'=> array()), array('mapped' => false));
    }

     public function getDefaultOptions(array $options)
     {
             return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\ProductColor',
            'cascade_validation' => true,
            'validation_groups' => array('product_color'),
             );
      } 
 

    
    public function getName()
    {
        return 'product_color';
    }

    
}

?>
