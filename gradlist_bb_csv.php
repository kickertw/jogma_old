<?php
	include($classpath . 'StudentDAO.php');
	include($classpath . 'GradListDAO.php');

	$gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
	$studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], 0);
	
	$domainName = $_SERVER['HTTP_HOST'];
	$referer = $_SERVER['HTTP_REFERER'];
	$outputText = 'last_name,first_name,new_rank,new_rank_sort,date1,date2,date3,school,city,state,country' . "\n";

	if (isset($_POST['gradListID']) && isset($_POST['file_prefix']) && strpos($referer, $domainName) !== false){	  
	  	//retrieval of ranks
	  	$rankRS = $studentDAO->getBBCandidateRankList();	  	
	  	while($rankRow = mysqli_fetch_array($rankRS, MYSQLI_ASSOC)){
			$rankList[intval($rankRow['sequence'])] = $rankRow['print_name'];    
		}	  	
	  	
	  	//retrieval of students per grad list
		$studentRS = $gradListDAO->getBBGradStudents($_POST['gradListID'], 0, 1);  
				
		while($row = mysqlI_fetch_array($studentRS, MYSQLI_ASSOC)){
			$oldSeq = intval($row['old_sequence']);
			$newSeq = intval($row['new_sequence']);
			
			for($ii = $oldSeq; $ii < $newSeq; $ii++){
			  	$graduationDate = date('l, F j, Y',strtotime($row['grad_date']));			
				$outputText .= $row['last_name'] . ',' . $row['first_name'] . ',' . $rankList[$ii+1] . ',' . $ii . ',' . $graduationDate . ',' . $row['school_name'] . ',' . $row['city'] . ',' . $row['state'] . ',' . $row['country'] . "\n";
			}
		}
	}
  
	header("Content-Type: application/CSV");
	header("Content-Disposition: attachment; filename=" . $_POST['file_prefix'] . ".csv");
	echo $outputText;
?>