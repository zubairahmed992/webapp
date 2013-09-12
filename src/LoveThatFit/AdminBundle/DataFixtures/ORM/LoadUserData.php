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
        $dirname = 'users';
        $filename = realpath(dirname(__FILE__) . '/../../../../../web/uploads/ltf').'/'.$dirname;         
        $fixturesPath = realpath(dirname(__FILE__) . '/../fixtures');
        $fixtures = Yaml::parse(file_get_contents($fixturesPath . '/user.yml'));
        $destination = $filename;     
        if(!file_exists($filename));
      {
        @mkdir($filename,0777);  
      }
        $source = realpath(dirname(__FILE__) . '/../../../../../web/bundles/lovethatfit/miscellaneous/fixtures/users');
        $this->deleteAllUserFiles($destination);
        foreach ($fixtures['users'] as $user_key => $user_values) {             
            $entity = new User();            
            $entity->setFirstName(ucwords($user_values['first_name']));
            $entity->setLastName(ucwords($user_values['last_name']));                        
            $entity->setEmail($user_values['email']);           
            $entity->setImage($user_values['image']);
            $entity->setAvatar($user_values['avater']);
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
            if (array_key_exists('hip', $user_values)) {
                            $mesurement->setHip($user_values['hip']);
                        }             
            $mesurement->setUser($user);            
            $manager->persist($mesurement);
            $manager->flush();            
            $userid = $this->container
                    ->get('user.helper.user')
                    ->findMaxUserId();
            foreach($userid as $usersid)
            {              
              @mkdir($destination.'/'.$usersid['id']);
              $current_destination=$destination.'/'.$usersid['id'];
              $current_source = $source . '/1';  
              $this->copyAllUserImageFiles($current_source, $current_destination, $options = array('folderPermission' => 0755, 'filePermission' => 0755));                            
              //rename($current_destination.'/avatar.jpg',$current_destination.'/'.$usersid['id'].'_avatar.jpg' );
              //rename($current_destination.'/original.jpg',$current_destination.'/'.$usersid['id'].'_original.jpg' );
              //rename($current_destination.'/cropped.jpg',$current_destination.'/'.$usersid['id'].'_cropped.jpg' );              
            }             
            
        }
    }
    
    public function deleteAllUserFiles($path) {
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
                                    if (@unlink($path . "/" . $file . "/" . $file2)) {
                                        $debugStr .=@($file / $file2);
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

    public function copyAllUserImageFiles($source, $dest, $options = array('folderPermission' => 0755, 'filePermission' => 0755)) {
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
                    
                    $result = $this->copyAllUserImageFiles($source . "/" . $file, $__dest, $options);
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
        return 4; // the order in which fixtures will be loaded
    }

}

