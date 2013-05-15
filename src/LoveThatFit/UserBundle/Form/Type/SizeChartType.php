<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SizeChartType extends AbstractType
{
private $top_brands;
private $bottom_brands;
private $dress_brands;

     public function __construct($top_brands, $bottom_brands,$dress_brands)             
    {
        $this->top_brands=$top_brands;
        $this->bottom_brands=$bottom_brands;
        $this->dress_brands=$dress_brands;
    }
    
     public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('top_brands', 'choice', array('choices' => $this->top_brands, 'required' => false));
        $builder->add('bottom_brands', 'choice', array('choices' => $this->bottom_brands, 'required' => false));
        $builder->add('dress_brands', 'choice', array('choices' => $this->dress_brands, 'required' => false));
        
        $builder->add('top_brand_sizes', 'choice', array('required' => false));
        $builder->add('bottom_brand_sizes', 'choice', array('required' => false));
        $builder->add('dress_brand_sizes', 'choice', array('required' => false));
        
       
         }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
       
    }

    public function getName()
    {
        return 'size_chart';
    }

     private function getBrandArray($target) {

        $brands = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                ->getBrandsByTarget($target);

        $brands_array = array();
        foreach ($brands as $i) {
            $brands_array[$i['id']] = $i['name'];
        }
        return $brands_array;
    }
    
}

?>
