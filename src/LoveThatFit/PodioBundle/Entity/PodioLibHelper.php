<?php
namespace LoveThatFit\PodioBundle\Entity;

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

class PodioLibHelper
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
        $env                = $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['podio_enviorment'];
        if($env == 'prod') {
            $this->env = "podio_prod_credentials";
        } else if($env == 'v3stack') {
            $this->env = "podio_v3stack_credentials";
        } else if($env == 'qa') {
            $this->env = "podio_qa_credentials";
        } else if($env == 'dev') {
            $this->env = "podio_dev_credentials";
        } else if($env == 'v3qa') {
            $this->env = "podio_v3qa_credentials";
        } else if($env == 'v3staging') {
            $this->env = "podio_v3staging_credentials";
        } else {
            $this->env  = "podio_local_credentials";
        }
        $this->container = $container;
    }

    //-------------------------------------------------------
    public function saveOrderPodio($order_podio)
    {        
        $yaml = new Parser();
        $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/PodioBundle/Resources/config/config_orders.yml'));        
        //Podio API Access Variables
        $this->client_id = $parse[$this->env]["client_id"];
        $this->client_secret = $parse[$this->env]["client_secret"];
        $this->app_id = $parse[$this->env]["app_id"];
        $this->app_token = $parse[$this->env]["app_token"];        
        //echo "<pre>"; print_r($order_podio);
        //Authenticate the Podio API
        Podio::setup($this->client_id, $this->client_secret);
        Podio::authenticate_with_app($this->app_id, $this->app_token);
        if (Podio::is_authenticated()) {
            //Podio::set_debug(true);
            //print "You were already authenticated and no authentication is needed.<br>"; 
            // Second approach - Create field collection with different fields
            $fields = new PodioItemFieldCollection(array(
              new PodioTextItemField(array("external_id" => "order-number", "values" => "".$order_podio['order_number']."")),
              new PodioTextItemField(array("external_id" => "title", "values" => "".$order_podio['billing_first_name']." ".$order_podio['billing_last_name']."")),              
              new PodioTextItemField(array("external_id" => "order-date", "values" => "".$order_podio['order_date']."")),
              new PodioTextItemField(array("external_id" => "order-amount", "values" => "".$order_podio['order_amount']."")),
              new PodioTextItemField(array("external_id" => "item-amount", "values" => "".$order_podio['item_amount']."")),
              new PodioTextItemField(array("external_id" => "quantity-item", "values" => "".$order_podio['quantity_item']."")),
              new PodioTextItemField(array("external_id" => "brand-name", "values" => "".$order_podio['brand_name']."")),
              new PodioTextItemField(array("external_id" => "item-description", "values" => "".$order_podio['item_description']."")),
              new PodioTextItemField(array("external_id" => "style-id", "values" => "".$order_podio['style_id']."")),
              new PodioTextItemField(array("external_id" => "charge-to", "values" => "".$order_podio['credit_card']."")),
              new PodioTextItemField(array("external_id" => "payment-method", "values" => "".$order_podio['payment_method']."")),
              new PodioTextItemField(array("external_id" => "braintree-status", "values" => "".$order_podio['transaction_status']."")),                            
              new PodioTextItemField(array("external_id" => "shipping-address", "values" => "".$order_podio['full_address_shipping']."")),
              new PodioTextItemField(array("external_id" => "member-email", "values" => "".$order_podio['user_email']."")),
              new PodioTextItemField(array("external_id" => "order-tax-amt", "values" => "".$order_podio['sales_tax']."")),
            ));
            // Create item and attach fields
            $item = new PodioItem(array(
              "app" => new PodioApp(intval($this->app_id)),
              "fields" => $fields
            ));         
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
    
}