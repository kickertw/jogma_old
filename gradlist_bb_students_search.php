<?php
    $rankListRS = $studentDAO->getBBCandidateRankList();
?>
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
            <td align="left" width="85%" colspan="2">
                <select name="schoolID">
                	<option value="0" >All Schools</option>
        <?php
                if($schoolListRS != false){
                    while($row = mysql_fetch_array($schoolListRS)){
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
                $ii = 0;
                while($row = mysql_fetch_array($rankListRS)){
                  	$rankSeq[$ii] = intval($row['sequence']);
                    $rankListName[$ii] = $row['rank_name'];
                    $rankListID[$ii] = intval($row['id']);
                    
                    if(strlen(trim($row['rank_name'])) > 0){
    ?>
                <option value="<?= $row['id'] ?>" <?php if($rankID == $row['id']){echo 'SELECTED';} ?>><?= $row['rank_name'] ?></option>
    <?php
    				}
    				$ii++;
                }
            }
    ?>
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
                <option value="rank_id" <?php if($orderBy == 'rank_id'){echo 'SELECTED';} ?>>Rank</option>
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
                <option value="rank_id" <?php if($orderBy2 == 'rank_id'){echo 'SELECTED';} ?>>Rank</option>
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
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchButton'])){
                    include('gradlist_bb_students_table.php');
                }else{
                    echo "Hit 'Go' to view all your students at once!";
                }
            ?>
        </td>
    </tr>