<?php
	include($classpath . 'StudentDAO.php');
	include($classpath . 'GradListDAO.php');

	$gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
	$studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], 0);
	
	$domainName = $_SERVER['HTTP_HOST'];
	$referer = $_SERVER['HTTP_REFERER'];
	$outputText = 'last_name,first_name,new_rank,new_rank_sort,date1,date2,date3,school' . "\n";

	$gradListID = $_POST['gradListID'];
	$file_prefix = $_POST['file_prefix'];
	$csv_type = $_POST['csv_type'];

	if (isset($gradListID) && isset($file_prefix) && strpos($referer, $domainName) !== false){	  
	  	//retrieval of ranks
	  	$rankRS = $studentDAO->getRankList();	  	
	  	while($rankRow = mysqli_fetch_assoc($rankRS)){
			$rankList[intval($rankRow['sequence'])] = $rankRow['print_name'];    
		}	  	
	  	
	  	//retrieval of students per grad list
		$studentRS = $gradListDAO->getGradStudents($gradListID, 0, 1);  
				
		while($row = mysqli_fetch_assoc($studentRS)){
			$oldSeq = intval($row['old_sequence']);
			$newSeq = intval($row['new_sequence']);
			
			for($ii = $oldSeq; $ii < $newSeq; $ii++){
			  	$graduationDate = date('l, F j, Y',strtotime($row['grad_date']));
				$showRow = false;
				if ($csv_type == 1 && strpos($rankList[$ii+1],'Adv') === false){
					$showRow = true;
				}elseif ($csv_type == 2 && strpos($rankList[$ii+1], 'Adv') !== false){
					$showRow = true;
				}elseif ($csv_type == 0){
					$showRow = true;
				}

				if ($showRow == true){
					$outputText .= $row['last_name'] . ',' . $row['first_name'] . ',' . $rankList[$ii+1] . ',' . $ii . ',' . $graduationDate . ',' . $row['school_name'] . "\n";
				}
			}
		}
	}
  
	header("Content-Type: application/CSV");
	header("Content-Disposition: attachment; filename=" . $file_prefix . ".csv");
	echo $outputText;
?>