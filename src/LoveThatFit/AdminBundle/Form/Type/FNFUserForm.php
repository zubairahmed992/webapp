<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 2/23/2017
 * Time: 7:21 PM
 */

namespace LoveThatFit\AdminBundle\Form\Type;


use LoveThatFit\UserBundle\Entity\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FNFUserForm  extends AbstractType
{
    private $mode;
    private $options;

    public function __construct($mode, $entity, $defaultOptions = array()) {
        $this->mode = $mode;
        $this->options = $defaultOptions;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('groups', 'collection', array(
            'type' => new FNFGroupForm( $this->options ),
            'allow_add' => true,
            'options' => array('data_class' => 'LoveThatFit\AdminBundle\Entity\FNFGroup'),
            'prototype' => true,
            'by_reference' => false,
            //'class' => 'LoveThatFit\AdminBundle\Entity\FNFGroup'
        ))
        ->add('users', 'entity', array(
            'class' => 'LoveThatFitUserBundle:User',
            'empty_value' => '',
            'multiple' => true,
            'label' => 'users'
        ));
    }

    /*public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $new_choice = new ChoiceView(array(), 'add', 'add new'); // <- new option
        $view->children['users']->vars['choices'][] = $new_choice;//<- adding the new option
        $view->children['users']->vars['preferred_choices'][] = $new_choice;
    }*/

    /*public function getDefaultOptions(array $options) {

        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\FNFUser',
            'cascade_validation' => true,
            'validation_groups' => array($this->mode)
        );

    }*/

    public function configureOptions(OptionsResolver  $resolver){
        $resolver->setDefaults(
            array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\FNFUser',
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
        return "FNFUser";
    }
}