<?php

include('config.php');

/******************************************************************************
* this code will put you go through a series of forms to put up and take down
* shows as needed. 
* 
* if you are using VB, it is called from 
*			/hangouts/index.php
* but if you aren't using VB, make sure it is in a that is protected through
* htaccess or someother means
*
******************************************************************************/
?>
<div id="main"><div id="main-content" class="wrapper">

	<H2>Welcome to We are CosmoQuest Admin Screen</H2>

	<?php
	
	if (isset($_GET['task'])) {
			
			// Write next show information
			if (!strncmp($_GET['task'],"end", 3)) {
					$show_name = mysql_real_escape_string($_GET['show_name']);
					$show_time = mysql_real_escape_string($_GET['show_time']);
					$show_description = mysql_real_escape_string($_GET['show_description']);
          $query = "INSERT INTO hangouts (live, show_name, show_time, show_description, created_at) 
									  VALUES (0, '$show_name', '$show_time', '$show_description', NOW() )";

          mysql_query($query);
          echo mysql_error();

					if ($fp = fopen("live.php", "w") ) {
							fwrite($fp, '0');
							fclose($fp);
					} else 	 {
							die("CAN NOT WRITE FILE");
						}
                

			} else if (!strncmp($_GET['task'],"start", 5)) {
				
				  // Format the Embed to display correctly for our format
					$orig = explode("width",$_GET['embed']);
					$embed = $orig[0]." width='600' height='390' scrolling='auto' marginwidth='0' marginheight='0' frameborder='0'></iframe>";
          $embed = mysql_real_escape_string($embed);
      
					// Get the user ID out of the embed statement
          $orig = explode("id%3D",$embed);
          $orig = explode("-",$orig[1]);
          $google_id = $orig[0];

					// Get the activity id using the API
					$activity_url = "https://www.googleapis.com/plus/v1/people/".$google_id."/activities/public?alt=json&pp=1&key=".$API_KEY;
					
					//open connection
					$ch = curl_init();

					//set the url, number of POST vars, POST data
					curl_setopt($ch,CURLOPT_URL,$activity_url);
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
					$string = curl_exec ($ch);
					
					$json = json_decode($string, true);
					
					$activity_id = $json['items'][0]['id'];
					$comment_url = $json['items'][0]['url'];
					
          $query = "INSERT INTO hangouts (live, embed, comments_url, google_userid, activity_id, created_at) VALUES (1, '$embed', '$comment_url', '$google_id', '$activity_id', NOW() )";
          mysql_query($query);
          echo mysql_error();

					if ($fp = fopen("live.php", "w") ) {
							fwrite($fp, '1');
							fclose($fp);
					} else {
						die("CAN NOT WRITE FILE");
					}
					

			}
	}
	
    // Get most recent post
    $query = mysql_query("SELECT * FROM hangouts ORDER BY id DESC LIMIT 1");
    if ($show = mysql_fetch_array($query) ) {
        
			if (!$show['live']) { 
				echo "<p>Show's Over!</p>
						  <ul><li><a href='./index.php'>Return to main page</a></li>
								  <li>Do you want to start a new one?<br/>";
				start_a_show(); echo "</li>";
				echo "<li>Do you want to change the announcement?<br/>";
				get_show_info(); echo "</li>";
				
			} else {
				echo "<p>Show in progress!</p>
							<ul><li><a href='./index.php'>Return to main page</a></li>
							    <li>End show and schedule new show<br/>";
								      get_show_info(); echo "</li>";
				echo "</ul>";
											
			}
    } else {
        echo "NOT SET UP. Post info for first show.";
				get_show_info();				
    }
	
		
	?>

</div></div>
<div style="clear:both;"></div>

<?php

function get_show_info() {
	?>
	<form action="index.php" method="GET" enctype="multipart/form-data">
		<input type="hidden" name="action" value="admin">
		<input type="hidden" name="task" value="end">
		<?php
				 $get_shows = mysql_query("SELECT * FROM shows");
				 echo "Show Name: <br><input type='text' name='show_name' size='50'><br/>";
				 echo "Time:<br><input type='text' name='show_time' size='50'><br/>";
				 echo "Description:<br/><textarea name='show_description' cols='36' rows='5'></textarea><br/>";
		?>
		<input type="submit" value="Announce Show">
	</form></li>
	<?php
}
function start_a_show() {
		?>
		<form action="index.php" method="GET" enctype="multipart/form-data">
			   <input type="hidden" name="action" value="admin">
			   <input type="hidden" name="task" value="start">
			   <textarea name="embed"></textarea>
			   <input type="submit" value="Launch Show">
		</form></li>
		<?php
}

?>
