<?php

namespace LoveThatFit\SupportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//use LoveThatFit\AdminBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EvaluationDefaultProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('product_id', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Product',
                    'property' => 'NameAndController', //get this method from the LoveThatFit\AdminBundle\Entity\Product
                    'empty_value'   => 'Please Select Product',
                    'label'=>'Select Product',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.name', 'ASC');
                    }
                )
            )
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LoveThatFit\SupportBundle\Entity\EvaluationDefaultProducts'
        ));
    }

    public function getName()
    {
        return 'lovethatfit_supportbundle_evaluationdefaultproductstype';
    }
}
