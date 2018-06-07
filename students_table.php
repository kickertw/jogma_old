<?php
    //This file is too be included by students.php
?>
<b><?= mysql_num_rows($studentListRS) ?> record(s)</b> have been found<br><br>
<table align="center" width="95%" bgcolor="FF0000">
<tr><td>
    <table align="center" cellspacing="1" width="100%">
    <tr bgcolor="C0C0C0">
        <th width="5%"><input name="checkAll" type="checkbox" onclick="select_all('studentIDs[]',this.checked)"></th>
        <th width="10%">ID</th>
        <th width="30%">Name</th>
        <th width="19%">Current Rank</th>
        <th width="14%">Belt Size</th>
        <th width="10%">Status</th>
    </tr>
<?php
    if(mysql_num_rows($studentListRS) > 0){
        while($row = mysql_fetch_array($studentListRS)){
            if($row['active'] == 1){
                $activeStatus = "Active";
            }else{
                $activeStatus = "Inactive";
            }
?>
    <tr bgcolor="white">
<!-- <td width="5%" align="center"><a href="index.php?action=stu.edit&stid=<?= $row['id'] ?>"><img src="<?= $imgRoot ?>button_edit.png" border="0"></a></td> -->
		<td width="5%" align="center"><input name="studentIDs[]" type="checkbox" value="<?= $row['id'] ?>"/></td>
        <td width="10%" align="center"><a href="index.php?action=stu.edit&stid=<?= $row['id'] ?>"><?= $row['id'] ?></a></td>
        <td width="30%" align="left"><?= $row['first_name'] ?> <?= $row['middle_init'] ?> <?= $row['last_name'] ?></td>
        <td width="19%" align="left"><?= $row['rank_name'] ?></td>
        <td width="14%" align="left"><?= $row['belt_size']?></td>
<!-- <td width="12%" align="left"><?= $row['location_code']?></td> -->
        <td width="10%" align="left"><?= $activeStatus ?></td>
    </tr>
<?php
        }
?>
	<tr bgcolor="white" valign="top">
		<td colspan="6">
			<img src="<?= $imgRoot ?>arrow_ltr.png" border="0">
<?php
		if($isActive != 1){
			echo '			<input name="setToActive" type="submit" value="Set Active"/>' . "\n\r";
		}
		if($isActive != 0){
			echo '			<input name="setToInactive" type="submit" value="Set Inactive"/>' . "\n\r";
		}
?>
		</td>
	</tr>
<?php
    }else{
?>
        <tr bgcolor="white"><td align="center" colspan="6">Zero students have been found...</td></tr>
<?php
    }
?>
    </table>
</td></tr>
</table>

<script type="text/javascript">
    function select_all(name,value){
        var formblock;
        formblock = document.getElementById('gradListWizard');
        for(var t, i=0;t=formblock.elements[name][i++];t.checked=value);
    }
</script>