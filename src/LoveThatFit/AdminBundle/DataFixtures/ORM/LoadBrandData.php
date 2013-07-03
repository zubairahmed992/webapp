<?php

namespace LoveThatFit\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoveThatFit\AdminBundle\Entity\Brand;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class LoadBrandData implements FixtureInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {       
        $fixturesPath = realpath(dirname(__FILE__) . '/../fixtures');
        $fixtures = Yaml::parse(file_get_contents($fixturesPath . '/brand.yml'));        
        foreach ($fixtures['brands'] as $key => $value) {
            $image = $value;
            $imagename = implode(",", $image);
            $brand = new Brand();
            $brand->deleteAllBrandImageFiles();
            $brand->copyAllBrandImageFiles();
            $brand->setName(ucwords($key));
            $brand->setImage($imagename);
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
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

}

