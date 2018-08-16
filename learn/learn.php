<?php
include_once("../config.php");

# if twitter keys are empty
if(CONSUMER_KEY == "" || CONSUMER_SECRET == ""){
	$result['success'] = false;
	$result['recursive'] = false;
	$result['info']['error'] = "twitter keys are empty";
	print_r(json_encode($result));
	exit;
}

# connect to twitter app
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
$ok = true;
$error = false;
$cont1 = 0;
$cont2 = 0;
$result = array();
$result['recursive'] = true;

while($ok && $cont1 < 100){

$c = 'abcdefghijklmnopqrstuvwxyz0123456789';

$t = objectToArray($connection->get('search/tweets', array('q' => $c[rand(0, strlen($c) - 1)], 'lang' => LANGUAGE, 'count' => 100)));

if (isset($t["errors"])) { $ok = false; $error = true; break; }

foreach ($t['statuses'] as $reply) {
$cont1++;

if (strpos($reply['text'],'RT') !== false) { /* it is a retweet -> nothing to do */ }else{
if (strpos(strtolower($reply['text']),'tweet') !== false) { /* it contains 'tweet', nothing..*/ }else{
if (strpos(strtolower($reply['text']),'twitter') !== false) { /* it contains 'tweet', nothing..*/ }else{

	if (!is_null($reply['in_reply_to_status_id'])) {
		
		$tweet = objectToArray($connection->get('statuses/show', array('id' => $reply['in_reply_to_status_id']/*, 'trim_user' => true*/)));

		if (isset($tweet["errors"])) { $ok = false; $error = true; break; }

		if (trim(twittercleaner($tweet['text'])) != "" && trim(twittercleaner($reply['text'])) != "") {
		
		if (strpos($tweet['text'],'RT') !== false) { /* it is a retweet -> nothing to do */ }else{

			if($debug){
				$result['debug'][$h++] = "question: ".twittercleaner($tweet['text'])." - reply: ".twittercleaner($reply['text'])."<br>";
			}
			
			# if question contains "?" is a real question, maybe
			if( strpos($tweet['text'], "?") === false ){
				# nothing to do
			}else{

				add(trim(twittercleaner($tweet['text'])), trim(twittercleaner($reply['text'])));
				$cont2++;

			}

		}

		}


	}

}}}
}


}

$result['info']['tweets'] = $cont1;
$result['info']['replies'] = $cont2;
$result['info']['message'] = PROJECT." learned ".$cont2." new replies of ".$cont1." searched thing";
$result['success'] = ($error) ? false : true;
if($error) $result['info']['error'] = "the teacher is absent";

print_r(json_encode($result));
exit;
?>