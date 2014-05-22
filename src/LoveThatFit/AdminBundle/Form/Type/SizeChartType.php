<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SizeChartType extends AbstractType
{   
    private $sizeTitleType;
    public function __construct($sizeTitleType) {                
         $this->sizeTitleType=$sizeTitleType;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $title = array('xxs'=>'xxs','xs'=>'xs','s'=>'s','m'=>'m','l'=>'l','xl'=>'xl','xxl'=>'xxl','0'=>'0','1'=>'1', '2'=>'2', '4'=>'4', '6'=>'6', '8'=>'8', '10'=>'10', '12'=>'12', '14'=>'14', '16'=>'16', '18'=>'18', '20'=>'20','22'=>'22','24'=>'24');
        $gender=array('f'=>'Female','m'=>'Male');
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
        $builder->add('target', 'choice', array('choices'=> array('Select Target','Top'=>'Top','Bottom'=>'Bottom', 'Dress'=>'Dress')));        
        $builder->add('bodytype', 'choice', array('choices'=> array('Regular'=>'Regular','Petite'=>'Petite', 'Tall'=>'Tall')));
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
      $builder->add('size_title_type', 'choice', array('choices'=>$this->sizeTitleType,'expanded' => true,
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
