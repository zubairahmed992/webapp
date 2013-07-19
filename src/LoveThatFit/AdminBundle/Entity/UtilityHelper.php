<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\Yaml\Parser;

class UtilityHelper {

    protected $conf;
    protected $pagination_limit;
    protected $pagination_default_page;

    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $this->pagination_limit = $this->conf["constants"]["pagination"]["limit"];
        $this->pagination_default_page=$this->conf["constants"]["pagination"]["default_page"];
        
    }

//--------------------------------------------------------------------------------
    public function getPaginationLimit() {
        return $this->pagination_limit;
    }
//--------------------------------------------------------------------------------
    public function getPaginationdefaultPage() {
        return $this->pagination_default_page;
    }

    
//--------------------------------------------------------------------------------

    public function getPaginationCount($page_number, $rec_count) {
        
        $pagination_count = 0;
        
        if ($page_number == 0 || $this->pagination_limit == 0) {
            $pagination_count = 0;
        } else {
            $pagination_count = ceil($rec_count / $this->pagination_limit);
        }
 
        return $pagination_count;
    }

    //--------------------------------------------------------------------------------

}