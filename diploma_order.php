<?php
	//Include util functions
	require_once('includes/util.php');

	//Including DAO Classes
    require_once($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);    
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
      	if (isset($orderButton)){
		    if ($devMode == 1){
		      	//echo '<form action="http://www.wongism.com/jriworld/paypal/testPay.php" method="POST">';
		        echo '<form action="http://www.sandbox.paypal.com/cgi-bin/webscr" method="POST">';
	            $testData = 'Please use the following test credit card for successful payment:<br>';
	            $testData .= 'Visa = 4141164407036934<br>Exp = 1/2007<br>CVV = 000<br><br>';	        
		    }else{
		        echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
		    }
		}else{
			echo '<form action="index.php?action=gl.order" method="POST">';
		}
	}else{
	  	echo '<form action="index.php?action=' . $action . '" method="POST">';
	}
?>	
        <table align="left" bgcolor="White" width="100%">
        <tr>
            <th class="title" colspan="3">Purchase UnderBelt Diplomas</th>
        </tr>
        <tr><td>&nbsp;</td></tr>
<?php
    if(strlen($errMsg) > 0){
?>
        <tr><td align="center" colspan="3" class="error"><?= $errMsg ?></td></tr>
        <tr><td>&nbsp;</td></tr>
<?php
    }
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST'){
      	$schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"]); 
?>
	    <tr>
	        <td align="right" width="25%">Choose your academy:</td>
	        <td align="left" width="75%">
	            <select name="schoolID">
<?php
	            while($row = mysqli_fetch_array($schoolListRS, MYSQLI_ASSOC)){
	                echo '<option value="' . $row['id'] . '">' . $row['location_code'] . ' - ' . $row['name'] . '</option>';
	            }
?>
	            </select>
	        </td>
	    </tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td align="left">
				<input name="gotoStep2" type="submit" value="Next >>>"/>
			</td>
		</tr>
<?php
	}else{
	  	if (isset($gotoStep2)){
	    	$schoolInfo = $studentDAO->getSchool($schoolID);
?>
		<tr>
			<td colspan="2"><u><b>Choose your order quantity</b></u><br><br></td>
		</tr>
	    <tr>
	        <td align="right" width="25%">Academy:</td>
	        <td align="left" width="75%"><b><?= $schoolInfo['name'] ?></b></td>
	    </tr>
	    <tr>
	        <td align="right" width="25%">Quantity Remaining:</td>
	        <td align="left" width="75%"><?= $schoolInfo['diploma_count'] ?></td>
	    </tr>
	    <tr>
			<td align="right" width="25%">Quantity To Order:</td>
			<td align="left" width="75%">
				<select name="qty">				
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="30">30</option>
					<option value="40">40</option>
					<option value="50">50</option>
					<option value="60">60</option>
					<option value="70">70</option>
					<option value="80">80</option>
					<option value="90">90</option>
					<option value="100">100</option>
				</select>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td align="left">
				<input name="schoolID" type="hidden" value="<?= $schoolID ?>"/>
				<input name="orderButton" type="submit" value="Order Diplomas"/>
			</td>
		</tr>					    
<?php	
//			}else{
?>
<!--
	    <tr>
			<td align="right" width="25%" valign="top">Quantity To Order:</td>
			<td align="left" width="75%" valign="top"><b>0</b> (You cannot reorder diplomas until the remaining quantity is less than 80% of your active student body.)</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td align="left">
				<a href="index.php?action=gl">Goto Grad List Manager</a>
			</td>
		</tr>
-->		
<?php			  
//			}	    
		}elseif (isset($orderButton)){
		  	$currentSchool = $studentDAO->getSchool($schoolID);  
		  	
		  	//hidden check value to prevent spoofing
		  	$checkSum = createRandomPassword(15);
		  	$studentDAO->diplomaOrderVerifyStart($schoolID, $checkSum);
?>
		<tr>
			<td colspan="2"><u><b>Confirm Your Order</b></u></td>
		</tr>
	    <tr>
	        <td align="right" width="25%">Academy:</td>
	        <td align="left" width="75%"><?= $currentSchool['name'] ?></td>
	    </tr>
	    <tr>
			<td align="right" width="25%">Quantity:</td>
			<td align="left" width="75%"><?= $qty ?></td>
		</tr>
	    <tr>
			<td align="right" width="25%">Total Cost:</td>
			<td align="left" width="75%"><b>$<?= convertToCurrency($qty * $DIPLOMA_FEE) ?></b></td>
		</tr>		
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td align="left">
				<?= $testData ?>
	            <input type="hidden" name="cmd" value="_xclick">
	            <input type="hidden" name="business" value="jrfpayments@gmail.com">
	            <input type="hidden" name="item_name" value="Diploma Purchase Order (<?= $currentSchool['city']?>, <?= $currentSchool['state']?>)">
	            <input type="hidden" name="quantity" value="<?= $qty ?>">
	            <input type="hidden" name="amount" value="<?= convertToCurrency($DIPLOMA_FEE) ?>">
	            <input type="hidden" name="rm" value="2">
	            <input type="hidden" name="return" value="https://www.jogma.net/index.php?action=gl.order&amp;success=1&amp;cs=<?= $checkSum ?>&amp;scid=<?= $schoolID ?>&amp;qy=<?= $qty ?>">
	            <input type="hidden" name="cancel_return" value="https://www.jogma.net/index.php?action=gl.order&amp;success=0">
				<input type="hidden" name="address1" value="<?= $currentSchool['address1'] ?>">
				<input type="hidden" name="address2" value="<?= $currentSchool['address2'] ?>">
				<input type="hidden" name="city" value="<?= $currentSchool['city'] ?>">
				<input type="hidden" name="state" value="<?= $currentSchool['state'] ?>">
				<input type="hidden" name="zip" value="<?= $currentSchool['postal'] ?>">
				<input type="hidden" name="no_note" value="0">
				<input type="hidden" name="no_shipping" value="2">
				<input type="hidden" name="currency_code" value="USD">
	            <input type="hidden" name="bn" value="PP-BuyNowBF">
	            <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but6.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
			</td>
		</tr>
<?php	    
		}else{
		  	$isValid = $studentDAO->diplomaOrderVerifyFinal($scid, $cs);
		  	if($success==1 && $isValid){
		  	  	//Increase the diploma count
		  	  	$studentDAO->updateSchoolDiplomaCount($scid, $cs, $qy);
		  	  	
		  	  	//E-Mail Diploma Manager
		  	  	$sInfo = $studentDAO->getSchool($scid);
				$today = date("F j, Y, g:i a");
				$subject = 'JRI Diploma Purchase - ' . $today;
				
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				
				// Additional headers
				$headers .= 'To: ' . $to . "\r\n";
				$headers .= 'From: JhoonRhee.com <do-not-reply@jhoonrhee.com>' . "\r\n";
				
				$message = '
				<html>
				<head>
				  <title>JRI Certification Registrant (' . $today . ')</title>
				</head>
				<body>
					<table width="80%" align="left">
					<tr><th colspan="2" align="left">Ship To:</th></tr>
					<tr>
						<td width="10%">&nbsp;</td>
						<td align="left">
							' . $sInfo['name'] . '<br>
							' . $sInfo['address1'] . '<br>
							' . $sInfo['address2'] . '<br>
							' . $sInfo['city'] . ', ' . $sInfo['state'] . ' ' . $sInfo['postal'] . '<br>
							' . $sInfo['country'] . '<br>
							' . $sInfo['phone'] . '
						</td>
					</tr>
					<tr><th colspan="2" align="left">Diploma Information:</th></tr>
					<tr>
						<td width="10%">&nbsp;</td>
						<td align="left">
							Diploma quantity = ' . $qy . '
						</td>
					</tr>					
					</table>
				</body></html>
				';				
				
				// Mail it
				mail($diplomaMgrEmail, $subject, $message, $headers);				
?>
		<tr><td align="center" colspan="2">Your Diploma Request payment was successful!<br><br><a href="index.php?action=gl">Goto the main menu</a></td></tr>
<?php		  
			}elseif(isset($success) && $success==0){
?>
		<tr>
			<td align="center" colspan="2">
				Your Diploma Request was not successful.<br>If your payment did process successfully, please contact the system administrator.
				<br><br><a href="index.php?action=gl">Goto the main menu</a>
			</td>
		</tr>
<?php			  
			}elseif($success==1 && $isValid == 0){
?>
		<tr>
			<td align="center" colspan="2">
				Your Diploma Request payment was successful, however an error has occurred.<br>Please notify your system administrator!
				<br><br><a href="index.php?action=gl">Goto the main menu</a>
			</td>
		</tr>	
<?php			  
			}
		}
	}
?>
		</table>
	</form>
