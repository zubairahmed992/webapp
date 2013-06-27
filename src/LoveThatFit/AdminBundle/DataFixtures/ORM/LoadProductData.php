<?php

namespace LoveThatFit\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\Product;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;


class LoadProductData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

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
        $fixtures = Yaml::parse(file_get_contents($fixturesPath . '/product.yml'));        
        foreach ($fixtures['products'] as $product_key => $product_values) {
            $brand = $this->container
                    ->get('admin.helper.brand')
                    ->findOneByName($product_key);
                foreach ($product_values as $clothing_type_key => $clothing_type_values) {                    
                    $clothing_type = $this->container
                    ->get('admin.helper.ClothingType')
                    ->findclothingTypeByName($clothing_type_key);                    
                        $entity = new Product();
                        $entity->setBrand($brand);
                        $entity->setClothingType($clothing_type);                        
                        $entity->setName($clothing_type_values['name']);
                        $entity->setDescription($clothing_type_values['description']);
                        $entity->setAdjustment($clothing_type_values['adjustment']);
                        $entity->setGender(ucwords($clothing_type_values['gender']));                        
                        $entity->setDisabled(false);
                        $entity->setCreatedAt(new \DateTime('now'));
                        $entity->setUpdatedAt(new \DateTime('now'));                        
                        $manager->persist($entity);
                        $manager->flush();                           
        }
    }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 5; // the order in which fixtures will be loaded
    }

}

