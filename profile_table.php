<?php
    $userRow = $userDAO->getUserInfo($_COOKIE["uid"]);
    $userListRS = $userDAO->getUserList($_COOKIE["uid"]);
    $errorMsg = '';
?>

<form name="profileQuery" action="index.php?action=pro" method="POST">
    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Profile Manager</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if (strlen($errorMsg) > 0) {
?>
    <tr><td align="center" colspan=2 class="error"><?= $errorMsg ?></td></tr>
    <tr><td>&nbsp;</td></tr>
<?php
    }

    if($isAdmin == 1 || $userRow['access_level'] == $UAL_USER_MANAGER){
        $schoolListRS = $studentDAO->getSchoolList($_COOKIE["uid"],1);
?>
    <tr>
        <th><u>Manage Users</u></th>
    </tr>
    <tr><td align="right"><br><u>Load a User</u><br></td></tr>
    <tr>
        <td align="right" width="20%">User Load:</td>
        <td align="left" width="80%">
            <select name="e_userID">
                <option value="-1">&lt;Choose A User&gt;</option>
<?php
            while($row = mysqli_fetch_assoc($userListRS)){
                echo '<option value="' . $row['id'] . '">' . $row['username'] . '</option>';
            }
?>
            </select>
            &nbsp;<input name="loadUserButton" type="submit" value="Go">
        </td>
    </tr>
    <tr><td align="right"><br><u>Add/Edit User</u><br></td></tr>
    <tr>
        <td align="right" width="20%">Status: </td>
        <td align="left" width="80%">
<?
    $userIsActive = '';
    $userOnProb = '';
    $userIsNotActive = 'SELECTED';

    if ($e_userRow['active'] == 1) {
        $userIsActive = 'SELECTED';
        $userIsNotActive = '';
        $userOnProb = '';
    } elseif ($e_userRow['active'] == 2) {
      	$userOnProb = 'SELECTED';
      	$userIsActive = '';
      	$userIsNotActive = '';
    }
?>
            <select name="e_status">
                <option value="0" <?= $userIsNotActive ?>>Inactive</option>
                <option value="1" <?= $userIsActive ?>>Active</option>
                <option value="2" <?= $userOnProb ?>>Probationary</option>
            </select>
        </td>
    </tr>
    <tr>
    	<td align="right" width="20%">Access Level: </td>
    	<td align="left" width="80%">
    		<select name="e_accesslvl">
<?php
		for ($ii=0; $ii < sizeof($accessLevelVal); $ii++){
		  	$optText = '<option value="' . $ii . '"';
		  	if($e_userRow['access_level'] == $accessLevelVal[$ii]){
			    	$optText .= ' SELECTED';
			}
			$optText .= ">$accessLevelName[$ii]</option>\r\n";
			echo "			$optText";
		}
?>    			
    		</select>
    	</td>
    </tr>
    <tr>
        <td align="right" width="20%">Username: </td>
        <td align="left" width="80%"><input name="e_username" type="text" value="<?= $e_userRow['username'] ?>" maxlength="100" size="25"></td>
    </tr>
    <tr>
        <td align="right" width="20%">Full Name: </td>
        <td align="left" width="80%"><input name="e_fullname" type="text" value="<?= $e_userRow['fullname'] ?>" maxlength="100" size="25"></td>
    </tr>
    <tr>
        <td align="right" width="20%">E-mail: </td>
        <td align="left" width="80%"><input name="e_email" type="text" value="<?= $e_userRow['email'] ?>"  maxlength="100" size="35"></td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr valign="bottom">
        <td align="right" width="20%">New Password: </td>
        <td align="left" width="80%"><input name="e_pass1" type="password" size="15" maxlength="20"></td>
    </tr>
    <tr valign="bottom">
        <td align="right" width="20%">Verify Pasword: </td>
        <td align="left" width="80%"><input name="e_pass2" type="password" size="15" maxlength="20"></td>
    </tr>
    <tr>
	    <td align="right" width="25%">Belt Rank Display: </td>
        <td align="left" width="75%">
        	<select name="e_rank_display">
        		<option value="0"<?php if($e_userRow['show_advanced_ranks'] == 0){echo ' SELECTED';} ?>>Show Solid Ranks Only</option>
        		<option value="1"<?php if($e_userRow['show_advanced_ranks'] == 1){echo ' SELECTED';} ?>>Show All Ranks</option>
        	</select>
		</td>
	</tr>    
    <tr><td align="right"><br><u>User Access Rights</u><br></td></tr>
<?php
    while($row2 = mysqli_fetch_assoc($schoolListRS)){
        $accessValue = $studentDAO->getSchoolAccess($e_userID, $row2['id']);
?>
    <tr>
        <td align="right"><?= $row2['location_code'] ?> =</td>
        <td align="left">
            <select name="sch_axs_<?= $row2['id'] ?>">
                <option value="0" <?php if($accessValue == 0){echo 'SELECTED';} ?>>No Access</option>
                <option value="1" <?php if($accessValue == 1){echo 'SELECTED';} ?>>Read Only</option>
                <option value="2" <?php if($accessValue == 2){echo 'SELECTED';} ?>>Read/Write</option>
            </select>
        </td>
    </tr>
<?php
    }
?>
    <tr>
        <td>&nbsp;</td>
        <td>
<?php
        if(isset($loadUserButton)){
            echo '<input name="updateUserID" type="hidden" value="' . $e_userID . '">';
            echo '<input name="updateUserButton" type="submit" value="Update User">';
            echo '&nbsp;&nbsp; <b>OR</b> &nbsp;&nbsp;<a href="index.php?action=pro">Goto Add Mode</a>';
        }else{
            echo '<input name="updateUserButton" type="submit" value="Add User">';
        }
?>
        </td>
    </tr>
    <tr><td colspan="2"><br><hr><br></td></tr>
<?php
    }
?>
    <tr>
        <th><u>Your Profile</u></th>
    </tr>
    <tr>
        <td align="right" width="20%">Username: </td>
        <td align="left" width="80%"><i><b><?= $userRow['username'] ?></b></i></td>
    </tr>
    <tr>
        <td align="right" width="20%">Full Name: </td>
        <td align="left" width="80%"><input name="fullname" type="text" value="<?= $userRow['fullname'] ?>" maxlength="100" size="25"></td>
    </tr>
    <tr>
        <td align="right" width="20%">E-mail: </td>
        <td align="left" width="80%"><input name="email" type="text" value="<?= $userRow['email'] ?>"  maxlength="100" size="35"></td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr valign="bottom">
        <td align="right" width="20%">New Password: </td>
        <td align="left" width="80%"><input name="pass1" type="password" size="15" maxlength="20"></td>
    </tr>
    <tr valign="bottom">
        <td align="right" width="20%">Verify Pasword: </td>
        <td align="left" width="80%"><input name="pass2" type="password" size="15" maxlength="20"></td>
    </tr>
    <tr>
	    <td align="right" width="25%">Belt Rank Display: </td>
        <td align="left" width="75%">
        	<select name="rank_display">
        		<option value="0"<?php if($userRow['show_advanced_ranks'] == 0){echo ' SELECTED';} ?>>Show Payable Ranks Only</option>
        		<option value="1"<?php if($userRow['show_advanced_ranks'] == 1){echo ' SELECTED';} ?>>Show All Ranks</option>
        	</select>
		</td>
	</tr>    
    <tr><td>&nbsp;</td><td><input name="updateProfileButton" type="submit" value="Update"></td></tr>
    </table>
    
    <input name="profile_access_level" type="hidden" value="<?= $userRow['access_level'] ?>"/>
    <input name="username" type="hidden" value="<?= $userRow['username'] ?>"/>
    <input name="e_access" type="hidden" value="<?= $userRow['access_level'] ?>"/>
</form>
