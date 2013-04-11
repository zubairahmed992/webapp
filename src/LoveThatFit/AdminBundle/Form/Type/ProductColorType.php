<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductColorType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sizes = array('XS'=>'XS', 'S'=>'S', 'M'=>'M', 'ML'=>'ML', 'L'=>'L', 'XL'=>'XL', '2XL'=>'2XL', '3XL'=>'3XL');
        
        $builder->add('title');
        $builder->add('color_a');
        $builder->add('color_b');
        $builder->add('color_c');
        $builder->add('pattern');
        $builder->add('file');
        $builder->add(
                'sizes', 'choice', 
                array('choices'=>$sizes,
                       'multiple'  => true,
                       'expanded'  => true, 
                )
                
                );
        
                

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
        return 'product';
    }

    
}

?>
