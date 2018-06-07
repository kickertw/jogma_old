<?php
    include('Application.php');
	include('includes/header.php');
	require_once($classpath . 'StudentDAO.php');
?>
	<form id="VerifyRankForm" method="post" action="rankverify.php">
		<table>
			<tr>
				<td>First Name:</td>
				<td><input name="fName" type="text" value=""></td>
			</tr>
			<tr>
				<td>Last Name:</td>
				<td><input name="lName" type="text" value=""></td>
			</tr>
			<tr>
				<td>Date of Birth:</td>
				<td><input class="datePicker" name="dob" type="text" value=""></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input id="verifyButton" type="submit" value="Go"></td>
			</tr>
		</table>
	</form>

<?php
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$fName = $_POST['fName'];
		$lName = $_POST['lName'];
		$dob = date('Y-m-d', strtotime(str_replace('-', '/', $dob)));
		
		$studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, 1, 1);
		$studentRS = $studentDAO->getStudents(-1, -1, $fName, $lName, -1, '', '', '', '', '', $dob);
		
		$row = mysql_fetch_array($studentRS);
		
		$stid = $row[id];
		
		if ($stid > -1){
			echo 'Record found for ' . $row['first_name'] . ' ' . $row['last_name'];
			include('students_grad_history.php');
		}else{
			echo "No records returned for <b>$fName $lName</b>.<br>If you believe this is an error, please contact 703-532-7433 (Jhoon Rhee of Arlington, VA)";
		}
	}

	include('includes/footer.php');
?>

<script src="scripts/verifyRank.js"></script>