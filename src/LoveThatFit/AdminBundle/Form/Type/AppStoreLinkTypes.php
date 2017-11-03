<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AppStoreLinkTypes extends AbstractType {
    private $entity;

    public function __construct($mode,$entity) {
        $this->mode = $mode;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('app_link', 'text', array('attr' => array('size' => '100')));
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\AppStoreLink',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }
    public function getName() {
        return 'app_store_link';
    }

}

?>
