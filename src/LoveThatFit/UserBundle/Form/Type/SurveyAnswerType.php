<?php
namespace LoveThatFit\UserBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyAnswerType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('answer');
        
    }

     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\SurveyQuestion',
        );
    }
    public function getName() {
        return 'answer';
    }

}

?>
