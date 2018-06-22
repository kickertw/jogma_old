<?php
    include('includes/formValidation.php');
    require_once($classpath . 'StudentDAO.php');
    require_once('includes/util.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);

	//Setting REQUEST Vars
	$stid = $_REQUEST['stid'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    	
    	//Setting POST Vars
    	$dob_mo = $_POST['dob_mo'];
    	$dob_day = $_POST['dob_day'];
    	$dob_yr = $_POST['dob_yr'];
    	$exp_mo = $_POST['exp_mo'];
    	$exp_day = $_POST['exp_day'];
    	$exp_yr = $_POST['exp_yr'];
    	$enr_mo = $_POST['enr_mo'];
    	$enr_day = $_POST['enr_day'];
    	$enr_yr = $_POST['enr_yr'];
    	
    	$firstName = $_POST['firstName'];    	
    	$lastName = $_POST['lastName'];
    	$beltSize = $_POST['beltSize'];
		$schoolID = $_POST['schoolID'];
		$childSchoolID = !isset($_POST['childSchoolID']) ? 0 : $_POST['childSchoolID'];
		$rankID = $_POST['rankID'];
		$phone1 = $_POST['phone1'];
		$phone2 = $_POST['phone2'];
		$addy1 = $_POST['addy1'];
		$addy2 = $_POST['addy2'];    	
    	$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		$country = $_POST['country'];
		$parentName = $_POST['parentName'];
		$programID = $_POST['programID'];
		$isActive = $_POST['isActive'];    	
    	$familyName = $_POST['familyName'];    	

        $birthDate = digitMasker($dob_mo) . '-' . digitMasker($dob_day) . '-' . $dob_yr;
        $expireDate = digitMasker($exp_mo) . '-' . digitMasker($exp_day) . '-' . $exp_yr;
		$enrollDate = digitMasker($enr_mo) . '-' . digitMasker($enr_day) . '-' . $enr_yr;
        $errMsg = addStudentValidate($studentDAO,$firstName, $lastName, $beltSize, $birthDate, $expireDate, $enrollDate, $schoolID);
        
        if(strlen($errMsg) == 0){
            $birthDate = $dob_yr . '-' . digitMasker($dob_mo) . '-' . digitMasker($dob_day);
	        $expireDate = $exp_yr . '-' . digitMasker($exp_mo) . '-' . digitMasker($exp_day);
    	    $enrollDate = $enr_yr . '-' . digitMasker($enr_mo) . '-' . digitMasker($enr_day);            
            $isUpdated = $studentDAO->updateStudent($stid, $firstName, $lastName, $rankID, 
													$phone1, $phone2, $addy1, $addy2,
													$city, $state, $zip, $country,
													$beltSize, $schoolID, $familyID, $familyName, 
													$birthDate, $parentName, $programID, $enrollDate, $expireDate, $isActive, $childSchoolID);
        }
    }else{
        $studentRS = $studentDAO->getStudent($stid);
        $currentStudent = mysqli_fetch_array($studentRS, MYSQLI_ASSOC);
        
        //setting local vars so input fields can be pre-populated
        $firstName = $currentStudent['first_name'];
        $lastName = $currentStudent['last_name'];
        $parentName = $currentStudent['parent_name'];
        $schoolID = $currentStudent['school_id'];
		$childSchoolID = $currentStudent['sub_school_id'];
        $programID = $currentStudent['program_id'];
        $rankID = $currentStudent['rank_id'];
        $isActive = $currentStudent['active'];
        $beltSize = $currentStudent['belt_size'];
    	$address1 = $currentStudent['address1'];
    	$address2 = $currentStudent['address2'];
    	$city = $currentStudent['city'];
    	$state = $currentStudent['state'];
    	$postal = $currentStudent['postal_code'];
		$country = $currentStudent['country'];
		$phone1 = $currentStudent['phone1'];
		$phone2 = $currentStudent['phone2'];

        $familyID = $studentDAO->getFamilyIDByStudent($stid);
                
        $datebits = explode('-',$currentStudent['birthdate']);
        $dob_yr = intval($datebits[0]);
        $dob_mo = intval($datebits[1]);
        $dob_day = intval($datebits[2]);
        
        $datebits = explode('-',$currentStudent['enroll_date']);
        $enr_yr = intval($datebits[0]);
        $enr_mo = intval($datebits[1]);
        $enr_day = intval($datebits[2]);
		
        $datebits = explode('-',$currentStudent['expire_date']);
        $exp_yr = intval($datebits[0]);
        $exp_mo = intval($datebits[1]);
        $exp_day = intval($datebits[2]);		        
    }
    
    $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"]);
	$subSchoolListRS = $studentDAO->getSubSchoolList($_COOKIE["uid"]);
    $familyListRS = $studentDAO->getFamilyList($schoolID);
    $rankListRS = $studentDAO->getRankListByUser($_COOKIE["uid"], 1);
    $programListRS = $studentDAO->getProgramList();
    
    $rankHTML = showRankMenu($isAdmin, 'rankID', $rankID, $rankListRS);
    $programHTML = showProgramMenu('programID', $programID, $programListRS);
?>

<?php
    if($isUpdated == true && strlen($errMsg) == 0){
?>
<form name="return2Search" action="index.php?action=stu.search" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title">Edit Your Student</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="center">Student has been <b>successfully</b> updated!</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="center"><input type="submit" name="returnToSearch" value="Back To My Search"></td></tr>
    </table>
</form>
<?php
    }elseif(isset($isUpdated) && $isUpdated == false){
?>
<form name="return2Search" action="index.php?action=stu.search" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title">Edit Your Student</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="center">Student was NOT successfully updated!  Please contact the administrator if this problem persists.</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="center"><input type="submit" name="returnToSearch" value="Back To My Search"></td></tr>
    </table>
</form>
<?php
    }else{
?>
<form name="studentEdit" action="index.php?action=stu.edit" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Edit Your Student</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if(isset($errMsg) && strlen($errMsg) > 0){
?>
    <tr><td colspan="2" align="center" class="error"><?= $errMsg ?></td></tr>
    <tr><td>&nbsp;</td></tr>
<?php
    }
?>
    <tr>
        <td align="right" width="15%">First Name: </td>
        <td align="left" width="85%"><input name="firstName" maxlength="100" type="text" value="<?= $firstName ?>"></td>
    </tr>
    <tr>
        <td align="right" width="15%">Last Name: </td>
        <td align="left" width="85%"><input name="lastName" maxlength="100" type="text" value="<?= $lastName ?>"></td>
    </tr>
    <tr>
        <td align="right" width="20%">Parent/Guardian Name(s): </td>
        <td align="left" width="80%"><input name="parentName" type="text" value="<?= $parentName ?>" size ="30" maxlength="200"></td>
    </tr>    
    <tr>
        <td align="right" width="15%">Date of Birth: </td>
        <td align="left" width="85%"><input name="dob_mo" type="text" value="<?= $dob_mo ?>" size="2" maxlength="2"> - <input name="dob_day" type="text" value="<?= $dob_day ?>" size="2" maxlength="2"> - <input name="dob_yr" type="text" value="<?= $dob_yr ?>" size="4" maxlength="4"></td>
    </tr>
    <tr>
        <td align="right" width="15%">Address 1: </td>
        <td align="left" width="85%"><input name="addy1" maxlength="150" type="text" value="<?= $address1 ?>"></td>
    </tr>
	<tr>
        <td align="right" width="15%">Address 2: </td>
        <td align="left" width="85%"><input name="addy2" maxlength="100"  type="text" value="<?= $address2 ?>"></td>
    </tr>
	<tr>
        <td align="right" width="15%">City, ST, Zip: </td>
        <td align="left" width="85%"><input name="city" size="10" maxlength="100" type="text" value="<?= $city ?>">, <input name="state" size="3" maxlength="5" type="text" value="<?= $state ?>"> <input name="zip" size="5" maxlength="20" type="text" value="<?= $postal ?>"></td>
    </tr>
	<tr>
        <td align="right" width="15%">Country: </td>
        <td align="left" width="85%"><input name="country" size="5" maxlength="30" type="text" value="<?= $country ?>"></td>
    </tr>
	<tr>
        <td align="right" width="15%">Phone 1: </td>
        <td align="left" width="85%"><input name="phone1" size="15" maxlength="25" type="text" value="<?= $phone1 ?>"></td>
    </tr>
	<tr>
        <td align="right" width="15%">Phone 2: </td>
        <td align="left" width="85%"><input name="phone2" size="15" maxlength="25" type="text" value="<?= $phone2 ?>"></td>
    </tr>    
	<tr><td>&nbsp;</td></tr>
    <tr>
        <td align="right" width="15%">School: </td>
        <td align="left" width="85%">
            <select name="schoolID">
    <?php
            if($schoolListRS != false){
                while($row = mysqli_fetch_array($schoolListRS, MYSQLI_ASSOC)){
    ?>
                <option value="<?= $row['id'] ?>" <?php if($schoolID == $row['id']){echo 'SELECTED';} ?>><?= $row['location_code'] ?></option>
    <?php
                }
            }
    ?>
            </select>
        </td>
    </tr>
	<tr class="subAffiliationRow">
        <td align="right" width="20%">Sub Affililation: </td>
        <td align="left" width="80%">
        	<select name="childSchoolID">
        		<option value="0">-</option>
<?php
        if($subSchoolListRS != false){
            while($row = mysqli_fetch_array($subSchoolListRS, MYSQLI_ASSOC)){
            	if($schoolID == $row['parent_id']){
?>
            <option value="<?= $row['id'] ?>" <?php if($childSchoolID == $row['id']){echo 'SELECTED';} ?>><?= $row['name'] ?></option>
<?php
            	}
        	}
		}
?>	
        	</select>
        </td>
    </tr>    
    <tr>
    	<td align="right" width="15%">Family Group: </td>
    	<td align="left" width="85%">
    		<select name="familyID">
    			<option value="-1" <?php if($familyID == -1){echo 'SELECTED';} ?>>No Family</option>
    <?php
            if($familyListRS != false){
                while($row = mysqli_fetch_array($familyListRS, MYSQLI_ASSOC)){
    ?>
                <option value="<?= $row['id'] ?>" <?php if($familyID == $row['id']){echo 'SELECTED';} ?>><?= $row['display_name'] ?></option>
    <?php
                }
            }
    ?>    			
    		</select> or add new 
    		<input name="familyName" type="text" value="<?= $familyName ?>" size="30" maxlength="100"/> 
		</td>
	</tr>    
    <tr>
        <td align="right" width="15%">Rank: </td>
        <td align="left" width="85%">
			<?= $rankHTML ?>
        </td>
    </tr>
    <tr>
        <td align="right" width="20%">Program: </td>
        <td align="left" width="80%">
			<?= $programHTML ?>            
        </td>
    </tr>
    <tr>
        <td align="right" width="20%">Start Date: </td>
        <td align="left" width="80%"><input name="enr_mo" type="text" value="<?= $enr_mo ?>" size="2" maxlength="2"> - <input name="enr_day" type="text" value="<?= $enr_day ?>" size="2" maxlength="2"> - <input name="enr_yr" type="text" value="<?= $enr_yr ?>" size="4" maxlength="4"></td>
    </tr>
    <tr>
        <td align="right" width="20%">Exp. Date: </td>
        <td align="left" width="80%"><input name="exp_mo" type="text" value="<?= $exp_mo ?>" size="2" maxlength="2"> - <input name="exp_day" type="text" value="<?= $exp_day ?>" size="2" maxlength="2"> - <input name="exp_yr" type="text" value="<?= $exp_yr ?>" size="4" maxlength="4"></td>
    </tr>     
    <tr>
        <td align="right" width="15%">Status: </td>
        <td align="left" width="85%">
            <select name="isActive">
                <option value="1" <?php if($isActive == '1'){echo 'SELECTED';} ?>>Active</option>
                <option value="0" <?php if($isActive == '0'){echo 'SELECTED';} ?>>Inactive</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" width="15%">Belt Size: </td>
        <td align="left" width="85%"><input name="beltSize" type="text" value="<?= $beltSize ?>" size="3"></td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td colspan="2"><hr width="90%"></td></tr>
    <tr>
		<td>&nbsp;</td>
		<td align="left">
    		<?php include('students_grad_history.php'); ?>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td><input name="updateButton" type="submit" value="Update Student">&nbsp;<input name="cancelButton" type="button" value="Cancel" onclick="javascript:history.back(-1);"></td></tr>
    </table>
    
    <input name="stid" type="hidden" value="<?= $stid ?>">
</form>
<?php
    }
?>