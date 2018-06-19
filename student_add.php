<?php
    require_once('includes/formValidation.php');
    require_once($classpath . 'StudentDAO.php');
    require_once('includes/util.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);

    // Initializing variables
    $isUpdated = false;
    $familyID = 0;
    $familyName = '';
    $rankID = 0;
    $programID = 0;
    $dob_mo = '';
    $dob_day = '';
    $dob_yr = '';
    $exp_mo = '';
    $exp_day = '';
    $exp_yr = '';
    $enr_mo = '';
    $enr_day = '';
    $enr_yr = '';

    $firstName = '';
    $lastName = '';
    $beltSize = '';
    $schoolID = 0;
    $childSchoolID = 0;
    $rankID = 0;
    $phone1 = '';
    $phone2 = '';
    $addy1 = '';
    $addy2 = '';
    $city = '';
    $state = '';
    $zip = '';
    $country = '';
    $parentName = '';
    $programID = 0;
    $isActive = true;
    $familyName = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {    	
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
		$childSchoolID = !isset($_POST['childSchoolID']) ? 0 : intval($_POST['childSchoolID']);
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
        $errMsg = addStudentValidate($studentDAO, $firstName, $lastName, $beltSize, $birthDate, $expireDate, $enrollDate, $schoolID);
        
        if(strlen($errMsg) == 0){
            $birthDate = $dob_yr . '-' . digitMasker($dob_mo) . '-' . digitMasker($dob_day);            
            $expireDate = 'NULL';
            $enrollDate = 'NULL';

            if ($exp_yr != '' && $exp_mo != '' && $exp_day != '') {
                $expireDate = $exp_yr . '-' . digitMasker($exp_mo) . '-' . digitMasker($exp_day);
            }
            
            if ($enr_yr == '' && $enr_mo == '' && $enr_day == ''){
                $enrollDate = $enr_yr . '-' . digitMasker($enr_mo) . '-' . digitMasker($enr_day);
            }
    	    
            $studentExists = $studentDAO->getStudents($schoolID, 0, $firstName, $lastName, 2, '', '', '', '', 0, $birthDate);

            if(mysqli_num_rows($studentExists) > 0){
                $errMsg = "Student with the name [$firstName $lastName] already exists...";
            }else{
                $studentID = $studentDAO->insertStudent($firstName, $lastName, $rankID,
														$phone1, $phone2, $addy1, $addy2,
														$city, $state, $zip, $country,
														$beltSize, $schoolID, $birthDate, 
														$parentName, $programID, $enrollDate, $expireDate, $isActive, $childSchoolID);

                if($studentID < 1){
                    $errMsg = "An error has occurred while trying to add [$firstName $lastName]";
                }else{
					if($familyID < 0 && strlen(trim($familyName)) > 0){
						$familyID = $studentDAO->insertFamily(trim($familyName), $schoolID);
					}
								
					$updStatus = $studentDAO->addStudentToFamily($familyID, $studentID);
					
					if(!$updStatus){
						$errMsg = "An error has occurred while trying to add [$firstName $lastName] into a family";  
					}else{
					  	$isUpdated = true;
					}
				}
           }
        }
    } elseif (isset($_REQUEST['schoolID'])) {
    	//If a new school is selected, we need to refresh to reload the proper families.
    	//The school ID will be passed as a URL Var.	    
    	$schoolID = $_REQUEST['schoolID'];
    }

    if (!$isUpdated){
        $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"]);
		$subSchoolListRS = $studentDAO->getSubSchoolList($_COOKIE["uid"]);
        $rankListRS = $studentDAO->getRankListByUser($_COOKIE["uid"]);
        $programListRS = $studentDAO->getProgramList();
        
        //functions from the util class
        $rankHTML = showRankMenu($isAdmin, 'rankID', $rankID, $rankListRS);
        $programHTML = showProgramMenu('programID', $programID, $programListRS);
    }
?>

<script language="javascript" type="text/javascript">
<!--
function reloadFam()
{
	box = document.forms[0].schoolID;
	id = box.options[box.selectedIndex].value;
	if (id) location.href = 'index.php?action=stu.add&schoolID=' + id;
}

// -->
	$(document).ready(function(){
		var currentSchool = $('select[name="schoolID"] option:selected').val();		
		var $subSchoolList = $('option[parentID="' + currentSchool + '"]', '#subSchools');
		
		if ($subSchoolList.length == 0){
			$('tr.subAffiliationRow').hide();
		}else{
			$subSchoolList.each(function(index){
				$('select[name="childSchoolID"]').append($(this));	
			});
			$('select[name="childSchoolID"]').val(0);
			$('tr.subAffiliationRow').show();			
		}
	});
</SCRIPT>

<form name="studentAdd" action="index.php?action=stu.add" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Add A New Student</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if(isset($isUpdated) && $isUpdated == true){
?>
    <tr><td align="center" colspan="2">Student has been <b>successfully</b> added!</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="center"><a href="index.php?action=stu.add">Add Another</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?action=stu">Goto Top Menu</a></td></tr>
<?php
    }else{
?>
    <tr><td align="center" colspan="2" class="error"><?= $errMsg ?></td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td align="right" width="20%">School: </td>
        <td align="left" width="80%">
            <select name="schoolID" onChange="reloadFam()">
    <?php
            if($schoolListRS != false){
                while($row = mysqli_fetch_array($schoolListRS, MYSQLI_ASSOC)){
                	if(!isset($schoolID) || $schoolID < 1){$schoolID = $row['id'];}
                	if(empty($row['parentSchoolID'])){
    ?>
                <option value="<?= $row['id'] ?>" <?php if($schoolID == $row['id']){echo 'SELECTED';} ?>><?= $row['location_code'] ?></option>
    <?php
					}
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
        		
        	</select>
        </td>
    </tr>    
    <tr>
    	<td align="right" width="20%">Family Group: </td>
    	<td align="left" width="80%">
    		<select name="familyID">
    			<option value="-1" <?php if($familyID == -1){echo 'SELECTED';} ?>>No Family</option>
    <?php
    		$familyListRS = $studentDAO->getFamilyList($schoolID);
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
        <td align="right" width="20%">Rank: </td>
        <td align="left" width="80%">
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
        <td align="right" width="20%">Status: </td>
        <td align="left" width="80%">
            <select name="isActive">
                <option value="1" SELECTED>Active</option>
                <option value="0">Inactive</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" width="20%">Belt Size: </td>
        <td align="left" width="80%"><input name="beltSize" type="text" value="<?= $beltSize ?>" size="3"></td>
    </tr>
	<tr><td>&nbsp;</td></tr>    
    <tr>
        <td align="right" width="20%">First Name: </td>
        <td align="left" width="80%"><input name="firstName" type="text" value="<?= $firstName ?>"></td>
    </tr>
    <tr>
        <td align="right" width="20%">Last Name: </td>
        <td align="left" width="80%"><input name="lastName" type="text" value="<?= $lastName ?>"></td>
    </tr>
    <tr>
        <td align="right" width="20%">Parent/Guardian Name(s): </td>
        <td align="left" width="80%"><input name="parentName" type="text" value="<?= $parentName ?>" size ="30" maxlength="200"></td>
    </tr>    
    <tr>
        <td align="right" width="20%">Date of Birth: </td>
        <td align="left" width="80%"><input name="dob_mo" type="text" value="<?= $dob_mo ?>" size="2" maxlength="2"> - <input name="dob_day" type="text" value="<?= $dob_day ?>" size="2" maxlength="2"> - <input name="dob_yr" type="text" value="<?= $dob_yr ?>" size="4" maxlength="4"></td>
    </tr>
    <tr>
        <td align="right" width="20%">Address 1: </td>
        <td align="left" width="80%"><input name="addy1" maxlength="150" type="text" value="<?= $addy1 ?>"></td>
    </tr>
	<tr>
        <td align="right" width="20%">Address 2: </td>
        <td align="left" width="80%"><input name="addy2" maxlength="100"  type="text" value="<?= $addy2 ?>"></td>
    </tr>
	<tr>
        <td align="right" width="20%">City, ST, Zip: </td>
        <td align="left" width="80%"><input name="city" size="10" maxlength="100" type="text" value="<?= $city ?>">, <input name="state" size="3" maxlength="5" type="text" value="<?= $state ?>"> <input name="zip" size="5" maxlength="20" type="text" value="<?= $zip ?>"></td>
    </tr>
	<tr>
        <td align="right" width="20%">Country: </td>
        <td align="left" width="80%"><input name="country" size="5" maxlength="30" type="text" value="<?= $country ?>"></td>
    </tr>
	<tr>
        <td align="right" width="20%">Phone 1: </td>
        <td align="left" width="80%"><input name="phone1" size="15" maxlength="25" type="text" value="<?= $phone1 ?>"></td>
    </tr>
	<tr>
        <td align="right" width="20%">Phone 2: </td>
        <td align="left" width="80%"><input name="phone2" size="15" maxlength="25" type="text" value="<?= $phone2 ?>"></td>
    </tr>
    <tr><td>&nbsp;</td><td><input name="addButton" type="submit" value="Add Student">&nbsp;<input name="cancelButton" type="reset" value="Reset"></td></tr>
<?php
    }
?>
    </table>
</form>

<select id="subSchools" style="display: none">
<?php
        if($subSchoolListRS != false){
            while($row = mysqli_fetch_array($subSchoolListRS, MYSQLI_ASSOC)){
            	if(!isset($schoolID) || $schoolID < 1){$schoolID = $row['id'];}
?>
            <option parentID="<?= $row['parent_id'] ?>" value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
<?php
            }
        }
?>	
</select>
