<?php
    include('includes/formValidation.php');

    //Including DAO Classes
    require_once($classpath . 'GradListDAO.php');
    require_once($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);
    
    //Returning a list of schools this user has access to
    $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"]);

    // Set post vars if applicable
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $rankID = $_POST['rankID'] ?? 0;
    $orderBy = $_POST['orderBy'] ?? '';
    $orderDir = $_POST['orderDir'] ?? '';
    $orderBy2 = $_POST['orderBy2'] ?? '';
    $orderDir2 = $_POST['orderDir2'] ?? '';
    $gradMo = $_POST['gradMo'] ?? '';
    $gradDay = $_POST['gradDay'] ?? '';
    $gradYear = $_POST['gradYear'] ?? '';
    $schoolID = $_POST['schoolID'] ?? 0;
    $gradListID = $_POST['gradListID'] ?? '';
    $studentIDs = $_POST['studentIDs'] ?? '';
    $step = 1;
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['step']) && !isset($step)){
            $step = $_POST['step'];
        }

        if ($step == 2) {
            $gradDate = $gradMo . '-' . digitMasker($gradDay) . '-' . $gradYear;

            if (datecheck($gradDate) == true){
   	            $gradDate = $gradYear . '-' . $gradMo . '-' . digitMasker($gradDay);
                $gradListID = $gradListDAO->createGradList($schoolID, $gradDate, $_COOKIE["uid"]);
                
                //If gradListID is zero...we know a list with the same date already exists...
                if(intval($gradListID) == 0){
                    $errMsg = "A graduation list for $gradMo-$gradDay-$gradYear already exists.";
                    $step = 1;
                }
            }else{
                $errMsg = "[$gradMo-$gradDay-$gradYear] is an invalid date.";
                $step = 1;
            }
        } elseif ($step == 3) {
            $studentListRS = $studentDAO->getStudents($schoolID, $rankID, $firstName, $lastName, 1, $orderBy, $orderDir, $orderBy2, $orderDir2, $gradListID, '', true);

			if (!isset($_POST['addToListButton2']) && !isset($_POST['gotoNextStep'])){
				$step = 2;
			}
			
			if (isset($_POST['addToListButton']) || isset($_POST['addToListButton2'])){
                for($i = 0; $i < count($studentIDs); $i++) {
                    if (isset($studentIDs[$i])){
                        $gradListDAO->addGrad($gradListID, $studentIDs[$i], $_POST['oldRank_'. $studentIDs[$i]], $_POST['newRank_'.$studentIDs[$i]]);
                    }
                }
            }
        }        
    }
    
    if ($action == 'gl.edit'){
        $action = 'gl.add';
    }
?>

    <form name="gradListWizard" action="index.php?action=<?= $action ?>" method="POST">
<?php
    if($step > 1) {
?>
        <input name="gradListID" type="hidden" value="<?= $gradListID ?>"/>
<?php
    }
?>
        <table align="left" bgcolor="White" width="100%">
        <tr>
            <th class="title" colspan="3">Graduation List Manager</th>
        </tr>
        <tr><td>&nbsp;</td></tr>
<?php
    if(strlen($errMsg) > 0){
?>
        <tr><td align="center" colspan="3" class="error"><?= $errMsg ?></td></tr>
        <tr><td>&nbsp;</td></tr>
<?php
    }
    
    if($step == 1){
?>
        <tr><td align="left" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><b>Step 1:&nbsp;&nbsp;Enter Graduation List Information<b></u></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td align="right" width="15%">Month/Year: </td>
            <td align="left" width="85%" colspan="2">
                <select name="gradMo">
                    <option value="01" <?php if(date("m")=='01'){echo 'SELECTED';} ?>>January</option>
                    <option value="02" <?php if(date("m")=='02'){echo 'SELECTED';} ?>>February</option>
                    <option value="03" <?php if(date("m")=='03'){echo 'SELECTED';} ?>>March</option>
                    <option value="04" <?php if(date("m")=='04'){echo 'SELECTED';} ?>>April</option>
                    <option value="05" <?php if(date("m")=='05'){echo 'SELECTED';} ?>>May</option>
                    <option value="06" <?php if(date("m")=='06'){echo 'SELECTED';} ?>>June</option>
                    <option value="07" <?php if(date("m")=='07'){echo 'SELECTED';} ?>>July</option>
                    <option value="08" <?php if(date("m")=='08'){echo 'SELECTED';} ?>>August</option>
                    <option value="09" <?php if(date("m")=='09'){echo 'SELECTED';} ?>>September</option>
                    <option value="10" <?php if(date("m")=='10'){echo 'SELECTED';} ?>>October</option>
                    <option value="11" <?php if(date("m")=='11'){echo 'SELECTED';} ?>>November</option>
                    <option value="12" <?php if(date("m")=='12'){echo 'SELECTED';} ?>>December</option>
                </select> -
                <input name="gradDay" type="text" size="2" maxlength="2"/> -
                <select name="gradYear">
                	<option value="<?= intval(date('Y'))-3 ?>"><?= intval(date('Y'))-3 ?></option>
                	<option value="<?= intval(date('Y'))-2 ?>"><?= intval(date('Y'))-2 ?></option>
                    <option value="<?= intval(date('Y'))-1 ?>"><?= intval(date('Y'))-1 ?></option>
                    <option value="<?= intval(date('Y')) ?>" SELECTED><?= intval(date('Y')) ?></option>
                    <option value="<?= intval(date('Y'))+1 ?>"><?= intval(date('Y'))+1 ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right" width="15%">School: </td>
            <td align="left" width="85%" colspan="2">
                <select name="schoolID">
        <?php
                if($schoolListRS != false){
                    while($row = mysqli_fetch_array($schoolListRS, MYSQLI_ASSOC)){
        ?>
                    <option value="<?= $row['id'] ?>" <?php if($schoolID ?? 0 == $row['id']){echo 'SELECTED';} ?>><?= $row['location_code'] ?></option>
        <?php
                    }
                }
        ?>
                </select>
            </td>
        </tr>
<?php
    } elseif ($step == 2) {
        if(!isset($_POST['editMode'])){
?>
        <tr><td align="left" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><b>Step 2:&nbsp;&nbsp;Select Students To Graduate<b></u></td></tr>
<?php
        }
?>
        <tr><td>&nbsp;</td></tr>
        <tr><td colspan="3">
            <table width="100%">
<?php
        include('gradlist_students_search.php');
?>
            </table>
        </td></tr>
<?php
    }elseif($step == 3){
        if($_POST['editMode']){$finText = 'updated';}else{$finText = 'created';}
?>
        <tr><td align="center">Your graduation list has been <?= $finText ?>.<br><br><a href="index.php?action=gl.edit">Click here to continue.</a></td></tr>
        </table>
<?php
    }
    
    if($step < 3){
?>
        <tr><td>&nbsp;</td></tr>
        <tr><td colspan="4"><hr width="90%"></td></tr>
<?php
	if ($step == 1){
		echo '	<tr><td width="15%">&nbsp;</td><td align="right" width="75%"><input name="gotoNextStep" type="submit" value="OK"></td><td width="10%">&nbsp;</td></tr>';
	}
?>
        </table>
        <input name="editMode" type="hidden" value="<?= $editMode ?>"/>
        <input name="step" type="hidden" value="<?= $step+1 ?>"/>
<?php
        if($step == 2){
            echo '<input name="schoolID" type="hidden" value="' . $schoolID . '"/>';
        }
    }
?>
    </form>
