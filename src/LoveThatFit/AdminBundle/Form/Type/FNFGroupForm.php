<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 3/1/2017
 * Time: 4:40 PM
 */

namespace LoveThatFit\AdminBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FNFGroupForm extends AbstractType
{
    private $defaultOptions;
    public function __construct( $defaultOptions ) {
        $this->defaultOptions = $defaultOptions;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('discount', 'text', array(
                'required'=>true
        ))
        ->add('start_at', 'text' , array(
            'required'=>true))
        ->add('end_at', 'text' , array(
            'required'=>true))
        ->add('min_amount', 'text' , array(
            'required'=>true))
        ->add('groupTitle', 'text' , array('required'=>true));
    }

    /*public function getDefaultOptions(array $options) {

        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\FNFGroup',
            'cascade_validation' => true,
        );

    }*/

    public function configureOptions(OptionsResolver  $resolver){
        $resolver->setDefaults(
            array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\FNFGroup',
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "FNFGroup";
    }
}