<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductItemPieceType extends AbstractType {   
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder->add('piece_type', 'choice', array('choices'=> array('suit'=>'Suit','pant'=>'Pant','coat'=>'Coat')
                    ,'expanded' => false,
                     'multiple' => false,'required'  => true,
                      'empty_value' => 'Select Piece Type'
              ));        
            $builder->add('file');   
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\ProductItemPiece',
                'cascade_validation' => true,                      
            );
        
    }

    public function getName() {
        return 'piece';
    }

}

?>
