<?php
namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Event\BrandSpecificationEvent;
use Symfony\Component\HttpFoundation\Response;

class BrandSpecificationHelper {

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

    //---------------------------------------------------------------------   

    public function createNew() {
        $class = $this->class;
        $brand = new $class();
        return $brand;
    }

//-------------------------------------------------------

    public function save($entity) {                   
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Brand Specification succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );       
    }

    //-------------------------------------------------------

    public function update($entity) {            
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Brand Specification '. ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );        
    }

//-------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();

            return array('brands' => $entity,
                'message' => 'The Brand specification '. ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('brands' => $entity,
                'message' => 'Brand specification not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

//-------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
   #-----------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
    
  public function findByBrand($entity)
  {
     return $this->repo->findByBrand($entity);   
  }

 public function brandDetailArray($data,$entity){
    
        // $data=$request->request->all
        // ();                     
            if(isset($data['brand_specification']['gender'])){$entity->setGender($this->getJsonForFields($data['brand_specification']['gender']));}else{$entity->setGender('null');}
            if(isset($data['brand_specification']['female_fit_type'])){$entity->setFemaleFitType($this->getJsonForFields($data['brand_specification']['female_fit_type']));}else{$entity->setFemaleFitType('null');}
            if(isset($data['brand_specification']['male_fit_type'])){$entity->setMaleFitType($this->getJsonForFields($data['brand_specification']['male_fit_type']));}else{$entity->setMaleFitType('null');}
            if(isset($data['brand_specification']['female_size_title_type'])){$entity->setFemaleSizeTitleType($this->getJsonForFields($data['brand_specification']['female_size_title_type']));}else{$entity->setFemaleSizeTitleType('null');}
            if(isset($data['brand_specification']['male_size_title_type'])){$entity->setMaleSizeTitleType($this->getJsonForFields($data['brand_specification']['male_size_title_type']));}else{$entity->setMaleSizeTitleType('null');}
            if(isset($data['brand_specification']['male_chest'])){$entity->setMaleChest($this->getJsonForFields($data['brand_specification']['male_chest']));}else{$entity->setMaleChest('null');}
            if(isset($data['brand_specification']['male_shirt'])){$entity->setMaleShirt($this->getJsonForFields($data['brand_specification']['male_shirt']));}else{$entity->setMaleShirt('null');}
            if(isset($data['brand_specification']['male_letter'])){$entity->setMaleLetter($this->getJsonForFields($data['brand_specification']['male_letter']));}else{$entity->setMaleLetter('null');}
            if(isset($data['brand_specification']['male_waist'])){$entity->setMaleWaist($this->getJsonForFields($data['brand_specification']['male_waist']));}else{$entity->setMaleWaist('null');}
            if(isset($data['brand_specification']['male_neck'])){$entity->setMaleNeck($this->getJsonForFields($data['brand_specification']['male_neck']));}else{$entity->setMaleNeck('null');}
            
            if(isset($data['brand_specification']['female_number'])){$entity->setFemaleNumber($this->getJsonForFields($data['brand_specification']['female_number']));}else{$entity->setFemaleNumber('null');}
            if(isset($data['brand_specification']['female_letter'])){$entity->setFemaleLetter($this->getJsonForFields($data['brand_specification']['female_letter']));}else{$entity->setFemaleLetter('null');}
            if(isset($data['brand_specification']['female_waist'])){$entity->setFemaleWaist($this->getJsonForFields($data['brand_specification']['female_waist']));}else{$entity->setFemaleWaist('null');}
            if(isset($data['brand_specification']['female_bra'])){$entity->setFemaleBra($this->getJsonForFields($data['brand_specification']['female_bra']));}else{$entity->setFemaleBra('null');}
            return $this->save($entity);
}   

public function brandSpscificationDetailArray($data,$entity){
        // $data=$request->request->all();                     
            if(isset($data['brand_specification']['gender'])){$entity->setGender($this->getJsonForFields($data['brand_specification']['gender']));}else{$entity->setGender('null');}
            if(isset($data['brand_specification']['female_fit_type'])){$entity->setFemaleFitType($this->getJsonForFields($data['brand_specification']['female_fit_type']));}else{$entity->setFemaleFitType('null');}
            if(isset($data['brand_specification']['male_fit_type'])){$entity->setMaleFitType($this->getJsonForFields($data['brand_specification']['male_fit_type']));}else{$entity->setMaleFitType('null');}
            if(isset($data['brand_specification']['female_size_title_type'])){$entity->setFemaleSizeTitleType($this->getJsonForFields($data['brand_specification']['female_size_title_type']));}else{$entity->setFemaleSizeTitleType('null');}
            if(isset($data['brand_specification']['male_size_title_type'])){$entity->setMaleSizeTitleType($this->getJsonForFields($data['brand_specification']['male_size_title_type']));}else{$entity->setMaleSizeTitleType('null');}
            if(isset($data['brand_specification']['male_chest'])){$entity->setMaleChest($this->getJsonForFields($data['brand_specification']['male_chest']));}else{$entity->setMaleChest('null');}
            if(isset($data['brand_specification']['male_shirt'])){$entity->setMaleShirt($this->getJsonForFields($data['brand_specification']['male_shirt']));}else{$entity->setMaleShirt('null');}
            if(isset($data['brand_specification']['male_letter'])){$entity->setMaleLetter($this->getJsonForFields($data['brand_specification']['male_letter']));}else{$entity->setMaleLetter('null');}
            if(isset($data['brand_specification']['male_waist'])){$entity->setMaleWaist($this->getJsonForFields($data['brand_specification']['male_waist']));}else{$entity->setMaleWaist('null');}
            if(isset($data['brand_specification']['male_neck'])){$entity->setMaleNeck($this->getJsonForFields($data['brand_specification']['male_neck']));}else{$entity->setMaleNeck('null');}
            
            if(isset($data['brand_specification']['female_number'])){$entity->setFemaleNumber($this->getJsonForFields($data['brand_specification']['female_number']));}else{$entity->setFemaleNumber('null');}
            if(isset($data['brand_specification']['female_letter'])){$entity->setFemaleLetter($this->getJsonForFields($data['brand_specification']['female_letter']));}else{$entity->setFemaleLetter('null');}
            if(isset($data['brand_specification']['female_waist'])){$entity->setFemaleWaist($this->getJsonForFields($data['brand_specification']['female_waist']));}else{$entity->setFemaleWaist('null');}
            if(isset($data['brand_specification']['female_bra'])){$entity->setFemaleBra($this->getJsonForFields($data['brand_specification']['female_bra']));}else{$entity->setFemaleBra('null');}
            return $this->update($entity);
}  
    
    //-------------------------------------------------------

    
private function getJsonForFields($fields){
        $f=array();
        foreach ($fields as $key => $value) {
        $f[$key]=$value;
        }
        return json_encode($f);
        
    }
#-------------------------Get Brand Detail base on id-------------------------#    
   public function getBrandSpecifications($id){
       return $this->repo->getBrandSpecifications($id);
   }
    
}