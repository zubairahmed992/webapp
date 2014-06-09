<?php

namespace LoveThatFit\AdminBundle\Form\Type;
use  LoveThatFit\AdminBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductItemPieceType extends AbstractType {  
   
    
    private $product_color_view;
    public function __construct($product_color_view)             
    {       
        $this->product_color_view=$product_color_view;
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
               
        $builder->add('product_color_view', 'entity', array(
                    'choices'=>$this->product_color_view,
                    'class' => 'LoveThatFitAdminBundle:ProductColorView',
                    'expanded' => false,
                    'required'  => false,
                    'multiple' => false,
                    'empty_value' => 'View',
                    'property' => 'title',
              ));       
       
        
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
