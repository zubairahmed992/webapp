<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductDataType extends AbstractType
{   
   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('Product', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Product',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'empty_value' => 'Select Product'
                ));
        $builder ->add('Brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'empty_value' => 'Select Brand'
                )); 
        $builder->add('name');
    }
    public function getName()
    {
        return 'productdata';
    }

    
}

?>
