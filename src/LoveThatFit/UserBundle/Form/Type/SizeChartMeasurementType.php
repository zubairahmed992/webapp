<?php

namespace LoveThatFit\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SizeChartMeasurementType extends AbstractType {

    private $top_brands;
    private $bottom_brands;
    private $dress_brands;

    public function __construct($top_brands, $bottom_brands, $dress_brands) {
        $this->top_brands = $top_brands;
        $this->bottom_brands = $bottom_brands;
        $this->dress_brands = $dress_brands;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('top_brand', 'choice', array('choices' => $this->top_brands, 'required' => false, 'empty_value' => 'Brand',));
        $builder->add('bottom_brand', 'choice', array('choices' => $this->bottom_brands, 'required' => false, 'empty_value' => 'Brand',));
        $builder->add('dress_brand', 'choice', array('choices' => $this->dress_brands, 'required' => false, 'empty_value' => 'Brand',));

        $builder->add('top_size', 'choice', array('required' => false));
        $builder->add('bottom_size', 'choice', array('required' => false));
        $builder->add('dress_size', 'choice', array('required' => false));
    }

    public function getName() {
        return 'brand_size_chart';
    }

}

?>