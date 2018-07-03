<?php
        //Include util functions
        require_once('includes/util.php');

    //Including DAO Classes
    require_once($classpath . 'GradListDAO.php');
    require_once($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);

    $glid = $_REQUEST['glid'] ?? '';
    $gsid = $_REQUEST['gsid'] ?? '';
    $step = $_REQUEST['step'] ?? '';
    $rankUpdateStatus = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['updateListButton'])){
            $idList = explode(',',$userIDs);

            for($ii = 0;$ii < count($idList);$ii++){
                eval('$newRank = $newRank_' . $idList[$ii]. ';');
                $gradListDAO->updateBBGradRank($gradListID,$idList[$ii],$newRank);
            }

            $glid = $_POST['gradListID'];
            $rankUpdateStatus = "Student's new ranks have been updated.";
            $step = 2;
        }elseif(isset($_POST['mergeButton'])){
            $updateResultText = '';
            $gradStudentRS = $gradListDAO->getBBGradStudents($_POST['gradListID']);
            $setReadOnly = true;

            while($setReadOnly && $row = mysqli_fetch_array($gradStudentRS, MYSQLI_ASSOC)){
                //Unable to Run updateStudentRankByGraduation(...) because of mySQL version...
                //we may be able to use this the mySQL version is updated...
                //
                //$updateResult = $studentDAO->updateStudentRankByGraduation($gradListID, $row['id']);
                $updateResult = $studentDAO->updateStudentRank($row['id'], $row['new_rank_id']);

                if($updateResult){
                    $updateResultText .= $row['first_name'] . ' ' . $row['last_name'] . ' has been updated<br>';
                }else{
                    $updateResultText .= $row['first_name'] . ' ' . $row['last_name'] . ' could NOT be updated<br>';
                    $setReadOnly = false;
                }
            }

            if ($setReadOnly){
                                $gradListDAO->markBBListReadOnly($_POST['gradListID']);
                        }
        }
    }

    if($gsid > 0){
        $gradListDAO->removeBBGrad($gsid);
        $step = 2;
    }

    if($glid > 0){
        $studentListRS = $gradListDAO->getBBGradStudents($glid);
        $rankListRS = $studentDAO->getBBCandidateRankList();

        if($rankListRS != false){
            $ii = 0;
            while($row = mysqli_fetch_assoc($rankListRS)){
                      $rankSeq[$ii] = intval($row['sequence']);
                $rankListName[$ii] = $row['rank_name'];
                $rankListID[$ii++] = $row['id'];
            }
        }
    }else{
        //Returning a list of graduation lists
        $gradListRS = $gradListDAO->getAllBBGradLists();
    }

    //Setting the text per step...
    if($step == 4){
        $stepText = 'Step 4:&nbsp;&nbsp;Black Belt Gradation Merge Complete';
    }elseif($step == 3){
        $stepText = 'Step 3:&nbsp;&nbsp;Merge the Graduation List with the Main Database';
    }elseif($step == 2){
                if($gradListDAO->isBBGradListReadOnly($glid)){
                        $stepText = 'Step 2:&nbsp;&nbsp;Review Download the Graduation List';
        }else{
                        $stepText = 'Step 2:&nbsp;&nbsp;Confirm/Review the Graduation List';
                }
    }else{
        $stepText = 'Step 1:&nbsp;&nbsp;Choose a Graduation List';
    }

    if ($step == 3){
        $nextStep = $step + 1;
                echo '<form name="gradListEdit" action="index.php?action=' . $action . '&step=' . $nextStep . '" method="POST">';
    }
?>
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Merge Your Graduation List</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="left" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><b><?= $stepText ?><b></u></td></tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if(strlen($rankUpdateStatus) > 0) {
        echo '  <tr><td colspan="2" align="center" class="success">' . $rankUpdateStatus . '</td></tr><tr><td>&nbsp;</td></tr>';
    }

    if($step == 2 && $glid > 0){
?>
    <tr><td colspan="2">
<?php
        include('gradlist_bb_table.php');
?>
    </td></tr>
<?php
    }elseif($step == 3){
?>
    <tr>
        <td align="center" colspan="2">
            <input name="gradListID" type="hidden" value="<?= $_POST['gradListID'] ?>">
            <input name="mergeButton" type="submit" value="Click Here To Run Rank Update!">
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    }elseif($step == 4){
?>
    <tr><td colspan="2" align="center"><?= $updateResultText ?></td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="2" align="center"><a href="index.php?action=gl.bbedit&glid=<?= $_POST['gradListID'] ?>&step=2">Click Here To Continue</a></td></tr>
<?php
    }else{
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
            while($glRow = mysqli_fetch_assoc($gradListRS)){
//                 if ($currentSchool != $glRow['school_id']) {
//                     $currentSchool = $glRow['school_id'];
//                     if (!$firstSchool) { echo '            </ul>'; }
//                     else { $firstSchool = false; }

// ?>
//             <u><b><?= $glRow['school_name'] ?></b></u>
//             <ul>
// <?php
//                 }
                $gradDate = date("F",strtotime($glRow['grad_date'])) . ' ' . date("j",strtotime($glRow['grad_date'])) . ', ' . date("Y",strtotime($glRow['grad_date']));
?>
                <li><a href="index.php?action=gl.bbedit&step=2&glid=<?= $glRow['id'] ?>"><?= $gradDate ?></a></li>
<?php
            }
?>
            </ul>
<?php
        }else{
                        echo '<span class="error">No graduation lists are available to be merged.</span>';
                }
?>
        </td>
    </tr>
<?php
    }
?>
    </table>

<?php
    if ($step == 3){echo '</form>';}
?>