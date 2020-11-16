<?php
error_reporting(0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);




include('quickbase_token.php');
include('quickbase_tables.php');

$contestant_id=strip_tags($_POST['id']);
$voter_userid=strip_tags($_POST['voter_userid']);
$voter_fullname=strip_tags($_POST['voter_fullname']);

$timer=time();



// query table Voters to check if User has voted for this contestant before

$contestant_id_field1 =  6;
$userid_id_field1 = 9;

$url3v = "https://api.quickbase.com/v1/records/query";
$ch3v = curl_init();
curl_setopt($ch3v,CURLOPT_URL, $url3v);
$useragent3v ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';

$data_params3v ='{
  "from": "'.$table_voters.'",
  "select": [
3,
    6,
    7,
    8,
    9,
    10,
11


  ],

  "where": "{'.$contestant_id_field1.'.CT.'.$contestant_id.'}AND{'.$userid_id_field1.'.CT.'.$voter_userid.'}"

}
';

curl_setopt($ch3v, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent3v",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch3v,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch3v,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch3v,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch3v,CURLOPT_POSTFIELDS, $data_params3v);
curl_setopt($ch3v,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch3v,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch3v,CURLOPT_RETURNTRANSFER, true);
$response3v = curl_exec($ch3v);

curl_close($ch3v);

//print_r($response3v);
$json3v = json_decode($response3v, true);
$countv = $json3v["metadata"]["numRecords"];

if($countv > 0){
//echo you have already Voted for this contestant
echo 4;
exit();
}












// query table contestants to get contestant Voters Count

$contestant_id_field =  3;


$url3a = "https://api.quickbase.com/v1/records/query";
$ch3a = curl_init();
curl_setopt($ch3a,CURLOPT_URL, $url3a);
$useragent3a ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';

$data_params3a ='{
  "from": "'.$table_contestants.'",
  "select": [
3,
    6,
    7,
    8,
    9,
    10,
11,
12


  ],

  "where": "{'.$contestant_id_field.'.CT.'.$contestant_id.'}"

}
';

curl_setopt($ch3a, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent3a",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch3a,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch3a,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch3a,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch3a,CURLOPT_POSTFIELDS, $data_params3a);
curl_setopt($ch3a,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch3a,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch3a,CURLOPT_RETURNTRANSFER, true);
$response3a = curl_exec($ch3a);

curl_close($ch3a);

//print_r($response3a);
$json3a = json_decode($response3a, true);
$counta = $json3a["metadata"]["numRecords"];

$c_vote= $json3a["data"][0]["12"]["value"];
$totalvote = $c_vote + 1;






//now Insert in Voters table

$url4="https://api.quickbase.com/v1/records";
$ch4 = curl_init();
curl_setopt($ch4,CURLOPT_URL, $url4);
$useragent4 ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';


$contestant_id_field = 6;
$vote_field =7;
$voter_fullname_field = 8;
$voter_userid_field = 9;
$voter_email_field = 10;
$timing_field = 11;



$post_add4='

{
  "to": "'.$table_voters.'",
  "data": [
    {


      "'.$contestant_id_field.'": {
        "value": "'.$contestant_id.'"
      },
      "'.$vote_field.'": {
        "value": "1"
      },
"'.$voter_fullname_field.'": {
        "value": "'.$voter_fullname.'"
      },
"'.$voter_userid_field.'": {
        "value": "'.$voter_userid.'"
      },
 "'.$timing_field.'": {
        "value": "'.$timer.'"
      }



    }
  ],

 "fieldsToReturn": [
    6,
    7,
    8,
    9,
    10,
11


  ]

}

';


curl_setopt($ch4, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent4",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch4,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch4,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch4,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch4,CURLOPT_POSTFIELDS, $post_add4);
curl_setopt($ch4,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch4,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch4,CURLOPT_RETURNTRANSFER, true);
$response4 = curl_exec($ch4);

curl_close($ch4);

//print_r($response4);
$json4 = json_decode($response4, true);
$statement4= $json4["metadata"]["totalNumberOfRecordsProcessed"];









// update table contestants to reflect vote counts now

$total_contestantVote_field = 12;

$url_update2 = "https://api.quickbase.com/v1/records";
$ch_update2 = curl_init();
curl_setopt($ch_update2,CURLOPT_URL, $url_update2);
$useragent_update2 ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';

$post_update2='

{
  "to": "'.$table_contestants.'",
  "data": [
    {

      "'.$total_contestantVote_field.'": {
        "value": "'.$totalvote.'"
      },

 "3": {
        "value": "'.$contestant_id.'"
      }

    }
  ],

 "fieldsToReturn": [
3,
    6,
    7,
    8,
10,
11,
12
  ]

}

';


curl_setopt($ch_update2, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent_update2",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch_update2,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch_update2,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch_update2,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch_update2,CURLOPT_POSTFIELDS, $post_update2);
curl_setopt($ch_update2,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch_update2,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch_update2,CURLOPT_RETURNTRANSFER, true);
$response_update2 = curl_exec($ch_update2);

curl_close($ch_update2);

//print_r($response_update2);
$json_update2 = json_decode($response_update2, true);

$updated_rec_id2 = $json_update2["data"][0]["3"]["value"];

// update table posts with the comments counts ends here








if($statement4){

//echo $totalvote;
echo 1;
}else{

echo 0;
}









?>


