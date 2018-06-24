<?php
    //Include util functions
    require_once('includes/util.php');

    //Including DAO Classes
    require_once($classpath . 'GradListDAO.php');
    require_once($classpath . 'StudentDAO.php');

    //Server (is it HTTP or HTTPS)
    $paypal_return = 'http://www.jogma.net';
    
    $step =  $_REQUEST['step'] ?? $_POST['step'] ?? 1;
    $glid = $_REQUEST['glid'] ?? $_POST['glid'] ?? 0;
    $gradListID = $_REQUEST['gradListID'] ?? $_POST['gradListID'] ?? 0;

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);
    if (isset($_POST['userIDs'])) {
        $userIDs = $_POST['userIDs'];
    }

    $gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);

    if ($step == 3) {
    	//IF GIVEN A CREDIT...WHAT DO WE DO...
    	if(isset($_REQUEST['pvc']) || isset($_POST['pvc'])){
            $pvc = $_REQUEST['pvc'] ?? $_POST['pvc'];
            $crleft = $_REQUEST['crleft'] ?? 0;
            $amt = $_REQUEST['amtPaid'] ?? $_POST['amtPaid'] ?? 0;
            
	        $schoolRS = $studentDAO->getSchoolByGradList($gradListID);
	        if ($amt == 0 && $crleft > 0) {
                $gradListDAO->updateSchoolCredit($schoolRS['id'], $crleft);
	        } else {
                $gradListDAO->updateSchoolCredit($schoolRS['id'], 0);
	        }            
		}
    
        $isValidPayment = $gradListDAO->priceVerifyFinal($gradListID, $pvc ?? 0);

        if ($isValidPayment){
            $isValidPayment = $gradListDAO->markListPaid($_COOKIE["uid"], $gradListID, $amt);
        }
     }

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (isset($_POST['updateListButton'])) {
            $idList = explode(',', $userIDs ?? '');

            for($ii = 0; $ii < count($idList); $ii++) {
                $newRank = $_POST['newRank_'. $idList[$ii]];
                $gradListDAO->updateGradRank($gradListID, $idList[$ii], $newRank);
            }

            $glid = $gradListID;
            $rankUpdateStatus = "Student's new ranks have been updated.";
            $step = 2;
        } elseif (isset($_POST['updateCurrentRanks'])) {
            $idList = explode(',', $userIDs);

            for($ii = 0;$ii < count($idList);$ii++){
                $gradListDAO->updateGradOldRank($gradListID,$idList[$ii]);
            }

                        $glid = $gradListID;
            $rankUpdateStatus = "Current ranks have been updated.";
            $step = 2;
        } elseif (isset($_POST['mergeButton'])) {
            $updateResultText = '';
            $gradStudentRS = $gradListDAO->getGradStudents($gradListID);
            $setReadOnly = true;

            while($setReadOnly && $row = mysqli_fetch_assoc($gradStudentRS)){
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
                                $gradListDAO->markListReadOnly($gradListID);
                                $gradInfo = $gradListDAO->getGradListByID($gradListID);
                                $studentDAO->decrementSchoolDiplomaCount($gradInfo['school_id'], $gradInfo['diplomas_needed']);
                        }
        }
    }

    if($gsid ?? 0 > 0){
        $gradListDAO->removeGrad($gsid);
    }

    if($glid > 0){
        $studentListRS = $gradListDAO->getGradStudents($glid);
        $rankListRS = $studentDAO->getRankListByUser($_COOKIE["uid"]);

        if($rankListRS != false){
                $showRank = true;
                $ii = 0;
            while($row = mysqli_fetch_assoc($rankListRS)){

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
        $gradListRS = $gradListDAO->getGradLists($_COOKIE["uid"], 0);
    }

    //Setting the text per step...
    if($step == 3){
        $stepText = 'Step 3:&nbsp;&nbsp;Merge the Graduation List with the Main Database';
    }elseif($step == 2){
        $stepText = 'Step 2:&nbsp;&nbsp;Confirm/Review the Graduation List';
    }else{
        $stepText = 'Step 1:&nbsp;&nbsp;Choose a Graduation List';
    }

    if ($step == 3){
        $isGLPaid = $gradListDAO->isGradListPaid($gradListID);
        $nextStep = $step + 1;

        if ($isGLPaid){
                        echo '<form name="gradListEdit" action="index.php?action=' . $action . '&step=' . $nextStep . '" method="POST">';
        }else{
                if ($devMode == 1){
                    echo '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="POST">';
                }else{
                    echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
                }
            }
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
    if (strlen($rankUpdateStatus ?? '') > 0) {
        echo '  <tr><td colspan="2" align="center" class="success">' . $rankUpdateStatus . '</td></tr><tr><td>&nbsp;</td></tr>';
    }

    if ($step == 2 && $glid > 0) {
?>
    <tr><td colspan="2">
<?php
        include('gradlist_table.php');
?>
    </td></tr>
<?php
    } elseif($step == 3) {
        if ($isGLPaid) {
?>
    <tr>
        <td align="center" colspan="2">
            <input name="gradListID" type="hidden" value="<?= $gradListID ?>">
            <input name="mergeButton" type="submit" value="Click Here To Run Rank Update!">
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
        } else {
            //Need to determine amount to pay
            $gradFeeAmount = $gradListDAO->calculateGradFees($gradListID, $_COOKIE["uid"], $FULL_GRAD_FEE);
            //$finalDiscount = $gradListDAO->calculateGradDiscount($gradListID, $FAMILY_DISCOUNT);
            $creditAmount = $gradListDAO->getSchoolCredit($gradListID);
            $amountDue = $gradFeeAmount - $creditAmount;

            $PVCValue = $gradListDAO->priceVerifyStart($gradListID);
            $schoolInfo = $studentDAO->getSchoolByGradList($gradListID);
            $gradInfo = $gradListDAO->getGradListByID($gradListID);
?>
    <tr>
        <td colspan="2" align="center">
            Total Graduation Fees = $<?= convertToCurrency($gradFeeAmount) ?><br>
            <!--Total Family Discount = ($<?= convertToCurrency($finalDiscount) ?>)<br>-->
<?php
            if ($creditAmount > 0){
?>
                Total Credit Amount = $<?= convertToCurrency($creditAmount) ?><br>
<?php
            }

            if ($amountDue > 0){
?>
            <b>Amount Due = $<?= convertToCurrency($amountDue) ?></b><br><br>
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="<?= $JRFEmailAcct ?>">
            <input type="hidden" name="item_name" value="<?= $gradInfo['grad_date']?> Grad Fee Payment (<?= $schoolInfo['city']?>, <?= $schoolInfo['state']?>)">
            <input type="hidden" name="item_number" value="<?= $gradListID ?>">
            <input type="hidden" name="quantity" value="1">
            <input type="hidden" name="amount" value="<?= convertToCurrency($amountDue) ?>">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="rm" value="2">
            <input type="hidden" name="return" value="<?= $paypal_return ?>/index.php?action=gl.merge&amp;amtPaid=<?= convertToCurrency($amountDue) ?>&amp;step=3&amp;gradListID=<?= $gradListID ?>&amp;pvc=<?= $PVCValue ?>">
            <input type="hidden" name="cancel_return" value="<?= $paypal_return ?>/index.php?action=gl.merge&amp;step=5">
            <input type="hidden" name="address1" value="<?= $currentSchool['address1'] ?>">
            <input type="hidden" name="address2" value="<?= $currentSchool['address2'] ?>">
            <input type="hidden" name="city" value="<?= $currentSchool['city'] ?>">
            <input type="hidden" name="state" value="<?= $currentSchool['state'] ?>">
            <input type="hidden" name="zip" value="<?= $currentSchool['postal'] ?>">
            <input type="hidden" name="no_note" value="0">
            <input type="hidden" name="notify_url" value="<?= $paypal_return ?>/paypal/IPN_notify.php">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="bn" value="PP-BuyNowBF">
            <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but6.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<?php
                        }else{
                        	$gradListDAO->insertPaypalTransaction('TW' . rand(1000000000,9999999999) , $gradInfo['grad_date'] . 'Grad Fee Payment (' . $schoolInfo['city'] . ',' . $schoolInfo['state'] . ')', $gradListID, 'n/a', convertToCurrency($amountDue));	
                        
?>
                        <br><a href="index.php?action=gl.merge&crleft=<?= convertToCurrency(-1*$amountDue) ?>&step=3&gradListID=<?= $gradListID ?>&pvc=<?= $PVCValue ?>">Proceed To Next Step</a>
<?php
                        }
?>
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
        }
    } elseif ($step == 4) {
?>
    <tr><td colspan="2" align="center"><?= $updateResultText ?></td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="2" align="center"><a href="index.php?action=gl">Click Here To Continue</a></td></tr>
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
            while($glRow = mysqli_fetch_assoc($gradListRS)){
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
?>
                <li><a href="index.php?action=gl.merge&step=2&scid=<?= $glRow['school_id'] ?>&glid=<?= $glRow['id'] ?>"><?= $gradDate ?></a></li>
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