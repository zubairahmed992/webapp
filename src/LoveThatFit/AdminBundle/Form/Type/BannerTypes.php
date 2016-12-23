<?php

namespace LoveThatFit\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BannerTypes extends AbstractType {
    private $entity;

    public function __construct($mode,$entity) {
        $this->mode = $mode;
        $this->display_screen = $entity->getDisplayScreen();
        $this->image_position = $entity->getImagePosition();
        $this->banner_type = $entity->getBannerType();
        $this->cat_id = $entity->getCatId();
        $this->parent_id = $entity->getParentId();
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('banner_type', 'choice', array(
            'data'	=> $this->banner_type,
            'choices'=> array('1'=>'Fullsize Banner','2'=>'Image with Title','3'=>'Text, Description with Price'),
            'multiple' => false,
            'expanded' => false
        ));
        $builder->add('name', 'text',array('required'=>true));
        $builder->add('description', 'textarea',array('required'=>false));
        $builder->add('price_min', 'money', array(
            'currency' => 'USD',
            'required'=>false
        ));
        $builder->add('price_max', 'money', array(
            'currency' => 'USD',
            'required'=>false
        ));
        $builder->add('display_screen', 'choice', array(
            'data'	=> $this->display_screen,
            'choices'=> array('shop'=>'Shop','product_list'=>'Product List'),
            'multiple' => false,
            'expanded' => false
        ));
        $builder->add('catid', 'hidden',array(
            'data' => $this->cat_id,
        ));
        $builder->add('parentid', 'hidden',array(
            'data' => $this->parent_id,
        ));
        $builder->add('sorting', 'integer', array('required' => true, 'attr' => array('min' => 1)));

        $builder->add('file',null,array('required'=>true));
        $builder->add('image_position', 'choice', array(
            'data'	=> $this->image_position,
            'choices'=> array('left'=>'Left','right'=>'Right'),
            'multiple' => false,
            'expanded' => false,
            'required' => true,
        ));
        $builder->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false));
    }

    public function getDefaultOptions(array $options) {

            return array(
                'data_class' => 'LoveThatFit\AdminBundle\Entity\Banner',
                'cascade_validation' => true,
                'validation_groups' => array($this->mode)
            );
        
    }
    public function getName() {
        return 'banner';
    }

}

?>
