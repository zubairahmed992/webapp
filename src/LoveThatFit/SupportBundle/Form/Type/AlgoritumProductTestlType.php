<?php

namespace LoveThatFit\SupportBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlgoritumProductTestlType extends AbstractType
{   
   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('Product', 'entity', array(
                    'class' => 'LoveThatFitAdminBundle:Product',
                    'expanded' => false,
                    'multiple' => false,
                    'property' => 'name',
                    'empty_value' => 'Select Product'
                )); 
    }
    public function getName()
    {
        return 'algorithm';
    }

    
}

?>
