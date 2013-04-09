<?php
namespace LoveThatFit\UserBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyAnswerType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('question');        
        $builder->add('choices', 'collection', array(
                'type'         => new SurveyAnswer(),
                'allow_add'    => true,
                'allow_delete' => true
            ));
    }

     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\SurveyQuestion',
        );
    }
    public function getName() {
        return 'question';
    }

}

?>
