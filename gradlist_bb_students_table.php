<?php
    //This file is too be included by gradlist_bb_students_search.php
?>

<table align="center" width="85%" bgcolor="FF0000">
<tr><td>
    <table align="center" cellspacing="1" width="100%">
    <tr bgcolor="C0C0C0">
        <th width="5%"><input name="checkAll" type="checkbox" onclick="select_all('studentIDs[]',this.checked)"></th>
        <th width="50%">Name</th>
        <th width="30%">Current Rank</th>
        <th width="15%">New Rank</th>
    </tr>
<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchButton'])){
        if(mysqli_num_rows($studentListRS) > 0){
            while($row = mysqli_fetch_assoc($studentListRS)){
?>
        <tr bgcolor="white">
            <td width="5%" align="center"><input name="studentIDs[]" type="checkbox" value="<?= $row['id'] ?>"></td>
            <td width="35%" align="left"><?= $row['first_name'] ?> <?= $row['middle_init'] ?> <?= $row['last_name'] ?></td>
            <td width="30%" align="left"><?= $row['rank_name'] ?><input name="oldRank_<?= $row['id'] ?>" type="hidden" value="<?= $row['rank_id'] ?>"/></td>
            <td width="30%" align="center">
                <select name="newRank_<?= $row['id'] ?>">
<?php
                    for($idx=0; $idx < sizeof($rankSeq); $idx++){
                        if($rankSeq[$idx] > $row['sequence'] && strlen(trim($rankListName[$idx])) > 0){
?>
                    <option value="<?= $rankListID[$idx] ?>"><?= $rankListName[$idx] ?></option>
<?php
                        }
                    }
?>
                </select>
            </td>
        </tr>
<?php
            }
        }else{
?>
            <tr bgcolor="white"><td align="center" colspan="7">Zero students were found...</td></tr>
<?php
        }
    }
?>
    </table>
</td></tr>
</table>
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searchButton'])){
?>
    <table align="center" width="87%">
    <tr><td>
        <input name="addToListButton" type="submit" value="Add To List">
    </td></tr>
    </table>
<?php
    }
?>

<script type="text/javascript">
    function select_all(name,value){
        var formblock;
        formblock = document.getElementById('gradListWizard');
        for(var t, i=0;t=formblock.elements[name][i++];t.checked=value);
    }
</script>

