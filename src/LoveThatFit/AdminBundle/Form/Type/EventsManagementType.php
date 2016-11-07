<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EventsManagementType extends AbstractType {

    public function __construct($mode) {
        $this->mode = $mode;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('event_name', 'text');
        $builder->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\EventsManagement',
            'cascade_validation' => true,
            'validation_groups' => array($this->mode)
        );
    }

    public function getName() {
        return 'events_management';
    }
}

?>
