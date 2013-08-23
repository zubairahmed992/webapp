<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyAnswerType extends AbstractType
{
    public function __construct($mode) {
        $this->mode = $mode;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('answer', 'text', array('label' =>' '));        
    }

     public function getDefaultOptions(array $options)
     {
             return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\SurveyAnswer',
            'cascade_validation' => true,
            'validation_groups' => array('survey_answer'),
             );
      } 
 

    
    public function getName()
    {
        return 'answer';
    }

    
}

?>
