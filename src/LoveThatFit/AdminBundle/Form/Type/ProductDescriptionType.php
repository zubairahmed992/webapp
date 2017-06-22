<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 5/30/2017
 * Time: 3:56 PM
 */

namespace LoveThatFit\AdminBundle\Form\Type;

use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

class ProductDescriptionType extends AbstractType
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('item_name');
        /*$builder->add('country_origin');*/
        $builder->add('description');
        $builder->add('item_details');
        $builder->add('care_label');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\Product',
            'cascade_validation' => true,
            'validation_groups' => array('product_description'),
        );
    }

    public function getName()
    {
        return 'product_description';
    }
}