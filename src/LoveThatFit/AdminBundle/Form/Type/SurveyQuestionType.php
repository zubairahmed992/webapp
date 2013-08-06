<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyQuestionType extends AbstractType
{
    public function __construct($mode) {
        $this->mode = $mode;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('question', 'text', array('label' =>' '));        
       $builder->add('questionstatus', 'hidden', array('data' => '1',));       
    }

     public function getDefaultOptions(array $options)
     {
             return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\SurveyQuestion',
            'cascade_validation' => true,
            'validation_groups' => array('survey_question'),
             );
      } 
 

    
    public function getName()
    {
        return 'question';
    }

    
}

?>
