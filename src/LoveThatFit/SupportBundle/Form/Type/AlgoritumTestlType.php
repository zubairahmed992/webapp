<?php

namespace LoveThatFit\SupportBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlgoritumTestlType extends AbstractType
{   
   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('User', 'entity', array(
                    'class'         => 'LoveThatFitUserBundle:User',
                    'query_builder' => function ($repository) {
                            return $repository->createQueryBuilder('u')
                            ->orderBy('u.email', 'ASC');
                            },
                    'expanded'      => false,
                    'multiple'      => false,
                    'required'      => false,
                    'property'      => 'email',
                    'empty_value'   => 'Select User'
                ));
    }
    public function getName()
    {
        return 'algorithm';
    }

    
}

?>
