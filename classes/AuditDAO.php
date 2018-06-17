<?php
	/***********************************************
	 * 	  File: AuditDAO.php
	 * 	  Desc: class AuditDAO is needed to log
	 *			transaction history
	 *    Date: 07/03/08
	 *  Author: T. Wong
	 ***********************************************/

	class AuditDAO{
	    var $userConn;
	    var $query;
	    

	    function AuditDAO($DB_server, $DB_user, $DB_pass, $DB_conn){
	        $userConn = mysqli_connect($DB_server, $DB_user, $DB_pass) or DIE("unable to connect to $DB_server");
	        mysqli_select_db($userConn, $DB_conn);
	    }
   		
   		//Insert a audit entry
   		function addLog($userID,$auditText){
            $query  = 'INSERT INTO tbl_audit ';
            $query .= '     (user_id, action_desc) ';
            $query .= 'VALUES ';
            $query .= "     ('%d','%s')";
            
   		    $query  = sprintf($query,
                              mysql_real_escape_string($userID),
                              mysql_real_escape_string($auditText));

			mysql_query($query);
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
                             mysql_real_escape_string($queryID),
                             mysql_real_escape_string($startRow),
                             mysql_real_escape_string($limit));

			return mysql_query($query);	
        }
   	}
?>
