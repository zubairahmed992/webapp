<?php

namespace LoveThatFit\WebServiceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ServiceFormType extends AbstractType {

    public function __construct($service_names) {
        $this->service_names = $service_names;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {        
        $builder->add('target', 'choice', array('choices'=> $this->service_names));        
    }

    public function getDefaultOptions(array $options) {
        return $options;
    }

    public function getName() {
        return 'service_hit';
    }

}

?>
