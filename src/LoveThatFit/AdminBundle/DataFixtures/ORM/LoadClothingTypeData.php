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
            foreach ($values as $gender_type_key => $gender_type_values) {
         $entity = new ClothingType();
         $entity->setName($gender_type_key);
         $entity->setTarget($gender_type_values['target']);
         $entity->setGender($key);
         $entity->setCreatedAt(new \DateTime('now'));
         $entity->setUpdatedAt(new \DateTime('now'));
         $entity->setDisabled(false);         
         $manager->persist($entity);
         $manager->flush();
        }
        }
    }
    
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
    
}

