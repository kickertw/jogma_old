<?php
	/***********************************************
	 * 	  File: AuditDAO.php
	 * 	  Desc: class AuditDAO is needed to log
	 *			transaction history
	 *    Date: 07/03/08
	 *  Author: T. Wong
	 ***********************************************/

	class AuditDAO{
	    public $link;

	    function AuditDAO($DB_server, $DB_user, $DB_pass, $DB_conn){
			$this->link = mysqli_connect($DB_server, $DB_user, $DB_pass, $DB_conn) or DIE("unable to connect to $DB_server");
	    }
   		
   		//Insert a audit entry
   		function addLog($userID,$auditText){
            $query  = 'INSERT INTO tbl_audit ';
            $query .= '     (user_id, action_desc) ';
            $query .= 'VALUES ';
            $query .= "     ('%d','%s')";
            
   		    $query  = sprintf($query,
			   				  mysqli_real_escape_string($this->link, $userID),
							  mysqli_real_escape_string($this->link, $auditText));

			mysqli_query($this->link, $query);
   		}
   		
   		function getLog($logID = 0, $userID = 0, $startRow = 0, $limit = 0){
            $query  = 'SELECT * FROM tbl_audit WHERE ';
            
            if($logID > 0){
            	$query .= " id = %d ";
            	$queryID = $logID;
            }
            
            if($userID > 0){
            	$query .= " user_id = %d ";
            	$queryID = $userID;            
            }
            
            $query .= ' LIMIT %d, %d';
            
            $query = sprintf($query,
							 mysqli_real_escape_string($this->link, $queryID),
                             mysqli_real_escape_string($this->link, $startRow),
                             mysqli_real_escape_string($this->link, $limit));

			return mysqli_query($this->link, $query);	
        }
   	}
?>
