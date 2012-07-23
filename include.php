<?php
session_start();

if(MODE_DEBUG){
	echo '<h2><a href="http://www.karotz.com/authentication/run/karotz/'.$apiKey.'">Restart</a></h2>';
}

if(!isset($_SESSION['interactiveid']))
	$_SESSION['interactiveid'] = "";
	
if(isset($_GET['interactiveid']))
	$_SESSION['interactiveid'] = $_GET['interactiveid'];

$interID = $_SESSION['interactiveid']; # or $_GET['interactiveid'];

if(!isset($interID) || empty($interID)){
	if(!MODE_DEBUG){
		echo "Application must be run from Karotz...";
		exit;
	}
}

# 1. Include Class
#
include('../api/wizz.cc_karotz_class.php');
include('../api/karotz.php');

# 2. Create the Karotz object
#
$karotz = new Karotz($interID, MODE_DEBUG); # true for debug mode