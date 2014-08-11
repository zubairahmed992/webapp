<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductSizeMeasurementType extends AbstractType
{
     public function __construct($mode) {
        $this->mode = $mode;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder->add('min_body_measurement');
        if($this->mode=='add'){
            $builder->add('title','hidden');
        }else{
            $builder->add('title');
        }        
        $builder->add('garment_measurement_flat');
        $builder->add('max_body_measurement');
        $builder->add('vertical_stretch');
        $builder->add('horizontal_stretch');
        $builder->add('stretch_type_percentage');
        $builder->add('ideal_body_size_high');
        $builder->add('ideal_body_size_low');   
        $builder->add('garment_measurement_stretch_fit');
        $builder->add('fit_model_measurement');
    }


     public function getDefaultOptions(array $options)
      {
            return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement',
            'cascade_validation' => true,
            'validation_groups' => array('product_size_measurement'),
             );
        } 
        
    public function getName()
    {
        return 'product_size_measurement';
    }

    
}

?>
