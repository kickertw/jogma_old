<?php
    if (isset($scid)){
        $schoolID = $scid;
    }
    
    if (isset($glid)){
        $gradListID = $glid;
    }
    
?>

<form name="gradListEdit" action="index.php?action=<?= $action ?>" method="POST">
<table align="center" width="90%" bgcolor="000000">
<tr><td class="goldText" align="center">Your Black Belt Candidates...</td></tr>
<tr><td>
    <table align="center" cellspacing="1" width="100%">
    <tr bgcolor="C0C0C0">
        <th width="5%">&nbsp;</th>
        <th width="40%">Name</th>
        <th width="30%">Current Rank</th>
        <th width="25%">New Rank</th>
    </tr>
<?php
    while($row = mysqli_fetch_assoc($studentListRS)){
        if(!isset($userIDs)){
            $userIDs = $row['id'];
        }else{
            $userIDs .= ',' . $row['id'];
        }
            
?>
        <tr bgcolor="white">
            <td width="5%" align="center">
<?php
		if(!$gradListDAO->isBBGradListReadOnly($gradListID)){
?>
				<a href="index.php?action=gl.bbedit&glid=<?= $glid ?>&gsid=<?= $row['gs_id'] ?>" onclick="return confirm('Are you sure you want to remove [<?= $row['first_name'] . ' ' . $row['last_name'] ?>] from this list?');"><img src="<?= $imgRoot ?>button_delete.png" border="0" alt="Remove Student"></a>
<?php
		}
?>				
			</td>
            <td width="40%" align="left"><?= $row['first_name'] ?> <?= $row['middle_init'] ?> <?= $row['last_name'] ?></td>
            <td width="30%" align="center"><?= $row['old_rank_name'] ?><input name="oldRank_<?= $row['id'] ?>" type="hidden" value="<?= $row['old_rank_id'] ?>"></td>
            <td width="35%" align="center">
<?php
		if ($gradListDAO->isBBGradListReadOnly($gradListID)) {
			for($idx = 0; $idx < count($rankListID); $idx++){
				if ($row['new_rank_id'] == $rankListID[$idx]) {
                    echo '<b>' . $rankListName[$idx] . '</b>';
                    break;
                }
			}
		} else {
?>				            
                <select name="newRank_<?= $row['id'] ?>">
<?php
	        for($idx = 0; $idx <= count($rankSeq); $idx++){
	            if($rankSeq[$idx] > $row['old_rank_sequence']){
	                if($row['new_rank_id'] == $rankListID[$idx]){$isSelected = ' SELECTED';}else{$isSelected = '';}

                	if(strlen(trim($rankListName[$idx])) > 0){
?>
                    <option value="<?= $rankListID[$idx] ?>"<?= $isSelected ?>><?= $rankListName[$idx] ?></option>
<?php
					}
            	}
        	}
?>
                </select>
<?php
        }
?>                
            </td>
        </tr>
<?php
    }
?>
    </table>
</td></tr>
</table>
<table align="center" width="92%">
<?php
    if($step == 2){
?>
        <tr><td>
<?php
		if($gradListDAO->isBBGradListReadOnly($gradListID)){
?>
			<input name="gradListID" type="hidden" value="<?= $gradListID ?>"/>
			<input name="file_prefix" type="hidden" value="<?= 'BBX_'. date('Y_d_m') ?>"/>
			<br>
			<input name="getBBCSVButton" type="submit" value="Download Database" />&nbsp;
			<input name="goBackButton" type="Button" value="Go Back" onclick="history.back(-1);"/>
		</td></tr>
        <tr><td>&nbsp;</td></tr>
<?php	
		}else{
?>
			<input name="updateListButton" type="submit" value="Update Ranks">
		</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td align="right"><input name="step" type="hidden" value="<?= $step+1 ?>"/><input name="gotoNextStep" type="submit" value="Next Step >>"></td></tr>			
<?php
		}
    }else{
?>
        <tr><td>
<?php
		if(!$gradListDAO->isBBGradListReadOnly($gradListID)){
?>		  
			<input name="addMoreStudentsButton" type="submit" value="Add Students">&nbsp;&nbsp;<input name="updateListButton" type="submit" value="Update Ranks">
<?php
		}
?>        
        </td></tr>
        <tr><td>&nbsp;</td></tr>
<?php
    }
?>
</table>
<input name="gradListID" type="hidden" value="<?= $glid ?>"/>
<input name="schoolID" type="hidden" value="<?= $schoolID ?>"/>
<input name="userIDs" type="hidden" value="<?= $userIDs ?>"/>
</form>
