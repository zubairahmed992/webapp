<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SizeChartType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $title = array('XS'=>'XS', 'S'=>'S', 'M'=>'M', 'ML'=>'ML', 'L'=>'L', 'XL'=>'XL', '2XL'=>'2XL', '3XL'=>'3XL');
        $builder->add(
                'title', 'choice', 
                array('choices'=>$title,
                       'multiple'  =>False,
                       'expanded'  => False, 
                )                
                );
        $builder->add('gender', 'choice', array('choices'=> array('M'=>'Male','F'=>'Female')));
        $builder->add('target', 'choice', array('choices'=> array('Top'=>'Top','Bottom'=>'Bottom', 'dress'=>'dress')));
        $builder->add('waist');
        $builder->add('hip');
        $builder->add('bust');
        $builder->add('chest');
        $builder->add('inseam');
        $builder->add('neck');
        $builder->add('sleeve');
        $builder->add('Brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                ));
       $builder->add('disabled', 'checkbox',array('label' =>'Disabled','required'=> false,));       
    }

     public function getDefaultOptions(array $options)
     {
             return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\SizeChart',
            'cascade_validation' => true,
            'validation_groups' => array('size_chart'),
             );
      } 
 

    
    public function getName()
    {
        return 'sizechart';
    }

    
}

?>
