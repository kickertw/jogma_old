<?php
    include('includes/formValidation.php');

    //If there is no step set...
    if(!isset($step)){
        $step = 1;
    }

    //Including DAO Classes
    require_once($classpath . 'GradListDAO.php');
    require_once($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);
    
    //Returning a list of schools this user has access to
    $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"]);
    
    $schoolID = $_POST['schoolID'] ?? '';
    $rankID = $_POST['rankID'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $orderBy = $_POST['orderBy'] ?? '';
    $orderDir = $_POST['orderDir'] ?? '';
    $orderBy2 = $_POST['orderBy2'] ?? '';
    $orderDir2 = $_POST['orderDir2'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    	$gradListID = $_POST['gradListID'];
    	$step = $_POST['step'];
    	$gradMo = $_POST['gradMo'];
    	$gradYear = $_POST['gradYear'];
    	$gradDay = $_POST['gradDay'];
    	$editMode = $_POST['editMode'];    	

        if ($step == 2){
            $gradDate = $gradMo . '-' . digitMasker($gradDay) . '-' . $gradYear;
            
            //When $gradListID == -1, a gradDate should be set. 
            if($gradListID == -1){
            	if (datecheck($gradDate) == true){
	            	$gradDate = $gradYear . '-' . $gradMo . '-' . digitMasker($gradDay);
                	$gradListID = $gradListDAO->createBBGradList($gradDate, $_COOKIE["uid"]);                	
            	}else{
                	$errMsg = "[$gradMo-$gradDay-$gradYear] is an invalid date.";
                	$step = 1;
            	}
            	
            	if($gradListID < 1){
            		$errMsg = "The list for [$gradMo-$gradDay-$gradYear] already exists.";
                	$step = 1;
            	}
            }
        } elseif ($step == 3) {
            $studentListRS = $studentDAO->getStudents($schoolID, $rankID, $firstName, $lastName, 1, $orderBy, $orderDir, $orderBy2, $orderDir2, $gradListID, '', false);

            if (!isset($_POST['gotoNextStep'])){
                //Since the "Next >>" button was not pressed...we should remain on the same step
                $step = 2;
                
                if (isset($_POST['addToListButton'])){
                    $studentIDs = $_POST['studentIDs'];
                
                    for($i = 0; $i < count($studentIDs); $i++){
                        $gradListDAO->addBBGrad($gradListID, $studentIDs[$i], $_POST['oldRank_'. $studentIDs[$i]], $_POST['newRank_'.$studentIDs[$i]]);
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
    if($step > 1){
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
        <tr><td align="left" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><b>Step 1:&nbsp;&nbsp;Select/Enter Graduation List Information<b></u></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td align="right" width="15%">Select: </td>
            <td align="left" width="85%" colspan="2">
                <select name="gradListID">
                	<option value="-1">Choose a date</option>
<?php
		$gradRS = $gradListDAO->getAllBBGradLists();
		while($row = mysqlI_fetch_array($gradRS, MYSQLI_ASSOC)){
?>                
                    <option value="<?= $row['id'] ?>"><?= date('F j, Y',strtotime($row['grad_date'])) ?></option>
<?php
		}
?>                    
                </select>
            </td>			
		</tr>
        <tr><td>&nbsp;</td><td align="left"><b>OR</b></td></tr>
        <tr>
            <td align="right" width="15%">Create New: </td>
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
                    <option value="<?= intval(date('Y'))-1 ?>"><?= intval(date('Y'))-1 ?></option>
                    <option value="<?= intval(date('Y')) ?>" SELECTED><?= intval(date('Y')) ?></option>
                    <option value="<?= intval(date('Y'))+1 ?>"><?= intval(date('Y'))+1 ?></option>
                </select>
            </td>
        </tr>
<?php
    } elseif ($step == 2){
?>
        <tr><td align="left" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><b>Step 2:&nbsp;&nbsp;Select Students To Graduate<b></u></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td colspan="3">
            <table width="100%">
<?php
        include('gradlist_bb_students_search.php');
?>
            </table>
        </td></tr>
<?php
    }elseif($step == 3){
        if($editMode){$finText = 'updated';}else{$finText = 'created';}
?>
        <tr>
			<td align="center">Your graduation list has been <?= $finText ?>.<br><br><a href="index.php?action=gl.bbedit">Click here to continue.</a></td>
		</tr>
        </table>
<?php
    }
    
    if($step < 3){
?>
        <tr><td>&nbsp;</td></tr>
        <tr><td colspan="3"><hr width="90%"></td></tr>
        <tr><td width="15%">&nbsp;</td><td align="right" width="75%"><input name="gotoNextStep" type="submit" value="Next >>"></td><td width="10%">&nbsp;</td></tr>
        </table>
        <input name="editMode" type="hidden" value="<?= $editMode ?>"/>
        <input name="step" type="hidden" value="<?= $step+1 ?>"/>
<?php
    }
?>
    </form>
