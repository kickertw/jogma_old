<?php
	$loginStatus == false;
    /* The way this works...
     * If it is determined that a user is not logged in, we alter $action = "login"
     * Else, we let $action alone and let them proceed
     */

	//retrieving POST Vars
	$user = isset($_POST['user']) ? $_POST['user'] : '';
    $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
	
    if($action != 'logout'){
        if(!isset($_COOKIE["sid"])){
            if($user == '' || $pwd == ''){
                $errMsg = "Please login.";
                $action = "login";
            }else{
				$uv = new UserValidater($DB_server, $DB_user, $DB_pass, $DB_conn);
				$loginID = $uv->login($user, $pwd);

				if($loginID > 0){
				    $newSID = md5(time());
				    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
				    setcookie("uid", ''.$loginID, time()+14400, '/',$appName);
                    setcookie("sid", $newSID, time()+14400, '/',$appName);
					$loginStatus = $uv->initSession($loginID, $newSID);
				}else{
				    $errMsg = "Incorrect username/password combination.  Please try again.";
					$GLOBALS['action'] = 'login';
					//$action = "login";
					$loginStatus = false;
				}
			}
        }else{        	
			$uv = new UserValidater($DB_server, $DB_user, $DB_pass, $DB_conn);
			$loginStatus = $uv->validateSession($_COOKIE["uid"],$_COOKIE["sid"]);
			if(strlen($action) == 0){
                $action = 'main';
			}
        }
	}elseif($action == 'logout'){
	    $uv = new UserValidater($DB_server, $DB_user, $DB_pass, $DB_conn);
	    $uv->logout($_COOKIE["uid"]);

	    setcookie("uid", '', time()-3600,'/',$appName);
        setcookie("sid", '', time()-3600,'/',$appName);
	}

	//If login/user validation fails...we do this
	if($loginStatus == false && strlen($errMsg) == 0 && $action != 'logout'){
	    $errMsg = "An error has occured during validation.";
	    $GLOBALS['action'] = 'login';//$action = 'login';
	}
?>
