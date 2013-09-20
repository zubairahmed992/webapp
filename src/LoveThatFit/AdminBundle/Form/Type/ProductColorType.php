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
        $sizes = array('xxs:0'=>'xxs:0','xs:1'=>'xs:1', 'xs:2'=>'xs:2', 's:4'=>'s:4', 's:6'=>'s:6', 'm:8'=>'m:8', '
m:10'=>'m:10', 'l:12'=>'l:12', 'l:14'=>'l:14', 'xl:16'=>'xl:16', 'xl:18'=>'xl:18', 'xxl:20'=>'xxl:20', 'xxl:22'=>'xxl:22', 'xxl:24'=>'xxl:24', 'xxl:26'=>'xxl:26', 'xxl:28'=>'xxl:28');
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
