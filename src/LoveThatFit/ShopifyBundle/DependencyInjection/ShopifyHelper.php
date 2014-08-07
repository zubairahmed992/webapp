<?php
namespace LoveThatFit\ShopifyBundle\DependencyInjection;
use Symfony\Component\Yaml\Parser;

class ShopifyHelper {

    protected $conf;
    //--------------------------------------------------------------------
    public function __construct() {
        
    }
    public function appSpecs(){
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('../src/LoveThatFit/ShopifyBundle/Resources/config/shopify_app.yml'));
        
    }
    //--------------------------------------------------------------------
    
    public function appScopes(){
        $specs = $this->appSpecs();
        return $specs['app_scopes'];
    }
    //--------------------------------------------------------------------
     public function appWebHooks(){
        $specs = $this->appSpecs();
        return $specs['webhooks'];
    }
}

?>
