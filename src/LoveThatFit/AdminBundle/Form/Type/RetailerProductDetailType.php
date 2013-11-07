<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RetailerProductDetailType extends AbstractType
{
       private $container;
       private $stretch_type;
      public function __construct($container)             
    {
        $this->container= $container;
        $this->stretch_type=$this->container->getWomenStretchType();
        $this->fabric_weight=$this->container->getWomenFabricWeight(); 
        $this->structural_detail=$this->container->getWomenStructuralDetails(); 
        $this->fit_type=$this->container->getWomenFitType(); 
        $this->layering=$this->container->getWomenLayering(); 
        $this->fabric_content=$this->container->getWomenFabricContent(); 
        $this->garment_detail=$this->container->getWomenGarmentDetail(); 
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$brand_list=$this->get('admin.helper.brand')->getBrandArray();
       // $builder->add('ClothingType', 'choice', array('choices' => $this->clothingType, 'required' => false,'empty_value' => 'Clothing Type',));
        $builder->add('name');
        $builder->add('styling_type','choice', array( 'required' => false,'empty_value' => 'Select Styling Type',));
        $builder->add('hem_length','choice', array( 'required' => false,'empty_value' => 'Select Hem Length',));
        $builder->add('neckline','choice', array( 'required' => false,'empty_value' => 'Select Neck Line',));
        $builder->add('sleeve_styling','choice', array( 'required' => false,'empty_value' => 'Select Sleeve Styling',));
        $builder->add('rise','choice', array( 'required' => false,'empty_value' => 'Select Rise',));
        $builder->add('stretch_type','choice', array( 'choices' => $this->stretch_type,'required' => false,'empty_value' => ' Select Stretch Type',));
        $builder->add('horizontal_stretch');
        $builder->add('vertical_stretch');
        $builder->add('fabric_weight','choice', array( 'choices' => $this->fabric_weight,'required' => false,'empty_value' => ' Select Fabric Weight',));
        $builder->add('structural_detail','choice', array( 'choices'=>$this->structural_detail,'required' => false,'empty_value' => 'Select Stuctural Details',));
        $builder->add('fit_type','choice', array('choices'=>$this->fit_type, 'required' => false,'empty_value' => 'Select Fit Type',));
        $builder->add('layering','choice', array('choices'=>$this->layering, 'required' => false,'empty_value' => 'Select Layering',));
        $builder->add('fit_priority');        
        $builder->add('fabric_content','choice', array('choices'=>$this->fabric_content,'required' => false,'empty_value' => 'Select Fabric Content',));
        $builder->add('garment_detail','choice', array('choices'=>$this->garment_detail, 'required' => false,'empty_value' => 'Select Garment Detail',));
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
                    'empty_value' => 'Select Clothing Type'
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
