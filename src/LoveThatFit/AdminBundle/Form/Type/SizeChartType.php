<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SizeChartType extends AbstractType
{
    public function __construct($mode) {
        $this->mode = $mode;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $title = array('xxs:0'=>'xxs:0','xs:1'=>'xs:1', 'xs:2'=>'xs:2', 's:4'=>'s:4', 's:6'=>'s:6', 'm:8'=>'m:8', '
m:10'=>'m:10', 'l:12'=>'l:12', 'l:14'=>'l:14', 'xl:16'=>'xl:16', 'xl:18'=>'xl:18', 'xxl:20'=>'xxl:20', 'xxl:22'=>'xxl:22', 'xxl:24'=>'xxl:24', 'xxl:26'=>'xxl:26', 'xxl:28'=>'xxl:28');
        $gender=array(''=>'Select Gender','m'=>'Male','f'=>'Female');
        $builder->add(
                'title', 'choice', 
                array('choices'=>$title,
                       'multiple'  =>False,
                       'expanded'  => False, 
                )                
                );
        $builder->add('gender','choice', 
                array('choices'=>$gender,
                       'multiple'  =>False,
                       'expanded'  => False, 
                ) );
        $builder->add('target', 'choice', array('choices'=> array(''=>'Select Target','Top'=>'Top','Bottom'=>'Bottom', 'Dress'=>'Dress')));        
        $builder->add('bodytype', 'choice', array('choices'=> array('Regular'=>'Regular','Petite'=>'Petite', 'Tall'=>'Tall')));
        $builder->add('waist');
        $builder->add('hip');
        $builder->add('bust');
        $builder->add('chest');
        $builder->add('inseam');
        $builder->add('outseam');
        $builder->add('sleeve');
        $builder->add('neck');     
        $builder->add('back');
        $builder->add('thigh');
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
