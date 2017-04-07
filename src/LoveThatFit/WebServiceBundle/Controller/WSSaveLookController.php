<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 1/16/2017
 * Time: 3:46 PM
 */

namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Doctrine\Common\Util\Debug;


class WSSaveLookController extends Controller
{
    public function saveLookAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        if (!array_key_exists('auth_token', $decoded)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        $item_ids = array_key_exists('item_ids', $decoded) ? json_decode($decoded['item_ids']) : null;

        if ($user) {
            try{
                $savedImageFile = $this->container->get('savelook.helper.savelook')->uploadUserLook( $user );
                if(is_array($item_ids))
                {
                    if($savedImageFile['isFileExists']){
                        $saveLookEntity = $this->container->get('savelook.helper.savelook')->addItem($savedImageFile['image'], $user);
                        foreach($item_ids as $id)
                        {
                            $productItems = $this->container->get('savelookItem.helper.savelookItem')->getItemById($id);
                            $this->container->get('savelookItem.helper.savelookItem')->addProductItem($saveLookEntity, $productItems);
                        }
                        $res = $this->get('webservice.helper')->response_array(true, 'Items added Successfully.', true, array(
                            'look_id' => $saveLookEntity->getId()
                        ));
                    }else{
                        $res = $this->get('webservice.helper')->response_array(false, 'Image not provided.');
                    }

                }else{
                    $res = $this->get('webservice.helper')->response_array(false, 'Item Ids are emtpy');
                }
            }catch (\Exception $e)
            {
                $res = $this->get('webservice.helper')->response_array(false, $e->getMessage());
            }

        }else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response($res);
    }

    public function getUserLooksAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $base_path      = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';


        if (!array_key_exists('auth_token', $decoded)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user){
            $user_id    = $user->getId();
            $res = $this->get('webservice.helper')->parseUserSaveLooksData( $user_id, $base_path );
        }
        else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response($res);
    }

    public function removeUserLookAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $base_path      = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';

        if (!array_key_exists('auth_token', $decoded)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user){
            $saveLookEntity = array_key_exists('look_id', $decoded) ? $this->get('savelook.helper.savelook')->findByLookId($decoded['look_id']) : null;
            if($saveLookEntity){
                $this->get('savelook.helper.savelook')->removeUserLook( $saveLookEntity, $user );
                $res = $this->get('webservice.helper')->response_array(true, 'User look removed successfully');
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'save look is not define.');
            }
        }
        else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response($res);
    }
}