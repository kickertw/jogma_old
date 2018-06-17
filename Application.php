<?php
    //Application Wide Variables
    $FULL_GRAD_FEE = 1.00;
    $FAMILY_DISCOUNT = 0.00;
    $DIPLOMA_FEE = 25.00;

	//Application Settings
	$appName = 'jogma.net';
	$appRoot = '';
	$imgRoot = 'images/';
	$classpath = 'classes/';
	$validUser = false;
	$appSize = '700';
	$navSize = '140';
	$bodySize = '545';
	$devMode = 0;
	
	$diplomaMgrEmail = 'mastercarlson@aol.com';
	
	if ($devMode == 0){
		$JRFEmailAcct = '-';
	}else{
		$JRFEmailAcct = '-';
	}


	//Application Naviagtion Links
	$navLink[0] = 'News/Updates';
	$navLink[1] = 'Profile Manager';
	$navLink[2] = 'Academy Manager';
	$navLink[3] = 'Student Manager';
	$navLink[4] = 'Grad List Manager';

	$navURL[0] = 'index.php?action=main';
	$navURL[1] = 'index.php?action=pro';
	$navURL[2] = 'index.php?action=am';
	$navURL[3] = 'index.php?action=stu';
	$navURL[4] = 'index.php?action=gl';


	//DB variables
	//wongism
	//------------
	//$DB_user = "wongism_jriadmin";
	//$DB_pass = "la8ter";
	//$DB_conn = "wongism_jritkd";

	//DB variables
	//jogma.net
	//------------
	$DB_server = 'localhost';
	$DB_user = "thewo25_jogmaSQL";
	$DB_pass = "j0gm@SQL";
	$DB_conn = "thewo25_jogma1";

	//User Access Level Constants
	//---------------------------
	$UAL_DEFAULT = 0;
	$UAL_USER_MANAGER = 1;
	$UAL_SITE_ADMIN = 2;
	$UAL_JRI_ADMIN = 3;

	$accessLevelVal[0] = $UAL_DEFAULT;
	$accessLevelVal[1] = $UAL_USER_MANAGER;
	$accessLevelVal[2] = $UAL_SITE_ADMIN;
	$accessLevelVal[3] = $UAL_JRI_ADMIN;
	//$accessLevelVal[3] = $UAL_PROBATION;

	$accessLevelName[0] = 'Normal';
	$accessLevelName[1] = 'User Manager';
	$accessLevelName[2] = 'Site Administrator';
	$accessLevelName[3] = 'JRI Board of Directors';
	//$accessLevelName[3] = 'Probationary User';


    //loading classes
    include('classes/UserValidater.php');
?>
