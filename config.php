<?php
# Local data
session_start();

# Project conf
define('PROJECT', 'fake-ai');

# Debug conf
$debug = false;

# Language conf
define('LANGUAGE', lang());

# External libraries
include_once("twitter/twitteroauth.php");

# Database connection
$hostname = ""; // your hostname (normally localhost)
$data_username = ""; // database username
$data_password = ""; // database password
$data_basename = ""; // database name
$conn = mysqli_connect("".$hostname."","".$data_username."","".$data_password."");  
mysqli_select_db($conn, "".$data_basename."") or die(mysqli_error($conn));

# Twitter
define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');

### Useful functions ###

# Function to convert object to array
function objectToArray($d) {
	if (is_object($d)) {
	# Gets the properties of the given object
	# with get_object_vars function
	$d = get_object_vars($d);
	}
	
	if (is_array($d)) {
	/*
	* Return array converted to object
	* Using __FUNCTION__ (Magic constant)
	* for recursive call
	*/
	return array_map(__FUNCTION__, $d);
	}
	else {
	# Return array
	return $d;
	}
}

# Function to create all combination of elements
function depth_picker($arr, $temp_string, &$collect) {
  if ($temp_string != "") 
      $collect []= $temp_string;

  for ($i=0; $i<sizeof($arr);$i++) {
      $arrcopy = $arr;
      $elem = array_splice($arrcopy, $i, 1); # removes and returns the i'th element
      if (sizeof($arrcopy) > 0) {
          depth_picker($arrcopy, $temp_string ." " . $elem[0], $collect);
      } else {
          $collect []= $temp_string. " " . $elem[0];
      }   
  }   
}

# Function to filter text
function filter($text) {
  global $conn;

  if(get_magic_quotes_gpc() == 0) {
    $text = addslashes($text);
  }

  $text = strip_tags($text);
  $escape = mysqli_real_escape_string($conn, $text);
  return $text;

}

# Function to easily add new data to database
function add($question, $reply, $action = false){
  global $conn;

  # filter input
  $question = filter($question);
  $reply = filter($reply);

  # verify if question is empty
  if($question == "") return false;

  # verify if reply and action are empty
  if($action == false && reply == "") return false;

  # search question in questions
	$select = mysqli_query($conn, "SELECT * FROM questions WHERE question = '$question'") or die(mysqli_error($conn));

  if (mysqli_num_rows($select) == 0) {
	  # if not found, create new
	  $insert = mysqli_query($conn, "INSERT INTO questions (question) VALUES ('$question')") or die(mysqli_error($conn));
  }

  # re-search question in questions
  $select = mysqli_query($conn, "SELECT * FROM questions WHERE question = '$question'") or die(mysqli_error($conn));

  # not found
  if (mysqli_num_rows($select) == 0) {
    return false;
  }

	# select id  
	$row = mysqli_fetch_array($select);
	$question_id = $row['id'];

  # search reply in replies
	if ($action != false) {
		$select = mysqli_query($conn, "SELECT * FROM replies WHERE reply = '$reply' AND action = '$action' AND question = '$question_id'") or die(mysqli_error($conn));
	}
	else{
		$select = mysqli_query($conn, "SELECT * FROM replies WHERE reply = '$reply' AND question = '$question_id'") or die(mysqli_error($conn));
	}

  # insert these new informations
	if (mysqli_num_rows($select) == 0) {
		
		if ($action != false) {
			$insert = mysqli_query($conn, "INSERT INTO replies (question, reply, action) VALUES ('$question_id','$reply','$action')") or die(mysqli_error($conn));
		}
		else{
			$insert = mysqli_query($conn, "INSERT INTO replies (question, reply) VALUES ('$question_id','$reply')") or die(mysqli_error($conn));
		}

		return true;
	}
	else{
    # Found
    # I don't have to add these informations 
    return false;
	}

}

# Function to remove mentions from tweets
# Explode tweet in words
# and remove @ from words
function removementions($str){
  $str = explode(' ', $str);

  foreach ($str as $word) {

    if (strpos($word,'@') !== false) {
      // Ops. It's a mention!
    }
    else{
	    $s .= $word." ";
    }
  
  }

  return $s;
}

# Function to remove hashtag from tweets
function removehashtags($str){
  return str_replace("#", "", $str);
}

# Function to remove links from a string
function removelinks($str){
  $pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
  $replacement = "";
  $str = preg_replace($pattern, $replacement, $str);

  return $str;
}

# Function to clean tweets using other functions
function twittercleaner($str){
  return removehashtags(removelinks(removementions($str)));
}

# Function to get current browser language 
function lang($mode = 'min'){
  $l = explode(";", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
  $l = explode(",", $l[0]);

  if ($mode == 'min') { return $l[1]; } return $l[0];
}
 
# Function to count words
# Return an array with words and counts
function countwords($array){
  $count = array();
  $aw = array(); # all words

  foreach ($array as $sentence) {
    $sentence = $sentence['reply'];
    $words = explode(" ", $sentence);
    foreach ($words as $word) {
      $count[$word] = $count[$word] + 1;
      array_push($aw, $word);
    }
  }

  # array[words] contains all words
  # array[count] contains all count referenced to each word in words array
  return array('words' => $aw, 'count' => $count);
}

# Function to return best words
# Search words with best precision
function bestwords($replies, $words, $count, $precision = 3){
  # more $precision -> less precision
  $accepted = array();

  for ($i = 0; $i < 100; $i++) { 
    $perc = 100-$i;

    # $perc : 100 = x : count(replies)
    $x = ($perc * count($replies)) / 100;

    foreach ($words as $word) {

      if ($count[$word] > $x && (!in_array($word, $accepted))) {
        array_push($accepted, $word);
      }

    }

    if (count($accepted) > $precision) {
      break;
    }

  }

  return $accepted;
}

# Function to return an array with accepted replies
function acceptreplies($words, $replies){
  $newreplies = array();

  for ($i = 0; $i < count($words); $i++) {
    $x = count($words) - $i;

    foreach ($replies as $reply) {
      $y = 0;

      foreach ($words as $word) {

        if($reply && $word && strpos($reply['reply'], $word) !== false) {
          $y++;
        }

      }

      if ($y == $x) {
        array_push($newreplies, $reply);
      }

    }

    if (count($newreplies) > 0) break;

  }

  return $newreplies;
}

# Function to deeply search
# This function is used when the question isn't in the database
# The function strip question in words and search words in the database
# then find best reply in the simil-question found
function research($s){
  global $conn;

  $s = trim(strtolower($s));

  # get questions from database
  $like_search = "%".str_replace(" ", "%", $s)."%";
  $get_questions = mysqli_query($conn, "SELECT id, question FROM questions WHERE question LIKE '$like_search'") or die(mysqli_error($conn));

  # control question found
  $questions = array();
  $temp = array();
  while ($row = mysqli_fetch_array($get_questions)) {
    $temp['question'] = $row['question'];
    $temp['id'] = $row['id'];
    
    array_push($questions, $temp);  
  }

  # if no question found
  # search part of the question
  if(count($questions) == 0){

    # divide question in words
    $words = explode(" ", $s);

    # create all combination of words
    $search_strings = array();
    depth_picker($words, "", $search_strings);

    # calculate 75% of words number
    # 75 : 100 = X : count($words)
    # --> X = 75*count($words)/100
    $minimum = (75*count($words))/100;
    # only other possible questions with more than
    # $minimum value will be considered

    # search other possible questions
    foreach ($search_strings as $ss) {

      # remove useless white-spaces
      $ss = trim($ss);

      if( count(explode(" ", $ss)) >= $minimum ){
        
        # get questions from database
        $like_search = "%".str_replace(" ", "%", $ss)."%";
        $get_questions = mysqli_query($conn, "SELECT id, question FROM questions WHERE question LIKE '$like_search'") or die(mysqli_error($conn));

        # control question found
        while ($row = mysqli_fetch_array($get_questions)) {
          $temp['question'] = $row['question'];
          $temp['id'] = $row['id'];
          
          array_push($questions, $temp);  
        }

      }

    }

  }

  # get replies for each question
  $replies = array();
  $temp2 = array();
  foreach ($questions as $question) {

    $question_id = $question['id'];
    $get_replies = mysqli_query($conn, "SELECT id, reply, action FROM replies WHERE question = '$question_id'") or die(mysqli_error($conn));

    while ($row = mysqli_fetch_array($get_replies)) {

      $temp2['reply'] = $row['reply'];
      $temp2['action'] = $row['action'];
      $temp2['id'] = $row['id'];
      array_push($replies, $temp2);
    
    }

  }

  # select replies
  $countwords = countwords($replies); 
  $replies = acceptreplies(bestwords($replies, $countwords['words'], $countwords['count'], rand(3,6)), $replies); # best replies ever

  # if nothing is found, reply "nothing"
  if (count($replies) == 0) { return array('reply' => '', 'action' => false); }

  # random selection
  $rand = rand(0, count($replies) - 1);

  # return the reply w/ action
  return array('reply' => $replies[$rand]['reply'], 'action' => $replies[$rand]['action']);
}

# Function to print reply
function reply($reply, $action = false, $enable_vote = false){

  $array['reply'] = $reply;
  $array['action'] = ($action == false || $action == 0 || $action == "0" || $action == "") ? false : intval($action);
  $array['enable_vote'] = $enable_vote;

  print_r(json_encode($array));
  exit;

}

?>