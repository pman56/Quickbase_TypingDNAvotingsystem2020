<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);


if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

$nounce = intval( $_POST['nounce'] );
if ( $nounce == '' ) {
        echo "nounce protection cannot be empty";
        exit();
    }



$email_address = $_POST['email_address'];
$typingPattern_email = $_POST['typingPattern_email'];

//check if Admin has inserted API Keys and Secret


//include quickbase token
include('quickbase_token.php');
include('quickbase_tables.php');



$url = "https://api.quickbase.com/v1/records/query";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
$useragent ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';


$post ='{
  "from": "'.$table_typingdnaKeys.'",
  "select": [
3,
    6,
7
  ]

}
';


curl_setopt($ch, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

curl_close($ch);

//print_r($response);
$json = json_decode($response, true);

$num_field = $json["metadata"]["numFields"];
$num_rec = $json["metadata"]["numRecords"];
$count = $num_rec;
if($count == 0 ) {
	        echo 1;
// echo TypingDNA API KEY not inserted by Admin AT Quickbase
exit(); 
}

//Get and set typing DNA api Keys
$apiKey= $json['data'][0]['6']['value'];
$apiSecret = $json['data'][0]['7']['value'];









//Get Hashed Email Address for the Registered User and use it as TypingDNA userID

$url2 = "https://api.quickbase.com/v1/records/query";
$ch2 = curl_init();
curl_setopt($ch2,CURLOPT_URL, $url2);
$useragent2 ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';

$email_query_field = 7;

$post ='{
  "from": "'.$table_members.'",
  "select": [
3,
    6,
7,
8,
9,
10,
11

  ],

  "where": "{'.$email_query_field.'.CT.'.$email_address.'}"

}
';


curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent2",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch2,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch2,CURLOPT_POSTFIELDS, $post);
curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch2,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
$response2 = curl_exec($ch2);

curl_close($ch2);

//print_r($response2);
$json2 = json_decode($response2, true);

$num_field2 = $json2["metadata"]["numFields"];
$num_rec2 = $json2["metadata"]["numRecords"];
$count2 = $num_rec;
if($count2 == 0 ) {
	        echo 4;
// echo there is not Hashed Email found for this user to be used for typingdna Userid
exit(); 
}

//Get the hashed email

$hashed_email = $json2['data'][0]['11']['value'];

$typingDNA_Userid = $hashed_email;


// Send  Users Typing Pattern to typingDna API
$base_url1 = 'https://api.typingdna.com/%s/%s';


$typingDna_Email_Pattern = $typingPattern_email;

$ch1 = curl_init(sprintf($base_url1, 'save', $typingDNA_Userid));
$data1 = array('tp' => $typingDna_Email_Pattern);
curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch1, CURLOPT_HEADER, FALSE);
curl_setopt($ch1, CURLOPT_USERPWD, $apiKey . ":" . $apiSecret);
curl_setopt($ch1, CURLOPT_TIMEOUT, 30);
curl_setopt($ch1, CURLOPT_POST, TRUE);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query($data1) . "\n");

$response1 = curl_exec($ch1);
curl_close($ch1);
//var_dump($response1);



$json = json_decode($response1, true);
$message = $json["message"];
$success = $json["success"];
$status = $json["status"];



        if($success == 1){

//echo "susccess";
echo 2;
exit();

}


if($success == 0){
//echo "failed";
echo 3;
exit(); 

}





	
}



?>



