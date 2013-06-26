<?php

namespace LoveThatFit\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use LoveThatFit\AdminBundle\Entity\Brand;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class LoadSizeChart extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

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
        $fixtures = Yaml::parse(file_get_contents($fixturesPath . '/size_chart.yml'));

        foreach ($fixtures['size_charts'] as $brand_key => $brand_values) {

            $brand = $this->container
                    ->get('admin.helper.brand')
                    ->findOneByName($brand_key);

            foreach ($brand_values as $gender_key => $gender_values) {
                foreach ($gender_values as $body_type_key => $body_type_values) {
                    foreach ($body_type_values as $clothing_type_key => $clothing_type_values) {
                        foreach ($clothing_type_values as $size_key => $size_values) {
                         
                        $entity = new SizeChart();
                        $entity->setBrand($brand);
                        $entity->setGender($gender_key);
                        $entity->setBodytype($body_type_key);
                        $entity->setTarget($clothing_type_key);
                        $entity->setTitle($size_key);
                        
                        $entity->setBack('14.5');
                        $entity->setBust('20');
                        $entity->setChest('20');
                        $entity->setDisabled(false);
                        $entity->getHip('20');
                        $entity->setInseam('20');
                        $entity->setNeck('20');
                        $entity->setOutseam('20');
                        $entity->setSleeve('20');
                        $entity->setThigh('20');
                        $entity->setWaist('20');
                        $manager->persist($entity);
                        $manager->flush();
                        
                        }
                    }
                        
                }
               
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 3; // the order in which fixtures will be loaded
    }

}

