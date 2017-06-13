<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MarketingTilesTypes extends AbstractType {
    private $entity;

    public function __construct($mode,$entity) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title', 'text',array('required'=>true));
        $builder->add('description', 'textarea',array('required'=>false));
        $builder->add('file',null,array('required'=>true));
        $builder->add('button_title', 'text',array('required'=>false));
        $builder->add('button_action', 'text',array('required'=>false,'data' => '0'));
        $builder->add('sorting', 'integer', array('required' => false, 'attr' => array('min' => 1)));
        $builder->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false));
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\MarketingTiles',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }
    public function getName() {
        return 'marketing_tiles';
    }

}

?>
