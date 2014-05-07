<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductRawType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('fit_priority');
        $builder->add('fabric_content');
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'LoveThatFit\AdminBundle\Entity\Product',
        );
    }

    public function getName() {
        return 'product';
    }

}

?>
