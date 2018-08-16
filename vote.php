<?php
require('config.php');

# get data from ajax
$question = filter($_POST['question']);
$reply = filter($_POST['reply']);
$vote = filter($_POST['vote']);

# search question from text
$search_question = mysqli_query($conn, "SELECT id FROM questions WHERE question = '$question'") or die(mysqli_error($conn));

$success = false;
while ($row = mysqli_fetch_array($search_question)) {

	# get id of this question
	$question_id = $row['id'];
	
	# set vote in database
	$set_vote = mysqli_query($conn, "UPDATE replies SET vote = '$vote' WHERE question = '$question_id' AND reply = '$reply'");

	if ($set_vote) {
		# vote successfully added
		$success = true;
	}	
	
}

if($success){

	$a['error'] = false;
	$a['message'] = "vote added";

	switch ($vote) {
		case 1:
			$a['color'] = 'label label-danger'; $a['value'] = 'Bad';
			break;
		case 2:
			$a['color'] = 'label label-warning'; $a['value'] = 'Incorrect';
			break;
		case 3:
			$a['color'] = 'label label-info'; $a['value'] = 'Ok';
			break;
		case 4:
			$a['color'] = 'label label-primary'; $a['value'] = 'Good';
			break;
		case 5:
			$a['color'] = 'label label-success'; $a['value'] = 'Excellent';
			break;
		
		default:
			$a['color'] = 'label label-default'; $a['value'] = '?';
			break;
	}

}
else{
	$a['error'] = true;
	$a['message'] = "reply not found";
}

print_r(json_encode($a));
exit;
?>