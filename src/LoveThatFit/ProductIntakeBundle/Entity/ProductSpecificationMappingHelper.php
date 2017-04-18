<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class ProductSpecificationMappingHelper {

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
    //-------------------------Create New Brand--------------------------------------------   

    public function createNew() {
        $class = $this->class;
        $c = new $class();
        $c->setCreatedAt(new \DateTime('now'));
        $c->setDisabled(false);
        return  $c;        
    }

//--------------------------Save Brand----------------------------------------------------------------

    public function save($entity) {       
        $this->em->persist($entity);
        $this->em->flush();        
    }

//----------------------- Update--------------------------------
    public function update($entity) {  
        $this->em->persist($entity);
        $this->em->flush();        
            return array(
                'message' => 'Product mapping has been Updated!',
                'message_type' => 'success',
                'success' => true,
            );
    }    
//------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        $title = $entity->getTitle();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array(
                'message' => 'The Mapping ' . $title . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array(
                'message' => 'Mapping not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

//----------------------Find Mappings By ID----------------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------Find All Mappings---------------------------------------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
//----------------------Find By title----------------------------------------------------------------
    public function findOneByTitle($title) {
        return $this->repo->findOneByTitle($title);
    }
    #--------------------------------------------
    
    public function getAllMappingArray() {
        return $this->repo->allMappingArray();
    }
    
    #--------------------------------------------
  public function toArray(){
      return array(
          'title' =>  $this->getTitle(),
          'brand' =>  $this->getBrand(),
          'gender' =>  $this->getGender(),
          'clothing_type' =>  $this->getClothingType(),
          'description' =>  $this->getDescription(),
          'mapping_json' =>  $this->getMappingJson(),
          'created_at' =>  $this->getCreatedAt(),
          'disabled' =>  $this->getDisabled(),
      );
  }
  
  #---------------------- CSV File Downlod Links
  public function  csvDownloads($csv_files){
      foreach ( $csv_files as $k => $v ){
          $csv_file  = $this->find($v->getId()); 
          $csv_file_path[$v->getId()] = $csv_file->getWebPath();
        }
        return $csv_file_path;
  }
  
  #-------------------------------
  
  public function parseCSVupload($request){
      $csv_array = $this->csv_to_array($request->files->get('csv_file'));
      
      $product_specs_mapping = $this->container->get('product_intake.product_specification_mapping')->find($request->request->get('sel_mapping'));
      $map = json_decode($product_specs_mapping->getMappingJson(), true);        
      #-------------->
        $parsed_data = $this->get('admin.helper.product.specification')->getStructure();
        $parsed_data['gender'] = $product_specs_mapping->getGender();
        $parsed_data['size_title_type'] = $product_specs_mapping->getSizeTitleType();
        $parsed_data=  $this->parse_by_map($map, $csv_array, $parsed_data);
      #-------------->
       
        
        #--------------------- calculate fit model measrements & ratio
        if (!array_key_exists('sizes', $parsed_data)) {
            return new Response('Measurements & sizes are missing');
        }
        #------------------------ Grade Rule calculation + sorting of sizes
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $prev_size_key = null;
        $ordered_sizes = array();
        $size_no = 0;
        foreach ($size_specs['sizes'][$parsed_data['gender'] == 'm' ? 'man' : 'woman'][$parsed_data['size_title_type']] as $size_key => $size_title) {
            if (array_key_exists($size_key, $parsed_data['sizes'])) {
                $ordered_sizes['sizes'][$size_key] = $parsed_data['sizes'][$size_key];                
            }
        }
        $parsed_data['sizes'] = $ordered_sizes['sizes'];   
        #---------> Save to DB
        $specs=$this->get('pi.product_specification')->createNew(
                $product_specs_mapping->getTitle(),
                $product_specs_mapping->getDescription(),
                json_encode($parsed_data));
         $specs->setSpecFileName('csv_spec_' . $specs->getId() . '.csv');
         $this->container->get('pi.product_specification')->save($specs);
         move_uploaded_file($_FILES["csv_file"]["tmp_name"], $specs->getAbsolutePath());
            
        $this->get('session')->setFlash('success', 'New Product specification added!');
        return $this->redirect($this->generateUrl('product_intake_product_specs_show', array('id' => $specs->getId())));    
  }
#----------------------------  

  private function parse_by_map($map, $csv_array, $parsed_data){
       foreach ($map as $specs_k => $specs_v) {
            if ($specs_k != 'formula') {
                if (is_array($specs_v) || is_object($specs_v)) {
                    foreach ($specs_v as $size_key => $fit_points) {
                        foreach ($fit_points as $fit_pont_key => $fit_model_measurement) {
                            $coordins = $this->extracts_coordinates($fit_model_measurement);
                            $fmm_value = $this->fraction_to_number(floatval($csv_array[$coordins['r']][$coordins['c']]));
                            $original_value = $fmm_value;
                            #~~~~~~>convert to measuring unit
                            if(array_key_exists('measuring_unit', $map) && $map['measuring_unit'] == 'centimeter'){
                                $fmm_value = $fmm_value * 0.393700787;                            
                            }
                            $unit_converted_value = $fmm_value;
                            #~~~~~~>calculate formula
                            if(array_key_exists('formula', $map)){
                                $fmm_value =  $this->upply_formula($map['formula'], $fit_pont_key, $fmm_value);                            
                            }
                            #----------------------* parsed data array calculate fit modle values for fit model size
                            $parsed_data[$specs_k][$size_key][$fit_pont_key] = array('garment_dimension' => $fmm_value, 'stretch_percentage' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'max_calc' => 0, 'min_actual' => 0, 'max_actual' => 0, 'ideal_low' => 0, 'ideal_high' => 0, 'fit_model' => 0, 'prev_garment_dimension' => 0, 'grade_rule' => 0, 'no' => 0,
                                'original_value'=>$original_value,
                                'unit_converted_value'=>$unit_converted_value,
                                );
                        }
                    }
                } else {#----------------------* if not related to measurements add as a field
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']]:$specs_v;                    
                }
            }
        }
      return $parsed_data;
  }
  
#----------------------------
    private function csv_to_array($file){
        $csv_array = array();        
        $i = 0;
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                for ($j = 0; $j < count($row); $j++) {
                    $csv_array[$i][$j] = $row[$j];
                }
                $i++;
            }
        }
        return $csv_array;
    }
#----------------------------
    private function upply_formula($formula, $fit_pont_key, $fmm_value) {
        if (array_key_exists($fit_pont_key, $formula)) {
            $st = str_replace("x",$fmm_value,$formula[$fit_pont_key]);
            $p = eval('return '.$st.';');
            return $p;
        } else {
            return $fmm_value;
        }
    }
    #----------------------------
    private function fraction_to_number($raw_value) {
        if (strpos($raw_value, '/')) {
            $raw_exploded = explode(' ', $raw_value);
            $frac = explode('/', $raw_exploded[1]);
            return (intval($raw_exploded[0]) + (intval($frac[0]) / intval($frac[1])));
        } else {
            return floatval($raw_value);
        }
    }

#----------------------------------------------------------------    
    private function extracts_coordinates($str) {
        $cdns = explode(',', $str);
        $c = array();
        if (count($cdns) > 1) {
            $c['r'] = intval($cdns[0]);
            $c['c'] = intval($cdns[1]);
        }
        return $c;
    }
  
}
