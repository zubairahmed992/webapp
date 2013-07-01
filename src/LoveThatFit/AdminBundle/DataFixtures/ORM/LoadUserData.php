<?php

namespace LoveThatFit\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;


class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

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
        $fixtures = Yaml::parse(file_get_contents($fixturesPath . '/user.yml'));        
        foreach ($fixtures['users'] as $user_key => $user_values) {            
            $entity = new User();            
            $entity->setFirstName(ucwords($user_values['first_name']));
            $entity->setLastName(ucwords($user_values['last_name']));
            //$entity->setPassword($user_values['password']);
            $entity->setEmail($user_values['email']);           
            $entity->setImage($user_values['image']);
            $entity->setGender($user_values['gender']);
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->setZipcode($user_values['zipcode']);
            $entity->setSalt(md5(uniqid()));
            $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($entity);
            $entity->setPassword($encoder->encodePassword($user_values['password'], $entity->getSalt()));
            $manager->persist($entity);
            $manager->flush();
            $mesurement=new Measurement();
            $firstName=$user_values['first_name'];
            $user = $this->container
                    ->get('user.helper.user')
                    ->findOneByName(ucwords($firstName));
            
            if (array_key_exists('weight', $user_values)) {
                            $mesurement->setWeight($user_values['weight']);
                        }
            if (array_key_exists('height', $user_values)) {
                            $mesurement->setHeight($user_values['height']);
                        }
            if (array_key_exists('waist', $user_values)) {
                            $mesurement->setWaist($user_values['waist']);
                        }
            if (array_key_exists('bust', $user_values)) {
                            $mesurement->setBust($user_values['bust']);
                        }
            if (array_key_exists('chest', $user_values)) {
                            $mesurement->setChest($user_values['chest']);
                        }
            if (array_key_exists('back', $user_values)) {
                            $mesurement->setBack($user_values['back']);
                        }
            if (array_key_exists('shoulder_height', $user_values)) {
                            $mesurement->setShoulderHeight($user_values['shoulder_height']);
                        } 
            if (array_key_exists('sleeve', $user_values)) {
                            $mesurement->setSleeve($user_values['sleeve']);
                        } 
            $mesurement->setUser($user);            
            $manager->persist($mesurement);
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 4; // the order in which fixtures will be loaded
    }

}

