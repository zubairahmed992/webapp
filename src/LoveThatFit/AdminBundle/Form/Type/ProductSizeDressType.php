<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductSizeDressType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('back');
        $builder->add('bust');
        $builder->add('length');
        $builder->add('waist');
        $builder->add('hip');
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
