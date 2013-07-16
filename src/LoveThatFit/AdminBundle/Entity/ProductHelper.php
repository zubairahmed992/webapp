<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\productEvent;

class ProductHelper {

    protected $dispatcher;

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
   
#------------------------------------------------------
public function find($id) {
        return $this->repo->find($id);
    }


#---------------------------------------------------
    public function findProductByTitle($name) {
        return $this->repo->findProductByTitle($name);
    }
    
    
    #---------WROK WILL BE DONE I FUTURE!!!!!!!!!!!!!!!!!!!!!!!!!!!----#
    public function getDefaultFittingAlerts($product_id)
    {
        
        // find product
        $product = $this->repo->find($product_id);
        
      if($product){
        $product_color = $product->getDisplayProductColor();
        $product_color_id = $product_color->getId();
        
         //get color size array, sizes that are available in this color 
        
        $color_sizes_array = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductColor')
                ->getSizeArray($product_color_id);
        $size_id = null;
        
         // find size id is not in param gets the first size id for this color
      
            $psize = array_shift($color_sizes_array);
            $size_id = $psize['id'];
       $product_size_id = $size_id;
        $product_size = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSize')
                ->find($product_size_id);

        //2) color & size can get an item

        if ($product_size && $product_color) {
            $product_item = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:ProductItem')
                    ->findByColorSize($product_color->getId(), $product_size->getId());
            
          return  $item_id=$product_item->getId();
        }      
        
        }//End of If   
        
         
        
        
        
    }         
 

}