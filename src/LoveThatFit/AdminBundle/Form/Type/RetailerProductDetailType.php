<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RetailerProductDetailType extends AbstractType
{
       // private $clothingType;
      public function __construct()             
    {
       
       // $this->clothingType=$clothingType;
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$brand_list=$this->get('admin.helper.brand')->getBrandArray();
       // $builder->add('ClothingType', 'choice', array('choices' => $this->clothingType, 'required' => false,'empty_value' => 'Clothing Type',));
        $builder->add('name');
        $builder->add('styling_type','choice', array( 'required' => false,'empty_value' => 'Styling Type',));
        $builder->add('hem_length','choice', array( 'required' => false,'empty_value' => 'Hem Length',));
        $builder->add('neckline','choice', array( 'required' => false,'empty_value' => 'Neck Line',));
        $builder->add('sleeve_styling','choice', array( 'required' => false,'empty_value' => 'Sleeve Styling',));
        $builder->add('rise','choice', array( 'required' => false,'empty_value' => 'Rise',));
        $builder->add('stretch_type','choice', array( 'required' => false,'empty_value' => 'Stretch Type',));
        $builder->add('horizontal_stretch');
        $builder->add('vertical_stretch');
        $builder->add('fabric_weight','choice', array( 'required' => false,'empty_value' => 'Fabric Weight',));
        $builder->add('structural_detail','choice', array( 'required' => false,'empty_value' => 'Stuctural Details',));
        $builder->add('fit_type','choice', array( 'required' => false,'empty_value' => 'Fit Type',));
        $builder->add('layering','choice', array( 'required' => false,'empty_value' => 'Layering',));
        $builder->add('fit_priority');        
        $builder->add('fabric_content','choice', array( 'required' => false,'empty_value' => 'Fabric Content',));
        $builder->add('garment_detail','choice', array( 'required' => false,'empty_value' => 'Garment Detail',));
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
