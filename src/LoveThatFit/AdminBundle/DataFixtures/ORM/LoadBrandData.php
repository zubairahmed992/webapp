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
        $destination = realpath(dirname(__FILE__) . '/../../../../../web/uploads/ltf/brands');
        $source = realpath(dirname(__FILE__) . '/../../../../../web/uploads/ltf/fixtures/brands');
        foreach ($fixtures['brands'] as $key => $value) {
            $image = $value;
            $imagename = implode(",", $image);
            $brand = new Brand();
            $this->emptyDir($destination);
            $this->smartCopy($source, $destination, $options = array('folderPermission' => 0755, 'filePermission' => 0755));
            $brand->setName(ucwords($key));
            $brand->setImage($imagename);
            $brand->setCreatedAt(new \DateTime('now'));
            $brand->setUpdatedAt(new \DateTime('now'));
            $brand->setDisabled(false);
            $manager->persist($brand);
            $manager->flush();
        }
    }

    public function emptyDir($path) {
        $debugStr = '';
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_file($path . "/" . $file)) {
                        if (unlink($path . "/" . $file)) {
                            $debugStr .=$file;
                        }
                    } else {
                        if ($handle2 = opendir($path . "/" . $file)) {
                            while (false !== ($file2 = readdir($handle2))) {
                                if ($file2 != "." && $file2 != "..") {
                                    if (unlink($path . "/" . $file . "/" . $file2)) {
                                        $debugStr .=$file / $file2;
                                    }
                                }
                            }
                        }
                        if (rmdir($path . "/" . $file)) {
                            $debugStr .=$file;
                        }
                    }
                }
            }
        }
        return $debugStr;
    }

    public function smartCopy($source, $dest, $options = array('folderPermission' => 0755, 'filePermission' => 0755)) {
        $result = false;
        if (is_file($source)) {
            if ($dest[strlen($dest) - 1] == '/') {
                if (!file_exists($dest)) {
                    cmfcDirectory::makeAll($dest, $options['folderPermission'], true);
                }
                $__dest = $dest . "/" . basename($source);
            } else {
                $__dest = $dest;
            }
            $result = copy($source, $__dest);
            chmod($__dest, $options['filePermission']);
        } elseif (is_dir($source)) {
            if ($dest[strlen($dest) - 1] == '/') {
                if ($source[strlen($source) - 1] == '/') {
                    //Copy only contents 
                } else {
                    //Change parent itself and its contents 
                    $dest = $dest . basename($source);
                    @mkdir($dest);
                    chmod($dest, $options['filePermission']);
                }
            } else {
                if ($source[strlen($source) - 1] == '/') {
                    //Copy parent directory with new name and all its content 
                    @mkdir($dest, $options['folderPermission']);
                    chmod($dest, $options['filePermission']);
                } else {
                    //Copy parent directory with new name and all its content 
                    @mkdir($dest, $options['folderPermission']);
                    chmod($dest, $options['filePermission']);
                }
            }
            $dirHandle = opendir($source);
            while ($file = readdir($dirHandle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($source . "/" . $file)) {
                        $__dest = $dest . "/" . $file;
                    } else {
                        $__dest = $dest . "/" . $file;
                    }
                    $result = $this->smartCopy($source . "/" . $file, $__dest, $options);
                }
            }
            closedir($dirHandle);
        } else {
            $result = false;
        }
        return $result;
    }

    

    

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

}

