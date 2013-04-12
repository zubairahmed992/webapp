<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductItemType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      
        
        $builder->add('line_number');
        $builder->add('file');
        
                

//$builder->add('Brand', 'choice',array('choices'=>$brand_list) );
        //$builder->add('ClothingType', 'choice', array('choices'=> array()), array('mapped' => false));
    }

     public function getDefaultOptions(array $options)
     {
             return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\ProductItem',
            'cascade_validation' => true,
            'validation_groups' => array('product_item'),
             );
      } 
 

    
    public function getName()
    {
        return 'product';
    }

    
}

?>
