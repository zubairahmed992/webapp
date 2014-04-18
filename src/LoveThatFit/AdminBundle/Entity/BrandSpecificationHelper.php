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
        // $data=$request->request->all();                     
            if(isset($data['brand_specification']['gender'])){$entity->setGender($this->getJsonForFields($data['brand_specification']['gender']));}else{$entity->setGender('null');}
            if(isset($data['brand_specification']['fit_type'])){$entity->setFitType($this->getJsonForFields($data['brand_specification']['fit_type']));}else{$entity->setFitType('null');}
            if(isset($data['brand_specification']['size_title_type'])){$entity->setSizeTitleType($this->getJsonForFields($data['brand_specification']['size_title_type']));}else{$entity->setSizeTitleType('null');}
            if(isset($data['brand_specification']['male_numbers'])){$entity->setMaleNumbers($this->getJsonForFields($data['brand_specification']['male_numbers']));}else{$entity->setMaleNumbers('null');}
            if(isset($data['brand_specification']['male_letters'])){$entity->setMaleLetters($this->getJsonForFields($data['brand_specification']['male_letters']));}else{$entity->setMaleLetters('null');}
            if(isset($data['brand_specification']['male_waists'])){$entity->setMaleWaists($this->getJsonForFields($data['brand_specification']['male_waists']));}else{$entity->setMaleWaists('null');}
            if(isset($data['brand_specification']['female_numbers'])){$entity->setFemaleNumbers($this->getJsonForFields($data['brand_specification']['female_numbers']));}else{$entity->setFemaleNumbers('null');}
            if(isset($data['brand_specification']['female_letters'])){$entity->setFemaleLetters($this->getJsonForFields($data['brand_specification']['female_letters']));}else{$entity->setFemaleLetters('null');}
            if(isset($data['brand_specification']['female_waists'])){$entity->setFemaleWaists($this->getJsonForFields($data['brand_specification']['female_waists']));}else{$entity->setFemaleWaists('null');}
            return $this->save($entity);
}   

public function brandSpscificationDetailArray($data,$entity){
        // $data=$request->request->all();                     
            if(isset($data['brand_specification']['gender'])){$entity->setGender($this->getJsonForFields($data['brand_specification']['gender']));}else{$entity->setGender('null');}
            if(isset($data['brand_specification']['fit_type'])){$entity->setFitType($this->getJsonForFields($data['brand_specification']['fit_type']));}else{$entity->setFitType('null');}
            if(isset($data['brand_specification']['size_title_type'])){$entity->setSizeTitleType($this->getJsonForFields($data['brand_specification']['size_title_type']));}else{$entity->setSizeTitleType('null');}
            if(isset($data['brand_specification']['male_numbers'])){$entity->setMaleNumbers($this->getJsonForFields($data['brand_specification']['male_numbers']));}else{$entity->setMaleNumbers('null');}
            if(isset($data['brand_specification']['male_letters'])){$entity->setMaleLetters($this->getJsonForFields($data['brand_specification']['male_letters']));}else{$entity->setMaleLetters('null');}
            if(isset($data['brand_specification']['male_waists'])){$entity->setMaleWaists($this->getJsonForFields($data['brand_specification']['male_waists']));}else{$entity->setMaleWaists('null');}
            if(isset($data['brand_specification']['female_numbers'])){$entity->setFemaleNumbers($this->getJsonForFields($data['brand_specification']['female_numbers']));}else{$entity->setFemaleNumbers('null');}
            if(isset($data['brand_specification']['female_letters'])){$entity->setFemaleLetters($this->getJsonForFields($data['brand_specification']['female_letters']));}else{$entity->setFemaleLetters('null');}
            if(isset($data['brand_specification']['female_waists'])){$entity->setFemaleWaists($this->getJsonForFields($data['brand_specification']['female_waists']));}else{$entity->setFemaleWaists('null');}
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
#-------------------------Bet Brand Detail base on id-------------------------#    
   public function getBrandSpecifications($id){
       return $this->repo->getBrandSpecifications($id);
   }
    
}