<?php
namespace LoveThatFit\AdminBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class LoadClothingTypeData implements FixtureInterface{

     /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /*
        try {
        $value =Yaml::parse(@file_get_contents("../fixtures/clothingtype.yml"));
        if (is_array($value))
        {
        foreach($value['clothing_type'] as $key=>$values) {  
         $entity = new ClothingType();
         $name = $key;
         $target=$values;         
         $strs = implode(",", $target);   
         $entity->setName($name);
         $entity->setTarget($strs);
         $entity->setCreatedAt(new \DateTime('now'));
         $entity->setUpdatedAt(new \DateTime('now'));
         $entity->setDisabled(false);         
         $manager->persist($entity);
         $manager->flush();
        }
        }  
        }catch (ParseException $e) {
           printf("Unable to parse the YAML string: %s", $e->getMessage());
    }*/
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
    
}

