<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MarketingTilesTypes extends AbstractType {
    private $entity;
    private $button_action;
    private $sorting;
    public function __construct($mode,$entity,$button_action,$sorting) {
        $this->mode = $mode;
        $this->button_action = ($button_action) ? $button_action : 0;
        $this->sorting = ($sorting) ? $sorting : 0;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title', 'text',array('required'=>true));
        $builder->add('description', 'textarea',array('required'=>false));
        $builder->add('file',null,array('required'=>true));
        $builder->add('button_title', 'text',array('required'=>false));
        $builder->add('button_action', 'text',array('required'=>false,'data' => $this->button_action));
        $builder->add('sorting', 'integer', array('required' => false, 'data' => $this->sorting, 'attr' => array('min' => 1)));
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
