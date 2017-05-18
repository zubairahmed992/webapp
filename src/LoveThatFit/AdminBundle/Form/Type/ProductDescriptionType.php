<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductDescriptionType extends AbstractType
{
    
    private $container;

    public function __construct($container)             
    {
        $this->container= $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('item_name');
        $builder->add('description');    
        $builder->add('item_details');     
    }

      public function getDefaultOptions(array $options)
      {
            return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\Product',
            'cascade_validation' => true,
            'validation_groups' => array('product_description'),
             );
        } 

    
    public function getName()
    {
        return 'product_description';
    }

    
}

?>
