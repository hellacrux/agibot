<?php

require_once('twitteroauth.php');

define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
define('ACCESS_TOKEN', '');
define('ACCESS_TOKEN_SECRET', '');

$twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
$twitter->host = "http://search.twitter.com/";

$msgs = array(
  "This is a message that would be sent to USERNAME http://example.com #hashtag",
  "This is another possible message for USERNAME http://example.com #hashtag"
);

$username = "ShellisPrepared";
$needle = "whatever text to search for";
$search = $twitter->get('search', array('q' => $needle, 'rpp' => rand(1, 1)));

$isFirst = true;
$last_response_id = file_get_contents('last_response.txt');

$twitter->host = "https://api.twitter.com/1/";
foreach($search->results as $tweet) {
  if (($tweet->id > $last_response_id) && (strpos($tweet->text, "@" . $username) === false) && (strpos($tweet->text, "RT") === false)) {
    $user = '@' . $tweet->from_user;
    $status =  preg_replace('/USERNAME/', $user, $msgs[array_rand($msgs)]);
    $twitter->post('statuses/update', array('status' => $status));
    if ($isFirst) {
      $myFile = "last_response.txt";
      $fh = fopen($myFile, 'w') or die("can't open file");
      $stringData = $tweet->id;
      //$stringData = '1';
      fwrite($fh, $stringData);
      fclose($fh);
      $isFirst = false;
    }
  }
}

?>
