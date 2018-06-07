<?php
    //Application Wide Variables
    $FULL_GRAD_FEE = 35.00;
    $FAMILY_DISCOUNT = 10.00;

	//Application Settings
	$appName = 'wongism.com';
	$appRoot = '';
	$imgRoot = 'images/';
	$classpath = 'classes/';
	$validUser = false;
	$appSize = '700';
	$navSize = '140';
	$bodySize = '545';
	$devMode = 0;
	$diplomaMgrEmail = 'mastercarlson@aol.com';


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
	//------------
	$DB_user = "wongism_jriadmin";
	$DB_pass = "la8ter";
	$DB_conn = "wongism_jritkd";

    //loading classes
    include('classes/UserValidater.php');
?>
