<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MannequinTestlType extends AbstractType
{   
   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('User', 'entity', array(
                    'class' => 'LoveThatFitUserBundle:User',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false,
                    'property' => 'email',
                    'empty_value' => 'Select User'
                ));
    }
    public function getName()
    {
        return 'user';
    }

    
}

?>
