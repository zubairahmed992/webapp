<?php
namespace LoveThatFit\UserBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;
use Podio;
use PodioItem;
use PodioApp;
use PodioItemFieldCollection;
use PodioTextItemField;
use PodioEmailItemField;
use PodioEmbed;
use PodioEmbedItemField;
use PodioSearchResult;

class PodioApiHelper
{

    /**
     * Holds the Podio App Client ID
     */
    protected $client_id;

    /**
     * Holds the Podio App Client Secret
     */
    protected $client_secret;

    /**
     * Holds the Podio App ID
     */
    protected $app_id;

    /**
     * Holds the Podio App Token
     */
    protected $app_token;

    private $container;

    private $env;

    public function __construct(Container $container)
    {
        $yaml               = new Parser();
        $env                = $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['enviorment'];
        if($env == 'prod') {
            $this->env = "podio_prod_credentials";
        } else if($env == 'dev') {
            $this->env = "podio_dev_credentials";
        } else {
            $this->env  = "podio_local_credentials";
        }

        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/config.yml'));
        
        //Podio API Access Variables
        $this->client_id = $parse[$this->env]["client_id"];
        $this->client_secret = $parse[$this->env]["client_secret"];
        $this->app_id = $parse[$this->env]["app_id"];
        $this->app_token = $parse[$this->env]["app_token"];
        $this->container = $container;
    }

    //-------------------------------------------------------
    public function saveUserPodio($user_podio)
    {        
        //Authenticate the Podio API
        Podio::setup($this->client_id, $this->client_secret);
        Podio::authenticate_with_app($this->app_id, $this->app_token);
        if (Podio::is_authenticated()) {
            //Podio::set_debug(true);
            //print "You were already authenticated and no authentication is needed.<br>"; 

            //admin protal url of member profile
            $url_member_profile = ''.$user_podio['base_path'].'admin/user/'.$user_podio['id'].'/show';
            //$view_profile = '<a href="'.$url_member_profile.'" target="_blank">View Profile</a>';

            // Second approach - Create field collection with different fields
            $fields = new PodioItemFieldCollection(array(
              new PodioEmailItemField(array("external_id" => "member-email")), 
              new PodioTextItemField(array("external_id" => "title", "values" => "".$user_podio['id']."")),
              new PodioTextItemField(array("external_id" => "activation-date", "values" => "".$user_podio['created_at']."")),
              new PodioTextItemField(array("external_id" => "zip-code", "values" => "".$user_podio['zipcode']."")),
              new PodioTextItemField(array("external_id" => "date-of-birth", "values" => "".$user_podio['birth_date']."")),
              new PodioTextItemField(array("external_id" => "gender", "values" => "".$user_podio['gender']."")),
              new PodioEmbedItemField(array("external_id" => "admin-portal-url")),
              new PodioTextItemField(array("external_id" => "member-calibrated", "values" => "No"))
            ));

            // Create item and attach fields
            $item = new PodioItem(array(
              "app" => new PodioApp(intval($this->app_id)),
              "fields" => $fields
            ));
            
            //Attached member profile url here
            $embed = PodioEmbed::create(array("url" => $url_member_profile));
            $item->fields["admin-portal-url"]->values = $embed;

            //Attached memeber email
            $item->fields["member-email"]->values = array("type" => "other","value" => "".$user_podio['email']."");

            try {
              // Save item
              $new_item_placeholder = $item->save();
              $item->item_id = $new_item_placeholder->item_id;
              return $item->item_id;
            } catch (PodioError $e) {
              return $e;
            }            
        }    
    }


    public function updateUserPodio($user_id)
    {
      $podioUser = $this->container->get('user.helper.podio')->findPodioUserByUserId($user_id);      
      $member_id = $podioUser[0]['member_id'];
      $podio_id = $podioUser[0]['podio_id'];
      //Authenticate the Podio API
      Podio::setup($this->client_id, $this->client_secret);
      Podio::authenticate_with_app($this->app_id, $this->app_token);
      if (Podio::is_authenticated()) {
          //if item id exists then update the value of member calibrated
          if($podio_id) {
              $update_item = PodioItem::update_values( $podio_id, $attributes = array("member-calibrated" => "Yes") );
              if($update_item['revision']) {
                  return 1;
              } else {
                  return 0;
              }
          }
      }
    }
}