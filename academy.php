<?php    
    include($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);
	
	$loadRow = array(
		'location_code' => '',
		'name' => '',
		'address1' => '',
		'address2' => '',
		'city' => '',
		'state' => '',
		'postal' => '',
		'country' => '',
		'poc' => '',
		'active' => '1',
		'credit' => '',
	);	

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['updateProfileButton'])) {
        	$pass1 = $_POST['pass1'];
        	$pass2 = $_POST['pass2'];
        	$username = $_POST['username'];
        	$fullname = $_POST['fullname'];
        	$email = $_POST['email'];
        	$ph1 = $_POST['ph1'];
        	$ph2 = $_POST['ph2'];
        
            $errorMsg = passwordCheck($pass1, $pass2);

            if (strlen($errorMsg)==0) {
                $userDAO->updateUserInfo($_COOKIE["uid"], $username, $fullname, $email, $ph1, $ph2, $pass1);
                $errorMsg = "Your Profile has been successfully updated!";
            }
        } elseif(isset($_POST['loadSchoolButton'])) {
        	$schoolID = $_POST['schoolID'];        
			$loadRow = $studentDAO->getSchool($schoolID);
			echo 'loadrow[active] = ' . $loadRow['active'];
        } elseif(isset($_POST['updateSchoolButton']) && strcmp($_POST['updateSchoolButton'], 'Update') == 0) {
        	$updateSchoolID = $_POST['updateSchoolID'];
        	$code = $_POST['code'];
        	$name = $_POST['name'];
        	$addy1 = $_POST['addy1'];
        	$city = $_POST['city'];
        	$state = $_POST['state'];
			$country = $_POST['country'];
			$postal = $_POST['postal'];
			$poc = $_POST['poc'];
			$addy2 = $_POST['addy2'];
			$isActive = $_POST['isActive'];
			$credit = $_POST['credit'];
			$parentSchoolID = $_POST['parentSchoolID'];

            $studentDAO->updateSchool($updateSchoolID, $code, $name, $addy1, $city, $state, $country, $postal, $poc, $addy2, $isActive, $credit, $parentSchoolID);
            $errorMsg = "Academy [$name] has been successfully updated!";
        } elseif (isset($_POST['updateSchoolButton']) && strcmp($_POST['updateSchoolButton'], 'Add') == 0) {
        	$code = $_POST['code'];
        	$name = $_POST['name'];
        	$city = $_POST['city'];
        	$state = $_POST['state'];
			$country = $_POST['country'];
			$postal = $_POST['postal'];
			$poc = $_POST['poc'];
			$addy1 = $_POST['addy1'];
			$addy2 = $_POST['addy2'];
			$parentSchoolID = $_POST['parentSchoolID'];
        
          	include('includes/formValidation.php');
          	$errorMsg = addSchoolValidate($code, $name, $addy1, $city, $state, $postal, $country, $poc);
          
          	if(strlen($errorMsg) == 0){
	            $studentDAO->insertSchool($code, $name, $addy1, $addy2, $city, $state, $country, $postal, $poc, $parentSchoolID);            
    	        $errorMsg = "Academy [$name] has been successfully added!";
    	    }
        } elseif (isset($_POST['removeSchoolButton']) && strcmp($_POST['removeSchoolButton'], 'Remove') == 0) {
        	$schoolID = $_POST['schoolID'];	
        
		  	$studentDAO->removeSchool($schoolID);
		  	$errorMsg = "Removal Successful";
		}
    }

    include('academy_table.php');
?>