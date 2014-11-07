<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SizeChartDynamicType extends AbstractType
{   
    private $size_specs;
    public function __construct($size_specs) {                
         $this->size_specs=$size_specs;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
                'title', 'choice', 
                array( 'multiple'  =>False,
                       'expanded'  => False, 
                )                
                );
        $builder->add('gender','choice', 
                array('choices'=>$this->size_specs['genders']['descriptions'],
                       'multiple'  =>False,
                       'expanded'  => False, 
                ) );
        $builder->add('target', 'choice');        
        $builder->add('bodytype', 'choice');
        $builder->add('waist');
        $builder->add('hip');
        $builder->add('bust');
        $builder->add('chest');
        $builder->add('inseam');
        $builder->add('outseam');
        $builder->add('sleeve');
        $builder->add('neck');     
        $builder->add('shoulder_across_back');
        $builder->add('thigh');
        $builder->add('Brand', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Brand',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                ));
      $builder->add('size_title_type', 'choice', array('expanded' => true,
                    'multiple' => false,'required'  => true,));
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
