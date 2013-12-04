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
    
    //private $gender;
     public function __construct($sizes_number_petite,$sizes_number_regular=Null,$sizes_number_tall)             
    {
        //$this->container= $container;
        $this->sizes_number_petite=$sizes_number_petite;        
        $this->sizes_number_regular=$sizes_number_regular;        
        $this->sizes_number_tall=$sizes_number_tall;        
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $displayProductColor=array('Yes'=>'Yes');
        $builder->add('title');
        $builder->add('tempImage','hidden');
        $builder->add('tempPattern','hidden');
        $builder->add(
                'petiteSizes', 'choice', 
                array('choices'=>$this->sizes_number_petite,
                       'multiple'  => true,
                       'expanded'  => true, 
                )
                
                );
        $builder->add(
                'regularSizes', 'choice', 
                array('choices'=>$this->sizes_number_regular,
                       'multiple'  => true,
                       'expanded'  => true, 
                )
                
                );
        $builder->add(
                'tallSizes', 'choice', 
                array('choices'=>$this->sizes_number_tall,
                       'multiple'  => true,
                       'expanded'  => true, 
                )
                
                );
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
