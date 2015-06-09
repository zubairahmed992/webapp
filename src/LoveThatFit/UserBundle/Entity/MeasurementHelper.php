<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class MeasurementHelper {

    /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;
    private $container; 
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,  Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;        
        $this->repo = $em->getRepository($class);
    }

    #-------------------------------------------------------------------------
    
    public function find($id) {
        return $this->repo->find($id);
    }

    #-------------------------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }
#-------------------------------------------------------------------------
    public function findMaxUserId() {
        return $this->repo->findMaxUserId();
    }
    #-------------------------------------------------------------------------
    public function saveMeasurement(Measurement $measurement) {
        $measurement->setUpdatedAt(new \DateTime('now'));        
        $this->em->persist($measurement);
        $this->em->flush();
    }   
    #-----------------------------------------------------------
Public function updateWithParams($measurement, $params){
    if(array_key_exists('shoulder_height', $params) && $params['shoulder_height']){$measurement->setShoulderHeight($params['shoulder_height']);}
    if(array_key_exists('hip_height', $params) && $params['hip_height']){$measurement->setHipHeight($params['hip_height']);}      
    
    if(array_key_exists('marker_json', $params) && $params['marker_json']){          
        $pred_measurements = $this->container->get('user.marker.helper')->getPredictedMeasurement($params['marker_json']);                
        foreach ($pred_measurements as $k=>$v) {
            $measurement->setProperty($k,$v);
        }
    }
    $this->saveMeasurement($measurement);  
}
#----------------------------------------------
  /*  public function saveVerticalPositonMeasurement(Measurement $measurement) {
        $measurement->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($measurement);
        $this->em->flush();
    }*/
    
   /* public function savehorizontalMeasurement(Measurement $measurement) {
        $measurement->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($measurement);
        $this->em->flush();
    }*/

#----------------------Code For Value Retaing ------------------------------------------------------------------------# 
   /* public function measurementRetain($measurement) {
       // return $measurement->getMeasurmentArray();
        
      $top_size_chart = $measurement->getTopFittingSizeChart();
        $bottom_size_chart = $measurement->getBottomFittingSizeChart();
        $dress_size_chart = $measurement->getDressFittingSizeChart();

        #---Getting the Top Size Chart --------#
        if ($top_size_chart) {

            $retaining_array['topSizeChartId'] = $top_size_chart->getId();
            $top_brand = $top_size_chart->getBrand();
            $retaining_array['top_brand_id'] = $top_brand->getId();
            
        } else {
            $retaining_array['top_brand_id'] = Null;
            $retaining_array['topSizeChartId'] = Null;
        }

        #---Getting The Bottom Size Chart--------#   
        if ($bottom_size_chart) {
            $retaining_array['bottomSizeChartId'] = $bottom_size_chart->getId();
            $bottom_brand = $bottom_size_chart->getBrand();
            $retaining_array['bottom_brand_id'] = $bottom_brand->getId();
        } else {
            $retaining_array['bottom_brand_id'] = Null;
            $retaining_array['bottomSizeChartId'] = Null;
        }

        #---Getting The Dress Size Chart-----------#
        if ($dress_size_chart) {
            $retaining_array['dressSizeChartId'] = $dress_size_chart->getID();
            $dress_brand = $dress_size_chart->getBrand();
            $retaining_array['dress_brand_id'] = $dress_brand->getId();
        } else {
            $retaining_array['dress_brand_id'] = Null;
            $retaining_array['dressSizeChartId'] = Null;
        }
        return $retaining_array;
    }*/
#----------------Get  Bust measurment Range------------------------------------#   
  /*  public function getBustMeasurementRange($bra_num) {
        $yaml = new Parser();
        $bustMeasurement = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $bustRange = $bustMeasurement["Bust_Measurement"];
        foreach($bustRange as $bust){
            $size_cup=$bust['size'].$bust['cup'];
            if($size_cup==$bra_num){
                return $bust['average'];
            }
        
            
            
        }
        //return array('bustRange'=>$bustRange);
    }*/
    #----------------------------------------------------------
    
    public function evaluateRegistration($user, $data){
        $measurement = $user->getMeasurement();
        $m_measure_ele = array('sleeve', 'neck', 'waist', 'inseam');
        $f_measure_ele = array('bust', 'waist', 'hip', 'bra_numbers', 'bra_letters');
        $f_size_chart_ele = array('top_brand','top_size','bottom_brand','bottom_size','dress_brand','dress_size');
        $m_size_chart_ele = array('top_brand','top_size','bottom_brand','bottom_size');
        
        if (is_array($data)){
            if($this->check_elements_value($data, $m_measure_ele)) {

            }   
            
        
        
        }
    }
    #~~~~~~~~~~~~~~~~~~~~
    private function check_elements_value($data, $elements){
        $all_available=true;
        foreach ($elements as $e){
            if(!array_key_exists($data, $e) && strlen($data[$e])==0){
                $all_available = false;
            }
        }        
        return $all_available;
    }
    
}