<?php
?>
<h2>No frills installer</h2>

<p>Checking file write permissions...

<?php 

$filename = 'live.php';
if (is_writable($filename)) {
    echo 'Done!</p>';
} else {
    echo 'The file is not writable.</p>';
    echo 'Please make live.php writable for the webserver user.';
    die();
}

?>

<p> Create database table...
<?php
    $query = "CREATE TABLE `hangouts` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `live` tinyint(4) DEFAULT '0',
		  `embed` blob,
		  `comments_url` varchar(255) DEFAULT NULL,
		  `google_userid` varchar(255) DEFAULT '',
		  `activity_id` varchar(255) DEFAULT '',
		  `show_name` varchar(255) DEFAULT '',
		  `show_time` varchar(255) DEFAULT '',
		  `show_description` blob,
		  `created_at` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;";
    mysql_query($query);
    
    $check = mysql_error();
      
    if ($check == "") {
        echo "Done!<br/><br/>All set up! It's recommended you delete or rename install.php from your server. Reload to use the software.</p>";
    } else {
        echo $check;
    }

      
?>
