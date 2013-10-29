<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductSizeMeasurementType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('title');
        $builder->add('garment_measurement_flat');
        $builder->add('max_body_measurement');
        $builder->add('vertical_stretch');
        $builder->add('horizontal_stretch');
        $builder->add('stretch_type_percentage');
        $builder->add('ideal_body_size_high');
        $builder->add('ideal_body_size_low');        
    }


     public function getDefaultOptions(array $options)
      {
            return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement',
            'cascade_validation' => true,
            'validation_groups' => array('product_size'),
             );
        } 
        
    public function getName()
    {
        return 'product_size_measurement';
    }

    
}

?>
