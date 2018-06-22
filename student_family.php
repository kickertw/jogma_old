<?php
    include('includes/formValidation.php');
    include($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $birthDate = digitMasker($dob_mo) . '-' . digitMasker($dob_day) . '-' . $dob_yr;
        $errMsg = addStudentValidate($firstName, $lastName, $beltSize, $birthDate);
        
        if(strlen($errMsg) == 0){
            $birthDate = $dob_yr . '-' . digitMasker($dob_mo) . '-' . digitMasker($dob_day);
            $studentExists = $studentDAO->getStudents($schoolID, 0, $firstName, $lastName, 2, '', '', '', '', 0, $birthDate);

            if(mysqli_num_rows($studentExists) > 0){
                $isUpdated = false;
                $errMsg = "Student with the name [$firstName $lastName] already exists...";
            }else{
                $isUpdated = $studentDAO->insertStudent($firstName, $lastName, $rankID, $beltSize, $schoolID, $birthDate, $isActive);

                if(!$isUpdated){
                    $errMsg = "An error has occurred while trying to add [$firstName $lastName]";
                }
            }
        }
    }

    if (!$isUpdated){
        $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"]);
        $rankListRS = $studentDAO->getRankList();
    }
?>

<form name="studentAdd" action="index.php?action=stu.add" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Add A New Family</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if(isset($isUpdated) && $isUpdated == true){
?>
    <tr><td align="center" colspan="2">Family has been <b>successfully</b> added!</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="center"><a href="index.php?action=stu.add">Add Another</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?action=stu">Goto Top Menu</a></td></tr>
<?php
    }else{
?>
    <tr><td align="center" colspan="2" class="error"><?= $errMsg ?></td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td align="right" width="15%">Family Display Name: </td>
        <td align="left" width="85%"><input name="firstName" type="text" value="<?= $firstName ?>"></td>
    </tr>
    <tr>
        <td align="right" width="15%">School: </td>
        <td align="left" width="85%">
            <select name="schoolID">
    <?php
            if($schoolListRS != false){
                while($row = mysqli_fetch_assoc($schoolListRS)){
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
    <?php
            if($rankListRS != false){
                while($row = mysqli_fetch_assoc($rankListRS)){
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
                <option value="1" SELECTED>Active</option>
                <option value="0">Inactive</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" width="15%">Belt Size: </td>
        <td align="left" width="85%"><input name="beltSize" type="text" value="<?= $beltSize ?>" size="3"></td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td><input name="addButton" type="submit" value="Add Student">&nbsp;<input name="cancelButton" type="reset" value="Reset"></td></tr>
<?php
    }
?>
    </table>
</form>
