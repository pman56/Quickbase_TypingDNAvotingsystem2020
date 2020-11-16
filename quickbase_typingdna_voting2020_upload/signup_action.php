<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);


if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {


$password = strip_tags($_POST['password']);
$fullname = strip_tags($_POST['fullname']);
$email = strip_tags($_POST['email']);

$timer = time();

// honey pot spambots
$emailaddress_pot =$_POST['emailaddress_pot'];
if($emailaddress_pot !=''){
//spamboot detected.
//Redirect the user to google site

echo "<script>
window.setTimeout(function() {
    window.location.href = 'https://google.com';
}, 1000);
</script><br><br>";

exit();
}

if ($password == ''){
echo "<div class='alert alert-danger' id='alerts_reg'><font color=red>password is empty</font></div>";
exit();
}

if ($fullname == ''){
echo "<div class='alert alert-danger' id='alerts_reg'><font color=red>fullname Name is empty</font></div>";
exit();
}

if ($email == ''){
echo "<div class='alert alert-danger' id='alerts_reg'><font color=red>Email Address is empty</font></div>";
exit();
}

$em= filter_var($email, FILTER_VALIDATE_EMAIL);
if (!$em){
echo "<div class='alert alert-danger' id='alerts_reg'><font color=red>Email Address is Invalid</font></div>";
exit();
}









//include quickbase token
include('quickbase_token.php');
include('quickbase_tables.php');


// check if Email Address already exist in users table in quickbase.

$url1 = "https://api.quickbase.com/v1/records/query";
$ch1 = curl_init();
curl_setopt($ch1,CURLOPT_URL, $url1);
$useragent1 ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';


$email_field= 7;


$post1 ='{
  "from": "'.$table_members.'",
  "select": [
3,
    7
  ],

  "where": "{'.$email_field.'.CT.'.$email.'}"

}
';


curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent1",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch1,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch1,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch1,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch1,CURLOPT_POSTFIELDS, $post1);
curl_setopt($ch1,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch1,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch1,CURLOPT_RETURNTRANSFER, true);
$response1 = curl_exec($ch1);

curl_close($ch1);

//print_r($response1);
$json = json_decode($response1, true);

$num_field1 = $json["metadata"]["numFields"];
$num_rec1 = $json["metadata"]["numRecords"];

        if($num_rec1 > 0){

// echo "email exist.";
echo "<br><div class='alert alert-danger'  id='alertdata_uploadfiles'><b><font color=red><b></b>This Email is already taken</font></b><br>";
exit();

}else{
// echo "Email is available";

}





//hash password before sending it to Quickbase...
$options = array("cost"=>4);
$hashpass = password_hash($password,PASSWORD_BCRYPT,$options);



//hash Email and use it typingDNA USERID before sending it to Quickbase...
//$options = array("cost"=>4);
$hashemail = password_hash($email,PASSWORD_BCRYPT,$options);


// now insert into members table

$fullname_field =6;
$emailing_field =7;
$password_field =8;
$timing_field = 9;
$typingdna_Enrollment_field =10;
$typingDNA_Hash_Email_field = 11;

$url_update = "https://api.quickbase.com/v1/records";
$ch_update = curl_init();
curl_setopt($ch_update,CURLOPT_URL, $url_update);
$useragent_update ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';

$post_updates='

{
  "to": "'.$table_members.'",
  "data": [
    {


      "'.$fullname_field.'": {
        "value": "'.$fullname.'"
      },

 "'.$emailing_field.'": {
        "value": "'.$email.'"
      },

 "'.$password_field.'": {
        "value": "'.$hashpass.'"
      },

"'.$timing_field.'": {
        "value": "'.$timer.'"
      },

"'.$typingdna_Enrollment_field.'": {
        "value": "0"
      },

"'.$typingDNA_Hash_Email_field.'": {
        "value": "'.$hashemail.'"
      }






    }
  ],

 "fieldsToReturn": [
3,
    6,
    7,
    8,
9,
10
  ]

}

';


curl_setopt($ch_update, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent_update",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch_update,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch_update,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch_update,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch_update,CURLOPT_POSTFIELDS, $post_updates);
curl_setopt($ch_update,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch_update,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch_update,CURLOPT_RETURNTRANSFER, true);
$response_update = curl_exec($ch_update);

curl_close($ch_update);

//print_r($response_update);
$json_update = json_decode($response_update, true);

$stmt = $json_update["data"][0]["3"]["value"];

if($stmt){

$dnacount = '2 More Times';

// initialize session if things where ok via html5 local storage for TypingDNA Enrollments.
echo "<script>
localStorage.setItem('dnauserid', '$username');
localStorage.setItem('dnaemail', '$email');
localStorage.setItem('dnafullname', '$fullname');
localStorage.setItem('dnacount', '$dnacount');
</script>";


//echo "success";

echo "<div id='alertdata_uploadfiles_o' class='well alerts alert-success'>Data Created Successfully.
.Redirecting in a second to Login Section.....<i class='fa fa-spinner fa-spin' style='font-size:20px'></i> <br></div>";

echo "<script>
window.setTimeout(function() {
    window.location.href = 'typingdna_enrollment.html';
//window.location.href = 'typingdna_enrollment.html?username=$username&userid=$userid_to_be_updated';
}, 1000);
</script><br><br>";



}else{
echo "<div id='alertdata_uploadfiles' class='alerts alert-danger'>Your Data cannot be submitted to Quickbase.<br></div>";
}




}



?>



