<?PHP
    include("./admin/config.php");

		// If not using VBulletin, then uncomment the next line
		// $admin = -1;

/**************************************************************************************************************
* The file content below is used to check logins if you are using VBulletin
**************************************************************************************************************/

		// Only run this using VBulletin --------------------------------------------------------------------------
		if ($admin != -1) {
			   // Read in Configuration file and check login --------------------------------------------------------
				 $currentDirectory = getcwd();
				 chdir($vb_root);
				 require_once('global.php');
				 chdir ($currentDirectory);    

				 // Grab the vbulletin object made when we included forum/global.php
				 global $vbulletin;

				 // Check if user is logged in (if userid is 0, the user isn't logged in)
		 	   if ($vbulletin->userinfo['userid']!=0) { 
				      $logged_in = 1; 
				
						  // Check if Admin
						  $str =  $vbulletin->userinfo['membergroupids'];
						  $array = explode_trim($str); 
				 		  if (in_array($vb_hangout_grp, $array)) {
							     $admin = 1;
						  } else { 
                   $admin = 0;
						  }
				 } else {
							$admin = 0;
				 }
		}

/**************************************************************************************************************
* Make sure the database can be connected to 
**************************************************************************************************************/				

		// Connect to MySQL ---------------------------------------------------------------------------------------
		$db = mysql_connect($db_host, $db_user, $db_password);
		if (!$db) {
		     die('Could not connect to mysql. 
						  Please make sure it exists & username/password are correct.'
						  .mysql_error()
						);
		}

		// Check that database exists -----------------------------------------------------------------------------
		if (!mysql_select_db($db_name, $db)) {
				 if($admin) {
		    		  include("./admin/install.php");
	    		 		die();
				 } else {
							die("Must run installer");
				 }
		} 

		// check that table exists --------------------------------------------------------------------------------
		if(!(mysql_query("SELECT * FROM hangouts"))) { 
				 if($admin) {
		    		  include("./admin/install.php");
		    		  die();
				 } else {
					    die("Must run installer");
				 }
		}

/**************************************************************************************************************
* Get the show info
**************************************************************************************************************/
		
		$get_show = mysql_query("SELECT * FROM hangouts ORDER BY id DESC LIMIT 1");
		$show     = mysql_fetch_array($get_show);
		
/**************************************************************************************************************
* Start sending the headers. If you have specific style sheets, they should be added below
**************************************************************************************************************/
?>

<html>
<head>
    <title>Come hang out</title>

		<!-- These are user supplied Style Sheets -->

		<!-- End user supplied Style Sheets -->
		
    <script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	  <script type="text/javascript" src="<?php echo $site_url?>functions.js"></script>
	
		<!-- These are user supplied javascript files -->
		
		<!-- End user supplied javascript files -->
			
</head>
<body>
<?PHP

    // User supplied headers below ----------------------------------->

    // End user supplied headers ------------------------------------->

/**************************************************************************************************************
* Start the page. 
**************************************************************************************************************/
		// If you have a header, put it here
		
		if (isset($_GET['action'])) $action = htmlspecialchars(mysql_real_escape_string($_GET['action']));
		if (isset($_POST['action'])) $action = htmlspecialchars(mysql_real_escape_string($_GET['action']));

		if ($admin) {
         switch ($action) {
						  // If the action is set to admin, you won't need to use the rest of the page
							// The $did_something flag is used to make sure the footer gets loaded, but
							// not everything else
					    case "admin":
									 include("./admin/admin.php");
									 $did_something = 1;
							break;
				 }		
	  }

/**************************************************************************************************************
* Below is the content for showing either a show or an alert that a show is coming. 
**************************************************************************************************************/
		if (!$did_something) {  
         ?>

				 <div id="main" style="width: 1000px; margin: 10px auto;">

				 <?php 
				// Check if need to be watching for a new show to start
				 if (!$show['live']) { ?>
					    <script type="text/javascript">
								   setTimeout(function() {checkReload();}, 1000);
							</script>
				 <?php }
				 
				 // This is a sidebar for loading your twitter account -----------------------------------------------
				 // Taken from: https://dev.twitter.com/docs/tweet-button
				 // You need to edit:
				 //			   'subject: data-url' line to reflect your website (edit the rest with care)
				 // 			 'search: ' 
				 ?>
				 <div id="sidebar" style="float: right;">
					
							<script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
			        <script>
			        new TWTR.Widget({
			                        version: 2,
			                        type: 'search',
			                        search: '#CQX AND #hangout',
			                        interval: 30000,
			                        title: '',
			                        subject: 'Tweet to join in: <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://cosmoquest.org/Hangouts" data-text="I\'m watching" data-hashtags="CQX,hangout">Tweet</a>',
			                        width: 355,
			                        height: 610,
			                        theme: {
			                        shell: {
			                        background: '#CDCDCD',
			                        color: '#ffffff'
			                        },
			                        tweets: {
			                        background: '#ffffff',
			                        color: '#444444',
			                        links: '#731810'
			                        }
			                        },
			                        features: {
			                        scrollbar: true,
			                        loop: false,
			                        live: true,
			                        behavior: 'all'
			                        }
			                        }).render().start();
			        </script>

							<script>
									  !function(d,s,id){
												 var js,fjs=d.getElementsByTagName(s)[0];								 
												 if(!d.getElementById(id)){js=d.createElement(s);js.id=id;
													    js.src="//platform.twitter.com/widgets.js";
													    fjs.parentNode.insertBefore(js,fjs);
												 }
										} (document,"script","twitter-wjs");
							 </script>
							
    		 </div> 
				 <!-- End of Twitter sidebar -->

				 <?php
				 // This is the main area of the page on the left that -------------------------------------------------
				 // You should edit the title. Comments mark where other stuff easily goes                        
				 ?>
				
				 <div id='viewer' style='padding:10px;'> 
						  <h2>Google Hangouts on Air</h2>
			
							<?php
				
							// Check if a show is, well, showing. If there is one: ------------------------------------------ 
							//      - put the show in a nice friendly div
							//			- display comments related specifically to the show
							if (!$show['live']) {
								   // You may want to change this div when a show isn't playing. Include a background or... ?>
									 <div id='player' 
									      style="border: 1px solid black; 
									             width: 600px; height: 390px; 
									             text-align: center; font-size: 24px; 
									             background: #000; 
									             margin-bottom: 10px;\">
												<h2 style="margin: 20px;"> The End </h2> 
												<p  style="margin: 20px;"> Stay tuned for the next show! </p>
									 </div>
							
									 <H3 style='border: none; font-weight: bold;'>
												<?php echo $show['show_name']; ?>
									 </H3>
									 <p><small>Time:<?php echo $show['show_time']; ?></small></p>
									 <p><?php echo $show['show_description']; ?></p>
									 
									 <?php
									 // If you have more to say, this is a good place to say it
									 // Consider linking to archives, showing featured eps, or 
									 // put in a menu to other site features
									 
									
						       // End User supplied content------------------------------
									
									
						  // Check if a show is, well, showing. If there is one: ------------------------------------------ 
						  //      - put the show in a nice friendly div
						  //			- display comments related specifically to the show
				 			}	else { ?>
									 <div id='player' 
											  style="border: 1px solid black; 
															 background: black; color: white;
															 width: 600px; height: 390px; 
															 text-align: center; font-size: 24px; 
															 margin-bottom: 10px;">

									 			<?php 
											  // This string is what contains your show's embed code
									      echo $show['embed'];	
												?>
									 </div>
									
									 <!-- This script will load the comments div after 1s -->
									 <script type="text/javascript">
											  setTimeout(function() {ReloadDiv();}, 1000);
									 </script>
									
									 <!-- This code loads the Google Comments box -->
									 <div id="google-comments-box" 
									      style="background: #CDCDCD; 
														   margin: 10px 0; padding: 1px 0;
														   width: 602px; 
														   -moz-border-radius: 5px; border-radius: 5px;">
												<H4 style="margin: 5px 5px 5px 5px; color: white;"> 
													   From Google+
												</H4>
												<div id="ReloadThis" 
														 style="background:white; 
																	  border: 1px solid #CDCDCD; 
																	  width: 580px; height: 160px;
																	  overflow-y:scroll; 
																	  margin-top: 5px; padding: 10px;">
		 												 Loading comments...
		  								  </div>
												<!-- This script makes sure the div is always scrolled to the bottom -->
												<script type="text/javascript"> ChangeHeight(); </script>
												
												<p style="padding: 5px; color: white; font-weight: bold; margin:0;">
														 <a href="<?php echo $show['comments_url'];?>" 
															  style="color: white;">
																  G+
																  <span style="float:right;">Join the conversation</span>
														 </a>
												</p>
									 </div>
							</div>
			   <?php 
				 } // This ends the "is there a show" if ?>
		     </div></div></div>

	  <?php 
		// Do they want to resize things?
	  	if ($win_size) {
			?>
			<form name="size" action="index.php" method="GET" style="float: left;">
				<input type="hidden" name="size" value="compact">
				<a href="javascript: void(0);" onclick="document.size.submit();"><small>switch to compact view </small></a> 
			</form>
		<?php } else { ?>
			<form name="size" action="index.php" method="GET" style="float: left;">
				<input type="hidden" name="size" value="standard">
				<a href="javascript: void(0);" onclick="document.size.submit();"><small>switch to standard view </small></a> 
			</form>		
		<?php } 
		
	  // If using VB and if they are an admin, open link using form
		if ($admin) {
			   ?> 
				 <form name="admin" action="index.php" method="GET" style="float: left; margin-left: 5px;">
						  <input type="hidden" name="action" value="admin">
							<small> <a href="javascript: void(0);" onclick="document.admin.submit();"> [administer site]</small></a>
				 </form>
		<?php
		
		// If not using VB, offer a friendly link to the admin site.
		// you may want to delete this.
		} else if ($admin == -1 ) {
				 ?>
				 <a href="./admin/admin.php">Administer Site (login Required)</a>
				 <?php
		}
		
} 	
	?>
</div>
</div>


<div id="footer">
<?PHP    
// User supplied footers below ----------------------------------->

// End user supplied footers ------------------------------------->   
?>
</div>


<?php	


/**************************************************************************************************************
* Function: explode_trim
* Required Input: A pointer to a string to tear apart
* Option Input:   The character or characters used to take apart the string
* Returns:        An array of the values between delimiters
* Example:
* 		$string = "1, 1, 2, 3, 5, 8, 13";
*			$array  = explode_trim($string, ',');
*			print_r($array); // returns: 0=>1, 1=>1, 2=>1, 3=>2, 3=>3, 4=>5, 5=>8, 6=>13
**************************************************************************************************************/

function explode_trim($str, $delimiter = ',') { 
    if ( is_string($delimiter) ) { 
        $str = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter) . ')\\s*|', $delimiter, $str)); 
        return explode($delimiter, $str); 
    } 
    return $str; 
}

?>