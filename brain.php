<?php
include_once("config.php");

# get question from ajax
$question = trim(filter($_POST['question']));

# if question is empty, no reply
if ($question == "") { reply(""); }

# search question in questions
$query_question = mysqli_query($conn, "SELECT * FROM questions WHERE question = '$question'") or die(mysqli_error($conn));

# question not found in old questions
if (mysqli_num_rows($query_question) == 0) {
    # call an automatic function
    # the function search deeply the reply
    $r = research($question);
    reply($r['reply'], $r['action'], false);
}

$i = 0;
$all = array();
while ($row_questions = mysqli_fetch_array($query_question)) {
 
    $question_id = filter($row_questions['id']);

    $no_research = (filter($row_questions['no_research']) == 1) ? true : false; # use research?
    

    $query_reply = mysqli_query($conn, "SELECT * FROM replies WHERE question = '$question_id'") or die(mysqli_error($conn));

    while ($row_replies = mysqli_fetch_array($query_reply)) {

        if($row_replies['reply'] == "" && $row_replies['action'] == null) break;
        
        $all[$i]['reply'] = $row_replies['reply'];
        $all[$i]['action'] = $row_replies['action'];

        
        $i++;

    }

}


# if research is enabled
# reply with research function
if (!$no_research){
    $r = research($question);
    reply($r['reply'], $r['action'], false);
}
# else reply directly with reply found in database
else {
    $rand = rand(0, ($i-1)); # reply random
    reply(ucfirst(htmlspecialchars_decode($all[$rand]['reply'])), $all[$rand]['action'], true);
}

exit;
?>