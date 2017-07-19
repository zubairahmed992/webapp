<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductDetailTypeNew extends AbstractType
{

    private $container;
    private $sizeTitleType;
    private $stretch_type;
    private $product_status;
    private $disabled;

    public function __construct($container, $sizeTitleType, $status = null, $disabled = null, $clothing_type, $product)
    {
        $this->container = $container;
        if ($product['gender'] == 'f') {
            $this->stretch_type = $this->container->getWomenStretchType();
            $this->fabric_weight = $this->container->getWomenFabricWeight();
            $this->structural_detail = $this->container->getWomenStructuralDetails();
        } else if ($product['gender'] == 'm') {
            $this->stretch_type = $this->container->getManStretchType();
            $this->fabric_weight = $this->container->getManFabricWeight();
            $this->structural_detail = $this->container->getManStructuralDetails();
        }
        $this->fit_type = $this->container->getFitType();
        $this->layering = $this->container->getLayering();
        $this->fabric_content = $this->container->getFabricContent();
        $this->garment_detail = $this->container->getGarmentDetail();
        $this->sizeTitleType = $sizeTitleType;
        $this->product_status = isset($status) ? $status : 'pending';
        $this->disabled = ($disabled) ? 1 : 0;
        $this->clothing_type = $clothing_type;

        $this->stretch_type_selected = $this->getSelectedValue($this->stretch_type, $product['stretch_type']);
        $this->fabric_weight_selected = $this->getSelectedValue($this->fabric_weight, $product['fabric_weight']);
        $this->structural_detail_selected = $this->getSelectedValue($this->structural_detail, $product['structural_detail']);
        $this->fit_type_selected = $this->getSelectedValue($this->fit_type, $product['fit_type']);
        $this->layering_selected = $this->getSelectedValue($this->layering, $product['layering']);


        /*$this->stretch_type_selected = $this->getSelectedValue($this->stretch_type, str_replace(" ", "_", strtolower($product['stretch_type'])));
        $this->fabric_weight_selected = $this->getSelectedValue($this->fabric_weight, $product['fabric_weight']);
        $this->structural_detail_selected = $this->getSelectedValue($this->structural_detail, $product['structural_detail']);
        $this->fit_type_selected = $this->getSelectedValue($this->fit_type, $product['fit_type']);
        $this->layering_selected = $this->getSelectedValue($this->layering, $product['layering']);*/
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $brand_list=$this->get('admin.helper.brand')->getBrandArray();
        // $builder->add('ClothingType', 'choice', array('choices' => $this->clothingType, 'required' => false,'empty_value' => 'Clothing Type',));
        $builder->add('name');
        $builder->add('control_number');
        $builder->add('product_model_height');
        $builder->add('styling_type', 'choice', array('required' => false, 'empty_value' => 'Select Styling Type',));
        $builder->add('hem_length', 'choice', array('required' => false, 'empty_value' => 'Select Hem Length',));
        $builder->add('neckline', 'choice', array('required' => false, 'empty_value' => 'Select Neck Line',));
        $builder->add('sleeve_styling', 'choice', array('required' => false, 'empty_value' => 'Select Sleeve Styling',));
        $builder->add('rise', 'choice', array('required' => false, 'empty_value' => 'Select Rise',));
        $builder->add('stretch_type', 'choice', array('choices' => $this->stretch_type, 'required' => false, 'empty_value' => ' Select Stretch Type','data' => $this->stretch_type_selected));
        $builder->add('horizontal_stretch');
        $builder->add('vertical_stretch');
        $builder->add('fabric_weight', 'choice', array('choices' => $this->fabric_weight, 'required' => false, 'empty_value' => ' Select Fabric Weight','data' => $this->fabric_weight_selected));
        $builder->add('structural_detail', 'choice', array('choices' => $this->structural_detail, 'required' => false, 'empty_value' => 'Select Stuctural Details','data' => $this->structural_detail_selected));
        $builder->add('fit_type', 'choice', array('choices' => $this->fit_type, 'required' => false, 'empty_value' => 'Select Fit Type','data' => $this->fit_type_selected));
        $builder->add('layering', 'choice', array('choices' => $this->layering, 'required' => false, 'empty_value' => 'Select Layering','data' => $this->layering_selected));
        $builder->add('fit_priority');
        $builder->add('fabric_content', 'choice', array('choices' => $this->fabric_content, 'required' => false, 'empty_value' => 'Select Fabric Content',));
        $builder->add('garment_detail', 'choice', array('choices' => $this->garment_detail, 'required' => false, 'empty_value' => 'Select Garment Detail',));
        $builder->add('description');
        $builder->add('gender', 'choice', array('choices' => array('m' => 'Male', 'f' => 'Female')));

        $builder->add('Retailer', 'entity', array(
            'class' => 'LoveThatFitAdminBundle:Retailer',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'property' => 'title',
            'empty_value' => 'Select Retailer'
        ));

        $builder->add('Brand', 'entity', array(
            'class' => 'LoveThatFitAdminBundle:Brand',
            'expanded' => false,
            'multiple' => false,
            'property' => 'name',
        ));
        $builder->add('ClothingType', 'choice', array(
            'choices' => $this->clothing_type['choices'],
            'required' => false,
            'empty_value' => ' Select Clothing Type',
            'mapped' => false,
            'data' => $this->clothing_type['selected']
        ));
        $builder->add('size_title_type', 'choice', array('choices' => $this->sizeTitleType, 'expanded' => true,
            'multiple' => false, 'required' => true,));

        /*if($this->disabled) {
            if($this->product_status=='completed') {
                $builder->add('disabled', 'checkbox',array('label' =>'','required'=> false));
            } else {
                $builder->add('disabled', 'checkbox',array('label' =>'','required'=> false,'value' =>$this->disabled,'disabled' =>'disabled'));
            }
        } else {
            $builder->add('disabled', 'checkbox',array('label' =>'','required'=> false,'value' =>$this->disabled));
        }
        $builder->add('disabled', 'checkbox', array('label' => '', 'required' => false));*/

        $builder->add('status', 'choice', array('choices' => array('pending' => 'Pending', 'review' => 'Needs Review', 'completed' => 'Complete'), 'data' => $this->product_status));

        // $builder->add('Brand', 'choice',array('choices'=>$brand_list) );
        // $builder->add('ClothingType', 'choice', array('choices'=> array()), array('mapped' => false));
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

    private function getSelectedValue($data, $item)
    {
        $item = $this->format_spec($item);
        foreach ($data as $key => $val) {
            if ($key == $item) {
                return $key;
            }
        }
        return '';
    }

    private function format_spec($input)
    {
        $input = str_replace("-", " ", $input);
        /*$input = str_replace("/", " ", $input);*/
        $input = explode(" ", $input);
        $formatted = '';
        foreach ($input as $key => $val) {
            if ($val != '-' && $val != '') {
                if ($formatted == '') {
                    $formatted .= $val;
                } else {
                    $formatted .= '_' . $val;
                }
            }
        }
        return strtolower($formatted);
    }
}