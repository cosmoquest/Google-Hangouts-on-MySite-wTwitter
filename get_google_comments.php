<?php
include("./admin/config.php");

// Connect to database ------------------------------------------------------------------------------------------------------
$db = mysql_connect($db_host, $db_user, $db_password);
if (!$db) {
        die('Could not connect to mysql. Please make sure it exists & username/password are correct.' . mysql_error());
}

// Connect to Database and get show info
mysql_select_db($db_name, $db) or die("Database does note exist");
$get_show = mysql_query("SELECT * FROM hangouts ORDER BY id DESC LIMIT 1");
$show     = mysql_fetch_array($get_show);

// Connect to Comments ------------------------------------------------------------------------------------------------------

$pagetoken = "";

$COMMENTS_url_1 = "https://www.googleapis.com/plus/v1/activities/";
$COMMENTS_url_2 = "/comments?alt=json&maxResults=100&";
$COMMENTS_url_3 = "pp=1&key=";

$curl_url = $COMMENTS_url_1.$show['activity_id'].$COMMENTS_url_2.$pagetoken.$COMMENTS_url_3.$API_KEY;

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL,$curl_url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
$string = curl_exec ($ch);

$json = json_decode($string, true);
//print_r($json);


foreach ($json['items'] as $value) {
	echo "<div style='padding: 5px; min-height: 40px; border-bottom: 1px dotted #DDD;'>";
	echo "<img src='".$value['actor']['image']['url']."' style='float: left; margin-right: 5px; height: 30px; width: 30px'>";
	echo "<p style='font-size: 12px!important; font-family: \"lucida grande\",lucida,tahoma,helvetica,arial,sans-serif!important; '>
			  <a href='".$value['actor']['url']."'>".$value['actor']['displayName']."</a> - ".$value['object']['content']."</p>";
	echo "</div>";
	$reply = $value['inReplyTo'][0]['url'];
}

//close connection
@curl_close($ch);


while (isset($json['nextPageToken'])) {
		$pagetoken = "pageToken=".$json['nextPageToken']."&";
	
		$curl_url = $COMMENTS_url_1.$show['activity_id'].$COMMENTS_url_2.$pagetoken.$COMMENTS_url_3.$API_KEY;

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$curl_url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		$string = curl_exec ($ch);

		$json = json_decode($string, true);
		//print_r($json);


		foreach ($json['items'] as $value) {
				echo "<div style='padding: 5px; min-height: 40px; border-bottom: 1px dotted #DDD;'>";
				echo "<img src='".$value['actor']['image']['url']."' style='float: left; margin-right: 5px; height: 30px; width: 30px'>";
				echo "<p style='font-size: 12px!important; font-family: \"lucida grande\",lucida,tahoma,helvetica,arial,sans-serif!important; '>
						  <a href='".$value['actor']['url']."'>".$value['actor']['displayName']."</a> - ".$value['object']['content']."</p>";
				echo "</div>";
				$reply = $value['inReplyTo'][0]['url'];
		}
		//close connection
		@curl_close($ch); 
}


echo "</div>";
?>