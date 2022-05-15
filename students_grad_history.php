<?php
	//RETRIEVAL OF GRAD HISTORY VIA
	//HISTORICAL TABLES FROM FALLS CHURCH
	$oldHistoryRS = $studentDAO->getStudentOldGradHistory($stid);
	$oldHistoryPrint = '';
	
	while($row = mysqli_fetch_array($oldHistoryRS, MYSQLI_ASSOC)){
		    $datebits = explode('-',$row['date_rank_earned']);
		    $gyear = intval($datebits[0]);
	    	$gmo = intval($datebits[1]);
		    $gday = intval($datebits[2]);
			$gradDate = $gmo . '-' . $gday . '-' . $gyear;   	  
	  	  
			$oldHistoryPrint .= '	<tr bgcolor="white" >' . "\n";
			$oldHistoryPrint .= '		<td align="center">' . $row['rank_name'] . '</td>' . "\n";
			$oldHistoryPrint .= '		<td align="center">' . $gradDate . '</td>' . "\n";
			$oldHistoryPrint .= '	</tr>' . "\n";
	}
	
	//RETRIEVAL OF GRAD HISTORY VIA
	//CURRENT GRADUATION LISTS

	$historyRS = $studentDAO->getStudentGradHistory($stid);	
	$historyPrint = '';
	
    while($row = mysqli_fetch_array($historyRS, MYSQLI_ASSOC)){
        $datebits = explode('-',$row['grad_date']);
        $gyear = intval($datebits[0]);
        $gmo = intval($datebits[1]);
        $gday = intval($datebits[2]);
        $gradDate = $gmo . '-' . $gday . '-' . $gyear;   	  
        
        $historyPrint .= '	<tr bgcolor="white" >' . "\n";
        $historyPrint .= '		<td align="center">' . $row['rank_name'] . '</td>' . "\n";
        $historyPrint .= '		<td align="center">' . $gradDate . '</td>' . "\n";
        $historyPrint .= '	</tr>' . "\n";
    }
	
	//RETRIEVAL OF BLACK BELT GRAD HISTORY VIA
	//CURRENT GRADUATION LISTS

	$bbHistoryRS = $studentDAO->getStudentBBGradHistory($stid);	
	$bbHistoryPrint = '';
	
    while($row = mysqli_fetch_array($bbHistoryRS, MYSQLI_ASSOC)){
        $datebits = explode('-',$row['grad_date']);
        $gyear = intval($datebits[0]);
        $gmo = intval($datebits[1]);
        $gday = intval($datebits[2]);
        $gradDate = $gmo . '-' . $gday . '-' . $gyear;   	  
        
        $bbHistoryPrint .= '	<tr bgcolor="white" >' . "\n";
        $bbHistoryPrint .= '		<td align="center">' . $row['rank_name'] . '</td>' . "\n";
        $bbHistoryPrint .= '		<td align="center">' . $gradDate . '</td>' . "\n";
        $bbHistoryPrint .= '	</tr>' . "\n";
    }
?>

<table width="50%" align="left" bgcolor="black">
<tr><td>
	<table width="100%" align="center" cellspacing="1">
	<tr class="black">
		<th class="goldText">Rank</th>
		<th class="goldText">Graduation Date</th>
	</tr>
<?php
    if (strlen($bbHistoryPrint) == 0 && strlen($historyPrint) == 0 && strlen($oldHistoryPrint) == 0){
?>
    <tr><td bgcolor="white"  colspan="2" align="center">No Graduation History</td></tr>
<?php
    } else {
?>
	<?= $bbHistoryPrint ?>
	<?= $historyPrint ?>
	<?= $oldHistoryPrint ?>	
<?php
    }
?>
	</table>
</td></tr>
</table>