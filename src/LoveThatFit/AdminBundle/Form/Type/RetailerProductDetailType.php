<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RetailerProductDetailType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$brand_list=$this->get('admin.helper.brand')->getBrandArray();
        $builder->add('name');
        $builder->add('styling_type');
        $builder->add('hem_length');
        $builder->add('neckline');
        $builder->add('sleeve_styling');
        $builder->add('rise');
        $builder->add('stretch_type');
        $builder->add('horizontal_stretch');
        $builder->add('vertical_stretch');
        $builder->add('fabric_weight');
        $builder->add('structural_detail');
        $builder->add('fit_type');
        $builder->add('fit_priority');        
        $builder->add('fabric_content');
        $builder->add('garment_detail');
        $builder->add('adjustment');
        $builder->add('description');        
        $builder->add('gender', 'choice', array('choices'=> array('M'=>'Male','F'=>'Female')));               
        $builder ->add('Brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                ));            
        $builder ->add('ClothingType', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:ClothingType',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                ));
        $builder->add('disabled', 'checkbox',array('label' =>'','required'=> false,));

//$builder->add('Brand', 'choice',array('choices'=>$brand_list) );
        //$builder->add('ClothingType', 'choice', array('choices'=> array()), array('mapped' => false));
    }

      public function getDefaultOptions(array $options)
      {
            return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\Product',
            'cascade_validation' => true,
            'validation_groups' => array('product_detail'),
             );
        } 

    
    public function getName()
    {
        return 'product';
    }

    
}

?>
