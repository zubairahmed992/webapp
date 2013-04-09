<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        $builder->add('name');
        $builder->add('sku');
        $builder->add('file');
        $builder->add('img_file');
        $builder->add('adjustment');
        $builder->add('waist');
        $builder->add('hip');
        $builder->add('bust');
        $builder->add('arm');
        $builder->add('leg');
        $builder->add('inseam');
        $builder->add('outseam');
        $builder->add('hem');
        $builder->add('back');
        $builder->add('length');
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

//$builder->add('Brand', 'choice',array('choices'=>$brand_list) );
        //$builder->add('ClothingType', 'choice', array('choices'=> array()), array('mapped' => false));
    }

     public function getDefaultOptions(array $options)
     {
             return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\Product',
            'cascade_validation' => true,
            'validation_groups' => array('products'),
             );
      } 
 

    
    public function getName()
    {
        return 'product';
    }

    
}

?>
