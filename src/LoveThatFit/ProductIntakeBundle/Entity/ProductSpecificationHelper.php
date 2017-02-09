<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class ProductSpecificationHelper {

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

     private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container) {
         $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
    //-------------------------Create --------------------------------------------   
public function getNew() {
        $class = $this->class;
        $c = new $class();
        $c->setCreatedAt(new \DateTime('now'));        
        return  $c;        
    }
    
    public function createNew($title, $desc, $json) {
        $class = $this->class;
        $c = new $class();
        $c->setTitle($title);
        $c->setDescription($desc);
        $c->setSpecsJson($json);
        $c->setCreatedAt(new \DateTime('now'));        
        $c->setUpdatedAt(new \DateTime('now'));        
        $this->save($c);
        return  $c;        
    }

//--------------------------Save ----------------------------------------------------------------

    public function save($entity) {       
        $this->em->persist($entity);
        $this->em->flush();        
    }

    
//------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        $title = $entity->getTitle();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array(
                'message' => 'The product specs for ' . $title . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array(
                'message' => 'Product specs not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    
    public function update($entity) {       
        $title = $entity->getTitle();
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();
            return array(
                'message' => 'The product specs for ' . $title . ' has been Updated!',
                'message_type' => 'success',
                'success' => true,
            );
    }
    
//----------------------Find ProductSpecifications By ID----------------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------Find All ProductSpecifications---------------------------------------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
#----------------------Find By title----------------------------------------------------------------
    public function findOneByTitle($title) {
        return $this->repo->findOneByTitle($title);
    }
#----------------------    
    public function calculateWithFitModel($sizes, $fit_model){
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model->getMeasurementJson(), true);

        foreach ($sizes[$fit_model->getSize()] as $fit_point => $measure) {
            $fit_model_ratio[$fit_point] = ($fit_model_fit_points[$fit_point] / $measure['garment_dimension']);
        }
        foreach ($sizes as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                $fit_model = $fpv['garment_dimension'] * $fit_model_ratio[$fpk];
                $grade_rule = $sizes[$size][$fpk]['grade_rule'];
                $sizes[$size][$fpk]['fit_model'] = number_format($fit_model, 2, '.', '');
                $sizes[$size][$fpk]['max_calc'] = number_format($fit_model + (2.5 * $grade_rule), 2, '.', '');
                $sizes[$size][$fpk]['min_calc'] = number_format($fit_model - (2.5 * $grade_rule), 2, '.', '');
                $sizes[$size][$fpk]['ideal_high'] = number_format($fit_model + $grade_rule, 2, '.', '');
                $sizes[$size][$fpk]['ideal_low'] = number_format($fit_model - $grade_rule, 2, '.', '');
                $sizes[$size][$fpk]['max_actual'] = $sizes[$size][$fpk]['max_calc'];
                $sizes[$size][$fpk]['min_actual'] = $sizes[$size][$fpk]['min_calc'];
                #$sizes[$size][$fpk]['ratio'] = $fit_model_ratio[$fpk];
            }
        }
        return $sizes;     
    }
    #----------------------    
    public function calculateWithStretch($specs){
        
        $horizontal=array('clothing_type', 'brand', 'name', 'gender', 'description', 'styling_type', 'hem_length', 'neckline', 'sleeve_styling', 'rise', 'stretch_type', 'horizontal_stretch', 'vertical_stretch', 'fabric_weight', 'layering', 'structural_detail', 'fit_type', 'fit_priority', 'fabric_content', 'garment_detail', 'size_title_type', 'retailer_reference', 'control_number', 'colors');
        
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                
            }
        }
        return $specs;     
    }
    
}
