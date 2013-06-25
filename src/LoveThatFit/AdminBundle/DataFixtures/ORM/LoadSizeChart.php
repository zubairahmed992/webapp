<?php

namespace LoveThatFit\AdminBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use LoveThatFit\AdminBundle\Entity\Brand;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class LoadSizeChart implements FixtureInterface{

     /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /*
        $entity = new SizeChart();
        $entity->setBack('14.5');
        $entity->setBodytype('Regular');
        $brand=new Brand();
        $brand->setName('Gap');
        $brand->setImage('Gap');
        $brand->setCreatedAt(new \DateTime('now'));
        $brand->setUpdatedAt(new \DateTime('now'));
        $brand->setDisabled(false);
        $entity->setBrand($brand);                
        $entity->setBust('20');
        $entity->setChest('20');
        $entity->setDisabled(false);
        $entity->setGender('F');
        $entity->getHip('20');
        $entity->setInseam('20');
        $entity->setNeck('20');
        $entity->setOutseam('20');
        $entity->setSleeve('20');
        $entity->setTarget('Top');
        $entity->setThigh('20');
        $entity->setTitle('00');
        $entity->setWaist('20'); 
        $manager->persist($entity);
        $manager->persist($brand);
        $manager->flush();
         */
    }   
}

