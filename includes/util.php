<?php
    function convertToCurrency($value){
        $remainder = strstr($value,'.');
        
        if(strlen($remainder) == 0){
            return $value . '.00';
        }elseif(strlen($remainder) == 2){
            return $value . '0';
        }else{
            return $value;
        }
    }
    
	function createRandomPassword($length = 7) {
	    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= $length) {
		  	$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
	        $pass = $pass . $tmp;
	        $i++;
		}
		
		return $pass;
	}    
	
	function generateLinkButton($url, $displayName = 'Go!', $method = 'GET'){
	  	$button = "<form action=\"$url\" method=\"$method\"> ";
	  	$button .= "<input type=\"submit\" value=\"$diplayName\"> ";
	  	$button .= "</form>";
	  	
	  	return $button;	  	
	}
	
	function showRankMenu($isAdmin, $varName, $rankID, $rankListRS) {
	  	$retVal = '';
		
		if($isAdmin){
            $retVal = '<select name="' . $varName . '">' . "\n";

	        if($rankListRS != false){
    	    	while($row = mysqli_fetch_array($rankListRS, MYSQLI_ASSOC)){
					$isSelected = '';
					if($rankID == $row['id']){$isSelected = ' SELECTED';}
					
					$retVal .=  '	<option value="' . $row['id'] . '"' . $isSelected . '>' . $row['rank_name'] . "</option>\n";
              	}
   	        }
            $retVal .= '</select>';

		}else{
	        if($rankListRS != false) {
    	    	while($row = mysqli_fetch_array($rankListRS, MYSQLI_ASSOC)) {
					if($rankID == $row['id']) {
						$retVal = '<input name="' . $varName . '" type="hidden" value="' . $rankID . '"/><b>' . $row['rank_name'] . '</b>';
						return $retVal;
					}
				}
			} else {
				echo 'RankListRS = false';
			}
		}
		
		return $retVal;	  	
	}
	
	function showProgramMenu($varName, $programID, $programListRS){	  
	  	$retVal = '';
		
	  	if(!isset($programID)){ $programID = 1; }
	  	
        $retVal = '<select name="' . $varName . '">' . "\n";

        if($programListRS != false){
	    	while($row = mysqli_fetch_array($programListRS, MYSQLI_ASSOC)){
				$isSelected = '';
				if($programID == $row['id']){$isSelected = ' SELECTED';}
				
				$retVal .=  '	<option value="' . $row['id'] . '"' . $isSelected . '>' . $row['name'] . "</option>\n";
          	}
        }
        $retVal .= '</select>';
		
		return $retVal;	  	
	}
	
	// Gradlist function used to determine if the shown current rank of 
	// a student matches his/her real current rank.
	// (NOTE:  This can occur if multiple unfinalized gradlists have
	//         been setup)
	//	Returns TRUE if listedRankID is correct
	//          FALSE if not
	function validateCurrentRank($dao, $studentID, $listedRankID)
	{
		$resultSet = $dao->getStudent($studentID);        
		$row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC);
		
		return $listedRankID == $row['rank_id'];
	}		
?>