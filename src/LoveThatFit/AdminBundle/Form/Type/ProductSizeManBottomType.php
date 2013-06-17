<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductSizeManBottomType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
      
        $builder->add('waist_min');
        $builder->add('waist_max');
        
      
        $builder->add('inseam_min');
        $builder->add('inseam_max');
      
        $builder->add('outseam_min');
        $builder->add('outseam_max');
        
        $builder->add('length');
        
        $builder->add('thigh');        
        
        $builder->add('hem');
        
     }


     public function getDefaultOptions(array $options)
      {  
            return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\ProductSize',
            'cascade_validation' => true,
            'validation_groups' => array('product_size'),
             );
        } 
        
    public function getName()
    {
        return 'product_size';
    }

    
}

?>
