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
        $brand = new Brand();
        $brand->setName('admin');
        $brand->setImage('test');
        $brand->setCreatedAt(new \DateTime('now'));
        $brand->setUpdatedAt(new \DateTime('now'));
        $brand->setDisabled(false);
        $manager->persist($brand);
        $manager->flush();
    }
    
}

