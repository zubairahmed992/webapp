<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductSizeWomenDressType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
      
        $builder->add('bust_min');
        $builder->add('bust_max');
        
      
        $builder->add('hip_min');
        $builder->add('hip_max');
        
        $builder->add('hem');
        
        $builder->add('length');
        
       
        $builder->add('back_min');
        $builder->add('back_max');
        
        
       
        $builder->add('waist_min');
        $builder->add('waist_max');
        
       
        $builder->add('sleeve_min');
        $builder->add('sleeve_max');
        
        
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
