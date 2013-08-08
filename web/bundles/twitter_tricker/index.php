<!-- 
	Name: Twitter Tricker
	Description: Twitter style vertical Ticker.
	Version : 1.00
	Author: Suresh Kumar 
	Email: skamrani2002@gmail.com
	Demo: http://lovethatfit.com
	Copyright: Copyright (c) 2013
-->
<!-- jQuery UI widget factory --> 
<script src="jquery.js" type="text/javascript"></script>
<script src="jquery.vticker-min.js" type="text/javascript"></script>  
<link href="../lovethatfit/site/css/main.css" rel="stylesheet" type="text/css" />
<style>
h1{padding:30px;color:#FFF;}
#tweet_cont{padding:50px;}
.tweet{color:#333; font-size: 0.9em; margin-bottom:30px; margin-top: 30px; margin-left: 10px;}
.tweet img{float:left;}
.tweet_txt{padding-left: 65px;}
#tweet_cont ul {margin:0;padding:0;list-style:none; }
#tweet_cont ul .tweet {background: #fff; margin-bottom:3px;clear:both;text-align:left; padding:3px;width:590px; }
#tweet_cont ul .tweet .avatar{float:left;width:48px;height:48px;margin:0px 5px 0px 0px; border:none;} 
#tweet_cont ul .tweet  h4{margin:0px 0px 0px 0px; font-size:14px; margin:0;padding:0;font-family:arial; }
#tweet_cont ul .tweet   small{padding-left:50px; font-size:13px; margin:0;padding:0;font-family:arial; font-weight:normal;}
.clearboth {clear: both;}

a {color:#3E76C2;text-decoration:none;}
a:hover {text-decoration:underline;}
</style>


<script type="text/javascript"> 
$(function(){
	$('.tweets').vTicker({ 
		speed: 500,
		pause: 3000,
		animation: 'fade',
		mousePause: true,
		showItems: 1,
		direction: 'down',
		height:85
	});
});
</script>

<div class="tweets" >

<?php 

session_start();
require_once("twitteroauth.php"); //Path to twitteroauth library
 
$twitteruser = 'LoveThatFit';
$notweets = 10;
$consumerkey = "ei0sUDMZxUCLLGOYVB6GUg";
$consumersecret = "3MugBhG2PDafRMOshiS58ShVJ5gYrA2EWVdTRX0vvvI";
$accesstoken = "67584638-ee2hjCDOgudYUoGudYOGfuBReTY8lkSyzlweSmtKK";
$accesstokensecret = "gac1LgG0YoDjAWDd3AV9BmbYB0ofxlGEwCDZpj3UyU";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
  
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
 
$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);


//echo json_encode($tweets);
?>
<ul>

<?php
$count=1; 

foreach($tweets as $ind_tweets){?>

<li id="hide_<?php echo $count;?>">
<div class="tweet">

<img src="<?php echo $ind_tweets->user->profile_image_url;?> ">
<div class="tweet_txt"><?php echo $ind_tweets->text;?></div>


<div class="clearboth"></div>
</div>
</li>
<?php 
$count++;

}?>
</ul>
          
