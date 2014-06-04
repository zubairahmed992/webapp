<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductColorViewType extends AbstractType {   
   
    public function __construct($mode) {
        $this->mode = $mode;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
            $builder->add('title','text');       
           if($this->mode=='add'){
            $builder->add('file');
        }else{
            $builder->add('file',null,array('required'=>false));
        } 
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\ProductColorView',
                'cascade_validation' => true,                      
            );
        
    }

    public function getName() {
        return 'color_view';
    }

}

?>
