<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BrandSizeChartType extends AbstractType
{   
   public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('Brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false,
                    'property' => 'name',
                    'empty_value' => 'Select Brand'
                ));
    }
    public function getName()
    {
        return 'brand_sizechart';
    }

    
}

?>
