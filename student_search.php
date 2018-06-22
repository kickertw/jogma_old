<?php
    include($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);    
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);

    $schoolID = 0;	
    $rankID = 0;
    $firstName = '';    	
    $lastName = '';
    $isActive = '';
    $orderBy = '';
    $orderDir = '';
    $orderBy2 = '';
    $orderDir2 = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    	$schoolID = $_POST['schoolID'];	
    	$rankID = $_POST['rankID'];
    	$firstName = $_POST['firstName'];    	
    	$lastName = $_POST['lastName'];
    	$isActive = $_POST['isActive'];
    	$orderBy = $_POST['orderBy'];
    	$orderDir = $_POST['orderDir'];
    	$orderBy2 = $_POST['orderBy2'];
    	$orderDir2 = $_POST['orderDir2'];		    	    	
    
    	if (isset($_POST['searchButton'])){
	        $studentListRS = $studentDAO->getStudents($schoolID, $rankID, $firstName, $lastName, $isActive, $orderBy, $orderDir, $orderBy2, $orderDir2);
	        
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'firstName', $firstName);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'lastName', $lastName);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'schoolID', $schoolID);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'rankID', $rankID);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'isActive', $isActive);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'orderBy', $orderBy);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'orderDir', $orderDir);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'orderBy2', $orderBy2);
	        $userDAO->saveSearchVar($_COOKIE["uid"], 'orderDir2', $orderDir2);
	    }elseif (isset($_POST['returnToSearch'])){
	        $firstName = $userDAO->getSearchVar($_COOKIE["uid"], 'firstName');
	        $lastName = $userDAO->getSearchVar($_COOKIE["uid"], 'lastName');
	        $schoolID = $userDAO->getSearchVar($_COOKIE["uid"], 'schoolID');
	        $rankID = $userDAO->getSearchVar($_COOKIE["uid"], 'rankID');
	        $isActive = $userDAO->getSearchVar($_COOKIE["uid"], 'isActive');
	        $orderBy = $userDAO->getSearchVar($_COOKIE["uid"], 'orderBy');
	        $orderDir = $userDAO->getSearchVar($_COOKIE["uid"], 'orderDir');
	        $orderBy2 = $userDAO->getSearchVar($_COOKIE["uid"], 'orderBy2');
	        $orderDir2 = $userDAO->getSearchVar($_COOKIE["uid"], 'orderDir2');
	        
	        $studentListRS = $studentDAO->getStudents($schoolID, $rankID, $firstName, $lastName, $isActive, $orderBy, $orderDir, $orderBy2, $orderDir2);
	    }elseif (isset($_POST['setToActive']) || isset($_POST['setToInactive'])){
	      	$idString = '0';
	      	$studentIDs = $_POST['studentIDs'];
	      	
            for($i = 0; $i < count($studentIDs); $i++){
            	$idString .= ',' . $studentIDs[$i];
          	}
        
        	if(isset($_POST['setToActive'])){
				$currentStatus = 1;
			}else{
			  	$currentStatus = 0;
			}
			
        	if(strlen($idString) > 1){
				$studentDAO->updateStudentStatus($idString, $currentStatus);
			}
			
			$studentListRS = $studentDAO->getStudents($schoolID, $rankID, $firstName, $lastName, $isActive, $orderBy, $orderDir, $orderBy2, $orderDir2);
	    }
    }
    
    $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"]);
    $rankListRS = $studentDAO->getRankListByUser($_COOKIE["uid"], 1);
?>

<form name="studentQuery" action="index.php?action=stu.search" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Your Students</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="right"><u><b>Filter By<b></u></td></tr>
    <tr>
        <td align="right" width="15%">First Name: </td>
        <td align="left" width="85%"><input name="firstName" type="text" value="<?= $firstName ?>"></td>
    </tr>
    <tr>
        <td align="right" width="15%">Last Name: </td>
        <td align="left" width="85%"><input name="lastName" type="text" value="<?= $lastName ?>"></td>
    </tr>
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
    <tr>
        <td align="right" width="15%">Rank: </td>
        <td align="left" width="85%">
            <select name="rankID">
                <option value="0">All Ranks</option>
    <?php
            if($rankListRS != false){
                while($row = mysqli_fetch_array($rankListRS, MYSQLI_ASSOC)){
    ?>
                <option value="<?= $row['id'] ?>" <?php if($rankID == $row['id']){echo 'SELECTED';} ?>><?= $row['rank_name'] ?></option>
    <?php
                }
            }
    ?>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" width="15%">Status: </td>
        <td align="left" width="85%">
            <select name="isActive">
                <option value="1" <?php if($isActive == '1'){echo 'SELECTED';} ?>>Active Only</option>
                <option value="0" <?php if($isActive == '0'){echo 'SELECTED';} ?>>Inactive Only</option>
                <option value="-1" <?php if($isActive == '-1'){echo 'SELECTED';} ?>>Active & Inactive</option>
            </select>
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="right"><u><b>Order By<b></u></td></tr>
    <tr>
        <td align="right" width="15%">Primary: </td>
        <td align="left">
            <select name="orderBy">
                <option value=" ">&lt;Choose A Field...&gt;</option>
                <option value="id" <?php if($orderBy == 'id'){echo 'SELECTED';} ?>>Student ID</option>
                <option value="first_name" <?php if($orderBy == 'first_name'){echo 'SELECTED';} ?>>First Name</option>
                <option value="last_name" <?php if($orderBy == 'last_name'){echo 'SELECTED';} ?>>Last Name</option>
                <option value="sequence" <?php if($orderBy == 'sequence'){echo 'SELECTED';} ?>>Rank</option>
                <option value="belt_size" <?php if($orderBy == 'belt_size'){echo 'SELECTED';} ?>>Belt Size</option>
            </select>
            &nbsp;&nbsp;
            <select name="orderDir">
                <option></option>
                <option value="ASC" <?php if($orderDir == 'ASC'){echo 'SELECTED';} ?>>Ascending</option>
                <option value="DESC" <?php if($orderDir == 'DESC'){echo 'SELECTED';} ?>>Descending</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" width="15%">Secondary: </td>
        <td align="left">
            <select name="orderBy2">
                <option value=" ">&lt;Choose A Field...&gt;</option>
                <option value="id" <?php if($orderBy2 == 'id'){echo 'SELECTED';} ?>>Student ID</option>
                <option value="first_name" <?php if($orderBy2 == 'first_name'){echo 'SELECTED';} ?>>First Name</option>
                <option value="last_name" <?php if($orderBy2 == 'last_name'){echo 'SELECTED';} ?>>Last Name</option>
                <option value="sequence" <?php if($orderBy2 == 'sequence'){echo 'SELECTED';} ?>>Rank</option>
                <option value="belt_size" <?php if($orderBy2 == 'belt_size'){echo 'SELECTED';} ?>>Belt Size</option>
            </select>
            &nbsp;&nbsp;
            <select name="orderDir2">
                <option></option>
                <option value="ASC" <?php if($orderDir2 == 'ASC'){echo 'SELECTED';} ?>>Ascending</option>
                <option value="DESC" <?php if($orderDir2 == 'DESC'){echo 'SELECTED';} ?>>Descending</option>
            </select>
        </td>
    </tr>
    <tr><td>&nbsp;</td><td><input name="searchButton" type="submit" value="Search"></td></tr>
    <tr><td colspan="2"><br><hr width="90%"><br></td></tr>
    <tr>
        <td colspan="2" align="center">
            <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                    include('students_table.php');
                }else{
                    echo "Hit 'Go' to view all your students at once!";
                }
            ?>
        </td>
    </tr>
    </table>
</form>
