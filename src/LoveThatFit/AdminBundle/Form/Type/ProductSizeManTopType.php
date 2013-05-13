<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductSizeManTopType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        $builder->add('neck_min');
        $builder->add('neck_max');
        
       
        $builder->add('sleeve_min');
        $builder->add('sleeve_max');
        
       
        $builder->add('chest_min');
        $builder->add('chest_max');
        
        
        $builder->add('back_min');
        $builder->add('back_max');
        
        $builder->add('hem');
        
        $builder->add('length');
        
      
        $builder->add('waist_min');
        $builder->add('waist_max');
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
