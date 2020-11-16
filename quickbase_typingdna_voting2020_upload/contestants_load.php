<?php 
ob_start();
error_reporting(0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include('header_title.php');


ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);

 ?>
<script>
$(document).ready(function(){

$('.vote_btn').click(function(){
// confirm start
 if(confirm("Are you sure you want to Vote for This Contestant: ")){
var id = $(this).data('id');

var voter_userid = localStorage.getItem('dnauserid3');
var voter_fullname = localStorage.getItem('dnafullname3');

$(".loader-vote_"+id).fadeIn(400).html('<br><div style="color:black;background:white;padding:10px;"><i class="fa fa-spinner fa-spin" style="font-size:20px"></i>&nbsp;Please Wait, Vote is being Casted...</div>');
var datasend = {'id': id, 'voter_userid': voter_userid, 'voter_fullname': voter_fullname};
		$.ajax({
			
			type:'POST',
			url:'vote.php',
			data:datasend,
                        crossDomain: true,
			cache:false,
			success:function(msg){


//alert(msg.trim());



	if(msg == 1){
alert('Voting Successful casted');
$(".loader-vote_"+id).hide();
$(".result-vote_"+id).html("<div style='color:white;background:green;padding:10px;'>Voting Successful Casted</div>");
setTimeout(function(){ $(".result-vote_"+id).html(''); }, 5000);
location.reload();
}


	if(msg == 0){

alert('Voting Cannot be Casted. Please ensure you are connected to Internet.');
$(".loader-vote_"+id).hide();
$(".result-vote_"+id).html("<div style='color:white;background:red;padding:10px;'>Voting Cannot be Casted. Please ensure you are connected to Internet.</div>");
setTimeout(function(){ $(".result-notify-vote_"+id).html(''); }, 5000);

}




	if(msg == 4){

alert('You have already Voted for this contestant.');
$(".loader-vote_"+id).hide();
$(".result-vote_"+id).html("<div style='color:white;background:red;padding:10px;'>You have already Voted for this contestant.</div>");
setTimeout(function(){ $(".result-notify-vote_"+id).html(''); }, 5000);

}





}
			
});
}

// confirm ends

                });










            });






</script>





<style>

.vote_count { color: #FFF; display: block; float: right; border-radius: 12px; border: 1px solid #2E8E12; background: green; padding: 2px 6px;font-size:14px; }


.vote_css{
background:#0088cc;
border:none;
color:white;
padding:6px;
border-radius:20%;
width:100px;
}

.vote_css:hover{
background:orange;
color:black;
}



</style>




<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



include('quickbase_token.php');
include('quickbase_tables.php');



$token=strip_tags($_POST['token']);



$url3 = "https://api.quickbase.com/v1/records/query";
$ch3 = curl_init();
curl_setopt($ch3,CURLOPT_URL, $url3);
$useragent3 ='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';

$data_params3 ='{
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

 

 "sortBy": [
    {
      "fieldId": 4,
      "order": "DESC"
    },
    {
      "fieldId": 5,
      "order": "DESC"
    }
  ]

  



}
';





curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
"QB-Realm-Hostname: $quickbase_domain",
"User-Agent: $useragent3",
"Authorization: QB-USER-TOKEN $access_token",
'Content-Type:application/json'
));  

//curl_setopt($ch3,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch3,CURLOPT_CUSTOMREQUEST,'POST');

//curl_setopt($ch3,CURLOPT_CUSTOMREQUEST,'DELETE');
curl_setopt($ch3,CURLOPT_POSTFIELDS, $data_params3);
curl_setopt($ch3,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch3,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch3,CURLOPT_RETURNTRANSFER, true);
$response3 = curl_exec($ch3);

curl_close($ch3);

//print_r($response3);
$json3 = json_decode($response3, true);
$rec_List1 = $json3["metadata"]["numRecords"];


if($json3 == ''){
//if($rec_List1 == ''){
echo "
<script>
function reloadPage() {
location.reload();
}
</script>
<div style='background:red;color:white;padding:10px;'>No Network. Refresh page and ensure there is Internet Connection <br><br> <center><button class='readmore_btn' style='' title='Refresh Page' onclick='reloadPage()'>Refresh Page</button></center> </div>";
exit();
}




if($rec_List1  == 0){

echo "<div style='background:red;color:white;padding:10px;border:none'>No Contestants Added Yet</div>";
}




foreach($json3['data'] as $v1){
$id = $v1['3']['value'];

$contestant_id = $id;
$contestant_photourl = $v1['7']['value'];
$contestant_fullname = $v1['8']['value'];

$contest_title = $v1['9']['value'];
$contest_description = $v1['10']['value'];
$timing= $v1['11']['value'];
$vote_count= $v1['12']['value'];



?>





<div class="col-sm-12 notify_content_css  col-sm-4" style='display:inline-block;border-style: dashed; border-width:2px; border-color: orange;color:black;background:#c1c1c1;padding:10px;' >
<?php 
if($contestant_id){
?>


<div  style="color:black;padding:10px;background:#ddd">
<img style='min-height:100px;min-width:100px;max-height:100px;max-width:100px;' class='img-circle' src='<?php echo $contestant_photourl; ?>' alt='User Image'>


<br><b>Contestant Name:</b> <?php echo $contestant_fullname; ?><br>

<b>Contest Position:</b> <?php echo $contest_title; ?><br>
<div class='pull-left vote_count'><b> Vote: </b> <?php echo $vote_count; ?></div> 

<br><br>

<p>
   <div class="loader-vote_<?php echo $id; ?>"></div>
   <div class="result-vote_<?php echo $id; ?>"></div>

<button class='pull-left col-sm-6 vote_css vote_btn' data-id='<?php echo $id; ?>' title='Vote for this  Candidate'>Vote</button>
</p>
<br>
</div>
<?php
}
?>














</div>



<?php
}
?>

<?php
ob_flush();
?>


