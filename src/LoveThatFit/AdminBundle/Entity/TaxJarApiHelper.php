<?php
namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;
use TaxJar;

class TaxJarApiHelper
{
    /**
     * Holds the TaxJar Client
     */
    protected $client;

    /**
     * Holds the TaxJar API Key
     */
    protected $api_key;

    private $container;

    private $env;

    public function __construct(Container $container)
    {
        $yaml               =   new Parser();
        $this->api_key     =   $yaml->parse(file_get_contents('../app/config/parameters.yml'))['parameters']['taxjar_api_key'];
    }

    public function createOrderSalesTax($data){
        ## calculate sales tax order
        if (isset($data) && !empty($data)) {
            //Authentication
            $this->client = TaxJar\Client::withApiKey($this->api_key);
            try {
              //Calculate sales tax for an order
              $order_taxes_taxjar = $this->client->taxForOrder([
                'from_country' => $data['from_country'],
                'from_zip' => $data['from_zip'],
                'from_state' => $data['from_state'],
                'to_country' => $data['to_country'],
                'to_zip' => $data['to_zip'],
                'to_state' => $data['to_state'],
                'amount' => $data['amount'],
                'shipping' => $data['shipping'],
                'line_items' => $data['order_line_items']
              ]);
              return $order_taxes_taxjar->amount_to_collect;
            } catch (TaxJar\Exception $e) {
                return array('error_code' => $e->getStatusCode(), 'error_message' => ''.$e->getMessage().'');
            }
        }
    }
}