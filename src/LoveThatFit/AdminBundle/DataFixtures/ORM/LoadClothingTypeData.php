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
       
        $fixturesPath = realpath(dirname(__FILE__). '/../fixtures');
        $fixtures     = Yaml::parse(file_get_contents($fixturesPath. '/clothingtype.yml'));
        foreach($fixtures['clothing_type'] as $key=>$values) {  
         $entity = new ClothingType();
         $target=$values;         
         $strs = implode(",", $target);   
         $entity->setName(ucwords($key));
         $entity->setTarget($strs);
         $entity->setCreatedAt(new \DateTime('now'));
         $entity->setUpdatedAt(new \DateTime('now'));
         $entity->setDisabled(false);         
         $manager->persist($entity);
         $manager->flush();
        }        
       
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
    
}

