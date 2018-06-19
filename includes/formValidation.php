<?php
	require_once($classpath . 'StudentDAO.php');
	
    /**
    * Checks for a valid date
    *
    * @param string  Date in the format "MM-DD-YYYY"
    * @param integer Disallow years more than $yearepsilon from now (in future as well as in past)
    * @return array [ month, day, year ]
    * @since 1.0
    */

    /* fixed:
    ** - leap years
    ** - december now is allowed
    ** - removed unnecessary checking
    ** if you find any more mistakes, please correct them. :)
    */

    function datecheck($date, $yearepsilon=5000) {
	    if (count($datebits=explode('-',$date))!=3) return false;
	    $month = intval($datebits[0]);
	    $day = intval($datebits[1]);
	    $year = intval($datebits[2]);
	
	    if ((abs($year-date('Y'))>$yearepsilon) || // year outside given range
	        ($month<1) || ($month>12) || ($day<1) ||
	        (($month==2) && ($day>28+(!($year%4))-(!($year%100))+(!($year%400)))) ||
	        ($day>30+(($month>7)^($month&1)))) return false; // date out of range
	
	    return true;
    }
    
    /*
     * Masks a value to the valueLen
     *      (e.g. digitMasker(3, '0', 4) ==> '0003')
     */
    function digitMasker($value, $maskValue = '0', $valueLen = 2){
    
        $currentLen = strlen($value);
        
        for($ii = $currentLen; $ii < $valueLen; $ii++){
            $value = $maskValue . $value;
        }
        
        return $value;
    }
    
    function addStudentValidate($sDAO, $firstName = '', $lastName = '', $beltSize = '', $birthdate = '', $expiredate = '', $enrolldate = '', $schoolID = -1, $familyID = -1) {
		$errMsg = '';
		
        if(strlen($firstName) <= 0){
            $errMsg = 'The student must have a first name.';
        }
        
        if(!isset($errMsg) && strlen($lastName) <= 0){
            $errMsg = 'The student must have a last name.';
        }
        
        if(!isset($errMsg) && datecheck($birthdate, intval(date('Y'))-1) == false){
            $errMsg = 'The student must have a valid date of birth';
        }

		if(!isset($expireDate) || strlen(trim($expireDate)) == 0){
			$expireDate = '01-01-1990';
		}
		
        if(!isset($errMsg) && datecheck($expireDate, intval(date('Y'))-1) == false){
            $errMsg = 'The student must have a valid expiration date ';
        }

		if(!isset($enrollDate) || strlen(trim($enrollDate)) == 0){
			$enrollDate = '01-01-1990';
		}        
        if(!isset($errMsg) && datecheck($enrollDate, intval(date('Y'))-1) == false){
            $errMsg = 'The student must have a valid start/enrollment date';
        }        
        
        if($familyID > 0){
			$familyRS = $sDAO->getFamily($familyID);
		
			$row = mysql_fetch_array($familyRS);
			if ($row['school_id'] != $schoolID){
			  	$errMsg = 'Since the school has changed, please reset the family group value.';
			}	  
		}
		
        return $errMsg;
    }
    
    function addSchoolValidate($code = '', $name = '', $addy1 = '', $city = '', $state = '', $postal = '', $country = '', $poc = ''){
	  	
	  	if(strlen($code) <= 0){
		    $errMsg = 'The academy location code is missing.';
		    return $errMsg;
		}
		
		if(strlen($name) <= 0){
		    $errMsg = 'The academy name is missing.';
		    return $errMsg;
		}
		
		if(strlen($addy1) <= 0){
		    $errMsg = 'The academy address is missing.';
		    return $errMsg;
		}
		
		if(strlen($city) <= 0){
		    $errMsg = 'The academy city is missing.';
		    return $errMsg;
		}
		
		if(strlen($state) <= 0){
		    $errMsg = 'The academy state is missing.';
		    return $errMsg;
		}
		
		if(strlen($postal) <= 0){
		    $errMsg = 'The academy zip/postal code is missing.';
		    return $errMsg;
		}
		
		if(strlen($country) <= 0){
		    $errMsg = 'The academy country is missing.';
		    return $errMsg;
		}
		
		if(strlen($poc) <= 0){
		    $errMsg = 'The academy POC (Point of Contact email) is missing.';
		    return $errMsg;
		}
	}
?>
