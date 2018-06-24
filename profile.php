<?php
    function passwordCheck($password1, $password2){

        $password1 = trim($password1);
        $password2 = trim($password2);
        
        $pLen = strlen($password1);
        

        if($pLen != 0 && ($pLen < 6 || $pLen > 12)){
            return "Your password must be between <b>6</b> and <b>12</b> characters long.";
        }elseif(strcmp($password1, $password2) != 0){
            return "Your passwords do not match.  Please reverify.";
        }else{
            return "";
        }
    }
    
    include($classpath . 'StudentDAO.php');

    $e_userID = $_POST['e_userID'] ?? -1;
    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);
    
	$e_userRow = array(
		'username' => '',
		'fullname' => '',
		'email' => '',
        'show_advanced_ranks' => 0,
        'active' => 1
	);

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    
        if(isset($_POST['updateProfileButton'])){
        	$pass1 = $_POST['pass1'];
        	$pass2 = $_POST['pass2'];
        	$username = $_POST['username'];
        	$fullname = $_POST['fullname'];
        	$email = $_POST['email'];
        	$ph1 = $_POST['ph1'];
        	$ph2 = $_POST['ph2'];
        	$rank_display = $_POST['rank_display'];
        	$accessLevel = $_POST['profile_access_level'];
        	      
            $errorMsg = passwordCheck($pass1, $pass2);

            if( strlen($errorMsg)==0 ){            
                $userDAO->updateUserInfo($_COOKIE["uid"], $username, $fullname, $email, $ph1, $ph2, $pass1, 1, $accessLevel, $rank_display);
                $errorMsg = "Your Profile has been successfully updated!";
            }
        } elseif($_POST['loadUserButton']) {
            $e_userRow = $userDAO->getUserInfo($e_userID);
        } elseif(isset($_POST['updateUserButton']) && strcmp($_POST['updateUserButton'], 'Update User') == 0) {
        	$updateUserID = $_POST['updateUserID'];
        	$e_pass1 = $_POST['e_pass1'];
        	$e_pass2 = $_POST['e_pass2'];
        	$e_username = $_POST['e_username'];
        	$e_fullname = $_POST['e_fullname'];
        	$e_email = $_POST['e_email'];
        	$e_ph1 = $_POST['e_ph1'];
        	$e_ph2 = $_POST['e_ph2'];
			$e_status = $_POST['e_status'];
			$e_accesslvl = $_POST['e_accesslvl'];        	
        	$e_rank_display = $_POST['e_rank_display'];        	
        	
            $errorMsg = passwordCheck($e_pass1, $e_pass2);

            if (strlen($errorMsg) == 0) {
                $userDAO->updateUserInfo($updateUserID, $e_username, $e_fullname, $e_email, $e_ph1, $e_ph2, $e_pass1, $e_status, $e_accesslvl, $e_rank_display);
                $schoolListRS = $studentDAO->getSchoolList(0,1);
                
                while($row = mysqli_fetch_assoc($schoolListRS)){
                	${sch_axs_.$row['id']} = $_POST['sch_axs_' . $row['id']];
                    $userDAO->updateSchoolAccess($updateUserID, $row['id'], ${sch_axs_.$row['id']});
                }

                $errorMsg = "User Profile [$e_username] has been successfully updated!";
            }
        }elseif(isset($updateUserButton) && strcmp($updateUserButton,'Add User') == 0){
        	$e_pass1 = $_POST['e_pass1'];
        	$e_pass2 = $_POST['e_pass2'];
        	$e_username = $_POST['e_username'];
        	$e_fullname = $_POST['e_fullname'];
        	$e_email = $_POST['e_email'];
        	$e_ph1 = $_POST['e_ph1'];
        	$e_ph2 = $_POST['e_ph2'];
			$e_status = $_POST['e_status'];
			$e_accesslvl = $_POST['e_accesslvl'];        	
        	$e_rank_display = $_POST['e_rank_display'];	
        
            $errorMsg = passwordCheck($e_pass1, $e_pass2);

            if( strlen($errorMsg)==0 ){
                $newUserID = $userDAO->addUser($e_username, $e_fullname, $e_email, $e_ph1, $e_ph2, $e_pass1, $e_status, $e_accesslvl, $e_rank_display);
                $schoolListRS = $studentDAO->getSchoolList(0,1);

                while($row = mysqli_fetch_assoc($schoolListRS)){
                	${sch_axs_.$row['id']} = $_POST['sch_axs_' . $row['id']];                
                    $userDAO->updateSchoolAccess($newUserID, $row['id'], ${sch_axs_.$row['id']});
                }
                
                $errorMsg = "User Profile [$e_username] has been successfully added!";
            }
        }
    }

    include('profile_table.php');
?>
