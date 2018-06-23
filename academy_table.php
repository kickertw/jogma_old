<?php
    $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"], 0, 'name');
?>

<form name="schoolQuery" action="index.php?action=am" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Academy Manager</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if(isset($errorMsg) && strlen($errorMsg) > 0){
?>
    <tr><td align="center" colspan=2 class="error"><?= $errorMsg ?></td></tr>
    <tr><td>&nbsp;</td></tr>
<?php
    }
?>
    <tr>
        <th><u>Manage Academies</u></th>
    </tr>
    <tr><td align="right"><br><u>Load an Academy</u><br></td></tr>
    <tr>
        <td align="right" width="25%">Academy Load:</td>
        <td align="left" width="75%">
            <select name="schoolID">
                <option value="-1">- Choose an academy -</option>
<?php
            while($row = mysqli_fetch_array($schoolListRS, MYSQLI_ASSOC)){
                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
            }
?>
            </select>
            &nbsp;<input name="loadSchoolButton" type="submit" value="Go">&nbsp;<input name="removeSchoolButton" type="submit" value="Remove" onclick="return confirm('Are you sure you want to remove this academy?');">
        </td>
    </tr>
    <tr><td align="right"><br><u>Add/Edit an Academy</u><br></td></tr>
    <tr>
        <td align="right" width="25%">Code: </td>
        <td align="left" width="75%"><input name="code" type="text" value="<?= $loadRow['location_code'] ?>" maxlength="5" size="5"></td>
    </tr>
    <tr>
        <td align="right" width="25%">Full Name: </td>
        <td align="left" width="75%"><input name="name" type="text" value="<?= $loadRow['name'] ?>" maxlength="200" size="50"></td>
    </tr>
    <tr>
        <td align="right" width="25%">Address 1: </td>
        <td align="left" width="75%"><input name="addy1" type="text" value="<?= $loadRow['address1'] ?>" maxlength="200" size="50"></td>
    </tr>
    <tr>
        <td align="right" width="25%">Address 2: </td>
        <td align="left" width="75%"><input name="addy2" type="text" value="<?= $loadRow['address2'] ?>" maxlength="200" size="50"></td>
    </tr>
    <tr>
        <td align="right" width="25%">City, State: </td>
        <td align="left" width="75%">
			<input name="city" type="text" value="<?= $loadRow['city'] ?>" maxlength="50" size="15">,&nbsp;
			<input name="state" type="text" value="<?= $loadRow['state'] ?>" maxlength="2" size="2">
		</td>
    </tr>
    <tr>
        <td align="right" width="25%">Postal Code: </td>
        <td align="left" width="75%"><input name="postal" type="text" value="<?= $loadRow['postal'] ?>" maxlength="10" size="10"/></td>
    </tr>
    <tr>
        <td align="right" width="25%">Country: </td>
        <td align="left" width="75%"><input name="country" type="text" value="<?= $loadRow['country'] ?>" maxlength="50" size="10"/></td>
    </tr>	    
    <tr>
        <td align="right" width="25%">Point of Contact (E-mail): </td>
        <td align="left" width="75%"><input name="poc" type="text" value="<?= $loadRow['poc'] ?>"  maxlength="100" size="35"/></td>
    </tr>
    <tr>
    	<td align="right" width="25%">Parent Academy: </td>
        <td align="left" width="75%">
        	<select name="parentSchoolID">
        		<option value="">-</option>        		
        	</select>
        </td>
    </tr>
    <tr>
        <td align="right" width="25%">Status: </td>
        <td align="left" width="75%">
            <select name="isActive">
                <option value="1" <?php if(isset($loadRow) && $loadRow['active'] == '1'){echo 'SELECTED';} ?>>Active</option>
                <option value="0" <?php if(isset($loadRow) && $loadRow['active'] ?? 1 == '0'){echo 'SELECTED';} ?>>Inactive</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right" width="25%">Credit: </td>
        <td align="left" width="75%">$<input name="credit" type="text" value="<?= $loadRow['credit'] ?>" maxlength="7" size="6"/></td>
    </tr>    
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td>&nbsp;</td>
        <td>
<?php
        if (isset($_POST['loadSchoolButton'])) {
            echo '<input name="updateSchoolID" type="hidden" value="' . $schoolID . '">';
            echo '<input name="updateSchoolButton" type="submit" value="Update">&nbsp;&nbsp;<b>OR</b>&nbsp;&nbsp;<a href="index.php?action=am">Goto Add Mode</a>';
        } else {
            echo '<input name="updateSchoolButton" type="submit" value="Add">';
        }
?>
        </td>
    </tr>
    </table>
</form>
