<?php
    //If there is no step set...
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addMoreStudentsButton'])){
        $editMode = true;
        $step = 3;
        include('gradlist_add.php');
?>
                </td></tr>
                </table>
            </td>
        </tr>
        </table>
<?php
        include('includes/footer.php');
		die();
    }

    //Including DAO Classes
    require_once($classpath . 'GradListDAO.php');
    require_once($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);
    
    //SETTING POST VARS
    $userIDs = $_POST['userIDs'] ?? 0;
    $gradListID = $_POST['gradListID'] ?? 0;    
    $rankUpdateStatus = '';

    //SETTTING REQUEST VARS
    $gsid = $_REQUEST['gsid'] ?? 0;
    if (isset($_POST['gradListID'])) {
    	$glid = $gradListID;
    } else {
    	$glid = $_REQUEST['glid'] ?? 0;
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateListButton'])){
    	
        $idList = explode(',',$userIDs);

        for($ii = 0;$ii < count($idList);$ii++){
            $newRank = $_POST['newRank_'. $idList[$ii]];
            //eval('$newRank = $_POST[\'newRank_' . $idList[$ii]. '\'];');
            $gradListDAO->updateGradRank($gradListID, $idList[$ii], $newRank);
        }
        
        $glid = $gradListID;
        $rankUpdateStatus = "Student's new ranks have been updated.";
    }

	//OLD CODE TO INDIVIDUALLY REMOVE A STUDENT (NON-FINALIZED LIST)
    //if($gsid > 0 && $act2 == 'rm'){
    	//remove a student from a gradlist
    //    $gradListDAO->removeGrad($gsid);
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['removeStudentsButton'])){
    	//remove student(s) from a gradlist
    	foreach($_POST['gsid'] as $value) { 
			$gradListDAO->removeGrad($value); 
		} 
    }elseif($gsid > 0 && $act2 == 'ud'){
    	//Undo a student's graduation
		$undoErrMsg = $gradListDAO->undoGrad($gsid, $scid, $userID, $FULL_GRAD_FEE);
    }

    if($glid > 0){
      	if($action == 'gl.rem'){
      		$gradListDAO->removeGradList($glid);
      		$glid = -1;
      		
			//Returning a list of graduation lists this user has access to
        	$gradListRS = $gradListDAO->getGradLists($_COOKIE["uid"]);      		
		}
		
        $studentListRS = $gradListDAO->getGradStudents($glid);
        $rankListRS = $studentDAO->getRankListByUser($_COOKIE["uid"]);

        if($rankListRS != false){
        	
        	$showRank = true;
        	$ii = 0;
            while($row = mysqli_fetch_array($rankListRS, MYSQLI_ASSOC)){
            	
            	if($showRank){
                	$rankListName[$ii] = $row['rank_name'];
                	$rankListID[$ii++] = $row['id'];
            	}
            	
				//We need to filter out anything above 2nd brown					                                        
                if($row['rank_name'] == '1st Brown'){
                	$showRank = false;	
                }                
            }
        }
    }else{
        //Returning a list of graduation lists this user has access to
        $gradListRS = $gradListDAO->getGradLists($_COOKIE["uid"]);
    }
?>

    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Edit Your Graduation List</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if(strlen($rankUpdateStatus) > 0){
        echo '  <tr><td colspan="2" align="center" class="success">' . $rankUpdateStatus . '</td></tr><tr><td>&nbsp;</td></tr>';
    }

    if($gradListID > 0 || $glid > 0){
		if(isset($undoErrMsg) && strlen($undoErrMsg)>0){
?>
	<tr><td colspan="2" align="center"><span class="error"><?= $undoErrMsg ?></span></td></tr>
<?php 	
		} 
?>
    <tr><td colspan="2">
<?php
        include('gradlist_table.php');
?>
    </td></tr>
<?php
    } else {
?>
    <tr>
        <td width="5%">&nbsp;</td>
        <td align="left">
<?php
        if(mysqli_num_rows($gradListRS) > 0){
            $currentSchool = '';
            $firstSchool = true;
?>
<?php
            while($glRow = mysqli_fetch_array($gradListRS, MYSQLI_ASSOC)){
                if($currentSchool != $glRow['school_id']){
                    $currentSchool = $glRow['school_id'];
                    if(!$firstSchool){echo '            </ul>';}
                    else{$firstSchool = false;}
                    
?>
            <u><b><?= $glRow['school_name'] ?></b></u>
            <ul>
<?php
                }
                
                $gradDate = date("F",strtotime($glRow['grad_date'])) . ' ' . date("j",strtotime($glRow['grad_date'])) . ', ' . date("Y",strtotime($glRow['grad_date']));
				$removeButton = '';
				                
                if($glRow['read_only'] == 0 && $gradListDAO->isGradListPaid($glRow['id']) == 0 ){
					$removeButton = ' <a href="index.php?action=gl.rem&glid=' . $glRow['id'] . '" onclick="return confirm(\'Are you sure you want to remove the graduation list for [' . $gradDate . ']?\');"><img src="' . $imgRoot . 'button_delete.png" border="0" alt="Remove List"></a>';
				}
?>
                <li><a href="index.php?action=gl.edit&scid=<?= $glRow['school_id'] ?>&glid=<?= $glRow['id'] ?>"><?= $gradDate ?></a><?= $removeButton ?></li>
<?php
            }
?>
            </ul>
<?php
        }
?>
        </td>
    </tr>
<?php
    }
?>
    </table>
