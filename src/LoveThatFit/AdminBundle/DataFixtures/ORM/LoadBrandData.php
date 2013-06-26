<?php
namespace LoveThatFit\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoveThatFit\AdminBundle\Entity\Brand;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class LoadBrandData implements FixtureInterface{

     /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
      
        $fixturesPath = realpath(dirname(__FILE__). '/../fixtures');
        $fixtures     = Yaml::parse(file_get_contents($fixturesPath. '/brand.yml'));
        
        foreach ($fixtures['Brands'] as $key => $value) {
              $brand = new Brand();
        $brand->setName($key);
        $brand->setImage('image1');
        $brand->setCreatedAt(new \DateTime('now'));
        $brand->setUpdatedAt(new \DateTime('now'));
        $brand->setDisabled(false);
        $manager->persist($brand);
        $manager->flush();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}

