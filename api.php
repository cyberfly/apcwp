<?php

$username = "jknapc_api";
$password = "jknapc_api123";
$remote_url = 'http://localhost/jknsapc/wp-json/posts';

$staff_name =  $_POST['staff_name'];
$staff_ic =  $_POST['staff_ic'];

$data = array(
        'title'=>$staff_ic,
        'content_raw'=>$staff_name,
        'status'=>'publish',
        'type'=>'award_entries'
	    );

$postdata = json_encode($data);

// Create a stream

$opts = array(
  'http'=>array(
    'method'=>"POST",
    'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
    			"Authorization: Basic " . base64_encode("$username:$password"),
    'content'=>$postdata
  )
);

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$result = file_get_contents($remote_url, false, $context);

$result = json_decode($result);

var_dump($result);exit;

// $award_queue_id = $result->ID;

?>