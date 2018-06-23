<script type="text/javascript">
    function select_all(name,value){
        var formblock;
        formblock = document.getElementById('gradListEdit');
        for(var t, i=0;t=formblock.elements[name][i++];t.checked=value);
    }
</script>

<?php
	require_once('includes/util.php');
	
    if (isset($_REQUEST['scid'])){
        $schoolID = $_REQUEST['scid'];
    }
    
    if (isset($_REQUEST['glid'])){
        $gradListID = $_REQUEST['glid'];
    }
    
    $gradListInfo = $gradListDAO->getGradListName($gradListID);
    
    if(is_array($gradListInfo)){
        $gradListTitle = $gradListInfo[0] . '<br>' . $gradListInfo[1];
        $gradListFileName = $gradListInfo[2];
    }else{
        $gradListTitle = $gradListInfo;
    }
    
    $gradListTitle .= '<br><br>There is currently a total of <span style="color: white;text-decoration: bold;font-size: 10pt;">' . mysqli_num_rows($studentListRS) . '</span> student(s) ';
    
     
?>
<form name="gradListEdit" action="index.php?action=<?= $action ?>" method="POST">
<table align="center" width="90%" bgcolor="000000">
<tr><td class="goldText" align="center"><?= $gradListTitle ?></td></tr>
<tr><td>
    <table align="center" cellspacing="1" width="100%">
    <tr bgcolor="C0C0C0">
        <th width="5%"><input name="checkAll" type="checkbox" onclick="select_all('gsid[]',this.checked)"></th>
        <th width="40%">Name</th>
        <th width="30%">Current Rank</th>
        <th width="25%">New Rank</th>
    </tr>
<?php
	$outOfDateRanks = false;
    while($row = mysqli_fetch_array($studentListRS, MYSQLI_ASSOC)){
        if(!isset($userIDs)){
            $userIDs = $row['id'];
        }else{
            $userIDs .= ',' . $row['id'];
        }
        
        //validating a student's current rank
        $isValidRank = validateCurrentRank($studentDAO, $row['id'], $row['old_rank_id']);
        if($isValidRank){
        	$oldRankDisplay = $row['old_rank_name'];
        }else{
        	$oldRankDisplay = '<span class="error"><i>' . $row['old_rank_name'] . '</i></span>';
        	$outOfDateRanks = true;
        }
?>
        <tr bgcolor="white">
            <td width="5%" align="center">
<?php
		if($gradListDAO->isGradListPaid($gradListID)){
?>
				<a href="index.php?action=gl.edit&act2=ud&scid=<?= $scid ?>&glid=<?= $glid ?>&gsid=<?= $row['gs_id'] ?>" onclick="return confirm('Are you sure you want to undo [<?= str_replace("'", "\'",$row['first_name']) . ' ' . str_replace("'", "\'", $row['last_name']) ?>] from this list?');">
					<img src="<?= $imgRoot ?>button_undo.png" border="0" alt="Undo Graduation">
				</a>
<?php
		}else{
?>
				<input name="gsid[]" type="checkbox" value="<?= $row['gs_id'] ?>">
				<!-- <a href="index.php?action=gl.edit&act2=rm&scid=<?= $scid ?>&glid=<?= $glid ?>&gsid=<?= $row['gs_id'] ?>" onclick="return confirm('Are you sure you want to remove [<?= str_replace("'", "\'",$row['first_name']) . ' ' . str_replace("'", "\'", $row['last_name']) ?>] from this list?');"><img src="<?= $imgRoot ?>button_delete.png" border="0" alt="Remove Student"></a> -->
<?php
		}
?>				
			</td>
            <td width="40%" align="left"><?= $row['first_name'] ?> <?= $row['middle_init'] ?> <?= $row['last_name'] ?></td>
            <td width="30%" align="center"><?= $oldRankDisplay ?><input name="oldRank_<?= $row['id'] ?>" type="hidden" value="<?= $row['old_rank_id'] ?>"></td>
            <td width="35%" align="center">
<?php
		if($gradListDAO->isGradListPaid($gradListID)){
			for($idx = 0; $idx < count($rankListID); $idx++) {
				if($row['new_rank_id'] == $rankListID[$idx]) {echo '<b>' . $rankListName[$idx] . '</b>';}			  
			}
		}else{
?>				            
                <select name="newRank_<?= $row['id'] ?>">
<?php
			$showOption = false;
	        for($idx = 0; $idx < count($rankListID); $idx++){
                if($row['new_rank_id'] == $rankListID[$idx]){$isSelected = ' SELECTED';}else{$isSelected = '';}

               	if($showOption){
?>
                    <option value="<?= $rankListID[$idx] ?>"<?= $isSelected ?>><?= $rankListName[$idx] ?></option>
<?php
				}
				
				if(strcasecmp($rankListName[$idx], $row['old_rank_name']) == 0){
					$showOption = true;
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
    if($action == 'gl.merge'){
		echo '	<tr><td>';
		if($gradListDAO->isGradListPaid($gradListID)){
			echo '		<input name="goBackButton" type="Button" value="Go Back" onclick="history.back(-1);"/>';
		}else{
			echo '		<input name="updateListButton" type="submit" value="Update New Ranks">';
			
			if($outOfDateRanks == true){
				echo '		<input name="updateCurrentRanks" type="submit" value="Update Current Ranks">';
			}
		}        
		echo '	</td></tr>';

		if($outOfDateRanks == false){
?>		
        <tr><td>&nbsp;</td></tr>
        <tr><td align="right"><input name="step" type="hidden" value="<?= $step+1 ?>"/><input name="gotoNextStep" type="submit" value="Next Step >>"></td></tr>
<?php			
		}
    }else{
?>
        <tr><td>
<?php
		if($gradListDAO->isGradListPaid($gradListID)){
		  	$gsInfo = $studentDAO->getSchoolByGradList($gradListID);
		  	$accessLevel = $studentDAO->getSchoolAccess($_COOKIE["uid"], $gsInfo['id']);
?>
			<input name="gradListID" type="hidden" value="<?= $gradListID ?>"/>
			<input name="file_prefix" type="hidden" value="<?= $gradListFileName ?>"/>
<?php		
			if($accessLevel == 2){
?>
			<select name="csv_type">
				<option value="0">Download All Ranks</option>
				<option value="1">Download Solid Ranks Only</option>
				<option value="2">Download Adv. Ranks Only</option>
			</select>
			<input name="getCSVButton" type="submit" value="Download" />&nbsp;
<?php
			}
?>
			<input name="goBackButton" type="Button" value="Go Back" onclick="parent.location='index.php?action=gl.edit'"/></a>
<?php	
		}else{
?>		  
			<br><input name="removeStudentsButton" type="submit" value="Remove Selected Students">&nbsp;&nbsp;<input name="addMoreStudentsButton" type="submit" value="Add Students">&nbsp;&nbsp;<input name="updateListButton" type="submit" value="Update Ranks">
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
