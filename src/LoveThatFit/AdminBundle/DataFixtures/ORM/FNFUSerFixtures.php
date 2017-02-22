<?php
namespace LoveThatFit\AdminBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\FNFUser;

class FNFUSerFixtures extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;
    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->container->get('user.helper.user')->find('2071');
        $fnfUser = new FNFUser();

        $fnfUser->setDiscount('100');
        $fnfUser->setUsers( $user );

        $manager->persist( $fnfUser );

        $user2 = $this->container->get('user.helper.user')->find('2563');
        $fnfUser2 = new FNFUser();

        $fnfUser2->setDiscount('100');
        $fnfUser2->setUsers( $user2 );

        $manager->persist( $fnfUser2 );

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}