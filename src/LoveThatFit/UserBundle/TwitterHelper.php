<?php

namespace LoveThatFit\UserBundle;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class TwitterHelper {

  
    //--------------------------------------------------------------------
    public function __construct() {
     
        
    }
     #----------------------Twitter Work-----------------------------------------------# 
    
    public function buildBaseString($baseURI, $method, $params) {
    $r = array();
    ksort($params);
    foreach($params as $key=>$value){
        $r[] = "$key=" . rawurlencode($value);
    }
    return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}

public function buildAuthorizationHeader($oauth) {
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach($oauth as $key=>$value)
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    $r .= implode(', ', $values);
    return $r;
}

public function twitter_latest(){
//$url = "http://api.twitter.com/1.1/statuses/user_timeline.json";
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

$yaml = new Parser();
        $twitter_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $consumer_key = $twitter_constants["consumer_key"];
        $consumer_secret = $twitter_constants["consumer_secret"];
        $oauth_access_token= $twitter_constants["oauth_access_token"];
        $oauth_access_token_secret= $twitter_constants["oauth_access_token_secret"];
        $screen_name=$twitter_constants["screen_name"];
//$consumer_key="9GBx1IchmgsTC404I52w";
//$consumer_secret="HC6fR9dZYl8zqzHNx36eCvlWvJ0HCmPzMJr3Pqj88";
//$oauth_access_token="1667582922-O5JzsoBc7fmfUR2jYVHrnCWsIiOWDDO38uXwpQk";
//$oauth_access_token_secret="sa0TN4vVjQtU82o09VatP68oLISZjkXd3erZnGk";

//$screen_name="LoveThatFit";
//$items=15;
$oauth = array( 'screen_name' => 'LoveThatFit',
                'count' => 10,
                'oauth_consumer_key' => $consumer_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $oauth_access_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0',
                );

$base_info = $this->buildBaseString($url, 'GET', $oauth);
$composite_key = rawurlencode($consumer_secret).'&'.rawurlencode($oauth_access_token_secret);
$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
$oauth['oauth_signature'] = $oauth_signature;

// Make Requests
//$header = array($this->buildAuthorizationHeader($oauth), 'Expect:');
//$options = array( CURLOPT_HTTPHEADER => $header,
                  //CURLOPT_POSTFIELDS => $postfields,
//                  CURLOPT_HEADER => false,
  //                 CURLOPT_URL => $url.'?screen_name=LoveThatFit&count=10', 
    //              CURLOPT_RETURNTRANSFER => true,
      //            CURLOPT_SSL_VERIFYPEER => false);



//$feed = curl_init();
//curl_setopt_array($feed, $options);
//$json = curl_exec($feed);
//curl_close($feed);

//$twitter_data = json_decode($json);

//return $twitter_data;
}

#---------------------------CAll Tweet--------------------------------------#


    
}

