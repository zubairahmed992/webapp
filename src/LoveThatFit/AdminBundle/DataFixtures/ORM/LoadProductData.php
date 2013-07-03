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
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
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
                        ->get('admin.helper.clothingtype')
                        ->findOneByName($clothing_type_key);
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
                $product_new = $this->container
                        ->get('admin.helper.product')
                        ->findProductByTitle($clothing_type_values['name']);
                foreach ($clothing_type_values['product_color'] as $product_color_key => $product_color_values) {
                    $productcolor = new ProductColor();
                    $productcolor->deleteAllProductImageFiles();
                    $productcolor->copyAllProductImageFiles();
                    $productcolor->setProduct($product_new);
                    $productcolor->setTitle($product_color_values['title']);
                    $productcolor->setImage($product_color_values['image']);
                    $productcolor->setPattern($product_color_values['pattern']);
                    $manager->persist($productcolor);
                    $manager->flush();
                    if (array_key_exists('default', $product_color_values)) {
                        $entity->setDisplayProductColor($productcolor);
                        $manager->persist($entity);
                        $manager->flush();
                    }
                }
                foreach ($clothing_type_values['product_sizes'] as $product_sizes_key => $product_size_values) {
                    $productsize = new ProductSize();
                    $productsize->setProduct($product_new);
                    $productsize->setTitle($product_size_values['title']);
                    $productsize->setInseamMin($product_size_values['inseam_min']);
                    $productsize->setInseamMax($product_size_values['inseam_max']);
                    $productsize->setOutseamMin($product_size_values['outseam_min']);
                    $productsize->setOutseamMax($product_size_values['outseam_max']);
                    $productsize->setHipMin($product_size_values['hip_min']);
                    $productsize->setHipMax($product_size_values['hip_max']);
                    $productsize->setBustMin($product_size_values['bust_min']);
                    $productsize->setBustMax($product_size_values['bust_max']);
                    $productsize->setBackMin($product_size_values['back_min']);
                    $productsize->setBackMax($product_size_values['back_max']);
                    $productsize->setWaistMin($product_size_values['waist_min']);
                    $productsize->setWaistMax($product_size_values['waist_max']);
                    $productsize->setChestMin($product_size_values['chest_min']);
                    $productsize->setChestMax($product_size_values['chest_max']);
                    $productsize->setNeckMin($product_size_values['neck_min']);
                    $productsize->setNeckMax($product_size_values['neck_max']);
                    $productsize->setSleeveMin($product_size_values['sleeve_min']);
                    $productsize->setSleeveMax($product_size_values['sleeve_max']);
                    $productsize->setThighMin($product_size_values['thigh_min']);
                    $productsize->setThighMax($product_size_values['thigh_max']);
                    $productsize->setHem($product_size_values['hem']);
                    $productsize->setLength($product_size_values['length']);
                    $manager->persist($productsize);
                    $manager->flush();
                }
                foreach ($clothing_type_values['product_item'] as $product_item_key => $product_item_values) {
                    $productid = $product_new->getId();
                    $productsize = $this->container
                            ->get('admin.helper.productsizes')
                            ->findSizeByProductTitle($product_item_values['size_title'],$productid);
                    $productcolor = $this->container
                            ->get('admin.helper.productcolor')
                            ->findColorByProductTitle($product_item_values['product_color_title'],$productid);
                    $productitem = new ProductItem();
                    $productitem->setProduct($product_new);
                    $productitem->setProductColor($productcolor);
                    $productitem->setProductSize($productsize);
                    $productitem->setLineNumber($product_item_values['size_title']);
                    $productitem->setImage($product_item_values['image']);
                    $manager->persist($productitem);
                    $manager->flush();
                }
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

