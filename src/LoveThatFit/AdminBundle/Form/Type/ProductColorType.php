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
        $sizes = array('00'=>'00', '0'=>'0', '2'=>'2', '4'=>'4', '6'=>'6', '8'=>'8', '10'=>'10', '12'=>'12','14'=>'14','16'=>'16','18'=>'18','20'=>'20');
        $displayProductColor=array('Yes'=>'Yes');
        $builder->add('title');
        $builder->add('tempImage','hidden');
        $builder->add('tempPattern','hidden');
        $builder->add(
                'sizes', 'choice', 
                array('choices'=>$sizes,
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
