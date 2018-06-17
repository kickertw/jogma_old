<?php
	/***********************************************
	 * 	  File: UserDAO.php
	 * 	  Desc: class UserDAO is needed to interface
	 *			with tbl_Users
	 *    Date: 02/01/05
	 *  Author: T. Wong
	 ***********************************************/

	class UserDAO{
	    var $link;
	    var $query;
	    
	    function UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn){
	        $link = mysqli_connect($DB_server, $DB_user, $DB_pass, $DB_conn) or DIE("unable to connect to $DB_server");
	        // mysqli_select_db($userConn, $DB_conn);
	    }

	    //Login a user and return his/her ID
	    function loginUser($username, $pass){
	        $query  = "SELECT * FROM tbl_users ";
			$query .= "WHERE username = '%s' AND password = '%s' AND active > 0";
			$query = sprintf($query,
							 /*mysql_real_escape_string*/($username),
							 /*mysql_real_escape_string*/($pass));
	    
			$resultSet = mysqli_query($link, $query) or die(mysqli_error() . " $query");
			
			if(mysqli_num_rows($resultSet) == 1){
			    $row = mysqli_fetch_array($resultSet);
			    return $row['id'];
			}else{
			    return -1;
			}
	    }
	    
	    //Check to see if one is a super user
	    function isSuperAdmin($userID){
	        $query  = "SELECT admin_level FROM tbl_users ";
			$query .= "WHERE id = %d";
			$query = sprintf($query,
							 /*mysql_real_escape_string*/($userID));

			$resultSet = mysqli_query($link, $query) or die(mysqli_error() . " $query");

			if(mysqli_num_rows($resultSet) == 1){
			    $row = mysqli_fetch_array($resultSet);
			    if($row['admin_level'] == 2){return 1;}
                else{return 0;}
			}else{
			    return -1;
			}
	    }

	    //Logout a user
	    function logoutUser($userID){
	        $query  = "UPDATE tbl_users ";
	        $query .= "SET sessionID = '-1' ";
			$query .= "WHERE id = %d";
			$query = sprintf($query,
							 /*mysql_real_escape_string*/($userID));

			mysqli_query($link, $query);

			if(mysqli_affected_rows() == 1){
			    return true;
			}else{
			    return false;
			}
	    }

		//Set user session
		function setSessionID($userID, $sessionID){
	        $query  = "UPDATE tbl_users ";
	        $query .= "SET sessionID = '%s' ";
			$query .= "WHERE id = %d";
			$query = sprintf($query,
                             /*mysql_real_escape_string*/($sessionID),
							 /*mysql_real_escape_string*/($userID));

			mysqli_query($link, $query);

			if(mysqli_affected_rows() == 1){
			    return true;
			}else{
			    return false;
			}
		}

		//Check user session
		function validateSession($userID, $sessionID){
	        $query  = "SELECT * FROM tbl_users ";
			$query .= "WHERE id = %d AND sessionID = '%s'";
			$query = sprintf($query,
							 /*mysql_real_escape_string*/($userID),
							 /*mysql_real_escape_string*/($sessionID));

			$resultSet = mysqli_query($link, $query);
			if(mysqli_num_rows($resultSet) == 1){
			    return true;
			}else{
			    return false;
			}
   		}
   		
   		//Gets user info
   		function getUserInfo($userID){
   		    $query  = "SELECT * FROM tbl_users ";
   		    $query .= "WHERE id = %d";
   		    $query = sprintf($query,
							 /*mysql_real_escape_string*/($userID));
							 
			$resultSet = mysqli_query($link, $query);

			return mysqli_fetch_array($resultSet);
   		}
   		
   		//Gets a list of users
   		function getUserList($excludeID){
   		    $query  = "SELECT * FROM tbl_users ";
   		    $query .= "WHERE id <> %d";
   		    $query = sprintf($query,
							 /*mysql_real_escape_string*/($excludeID));

			$resultSet = mysqli_query($link, $query);

			return $resultSet;
   		}
   		
   		//Insert a new user
   		function addUser($username, $fullname, $email, $ph1, $ph2, $pwd, $status, $access, $showAdvRanks){
            $query  = 'INSERT INTO tbl_users ';
            $query .= '     (username, fullname, email, password, active, access_level, show_advanced_ranks) ';
            $query .= 'VALUES ';
            $query .= "     ('%s','%s','%s','%s',%d,%d,%d)";
            
   		    $query  = sprintf($query,
                              /*mysql_real_escape_string*/($username),
                              /*mysql_real_escape_string*/($fullname),
                              /*mysql_real_escape_string*/($email),
							  /*mysql_real_escape_string*/($pwd),
							  /*mysql_real_escape_string*/($status),
							  /*mysql_real_escape_string*/($access),
							  /*mysql_real_escape_string*/($showAdvRanks));

			mysqli_query($link, $query);
			
			//Now we need to retrieve the ID of the user just inserted
			$resultSet = mysqli_query($link, 'SELECT @@IDENTITY as userID') or die('Unable to return USER_ID after insert');
			$row = mysqli_fetch_array($resultSet);
			return $row['userID'];			
   		}
   		
   		function getSearchVar($userID, $name){
            $query  = 'SELECT var_value FROM tbl_last_search ';
            $query .= "WHERE user_id = %d AND var_name = '%s' ";
            
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($userID),
                             /*mysql_real_escape_string*/($name));

			$resultSet = mysqli_query($link, $query);
			
            if(mysqli_num_rows($resultSet) == 1){
                $row = mysqli_fetch_array($resultSet);
                return $row['var_value'];
            }else{
                return '';
            }
        }
   		
   		//Save a users last search var
   		function saveSearchVar($userID, $name, $value){
            $query  = 'SELECT * FROM tbl_last_search ';
            $query .= "WHERE user_id = %d AND var_name = '%s' ";
            
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($userID),
                             /*mysql_real_escape_string*/($name));

			$resultSet = mysqli_query($link, $query) or die(mysqli_error() . " $query");
			
			if(mysqli_num_rows($resultSet) > 0){
                $query2  = 'UPDATE tbl_last_search ';
                $query2 .= "SET var_value = '%s' ";
                $query2 .= "WHERE user_id = %d AND var_name = '%s' ";
                
                $query2 = sprintf($query2,
                                  /*mysql_real_escape_string*/($value),
                                  /*mysql_real_escape_string*/($userID),
                                  /*mysql_real_escape_string*/($name));
			}else{
                $query2  = 'INSERT INTO tbl_last_search ';
                $query2 .= '     (user_id, var_name, var_value) ';
                $query2 .= 'VALUES ';
                $query2 .= "     (%d,'%s','%s')";
                
                $query2 = sprintf($query2,
                                  /*mysql_real_escape_string*/($userID),
                                  /*mysql_real_escape_string*/($name),
                                  /*mysql_real_escape_string*/($value));
            }
            
            //save the name-value pair
            mysqli_query($link, $query2) or die(mysqli_error() . " $query2");
   		}
   		
   		//Update user info
   		function updateUserInfo($userID, $username, $fullname, $email, $ph1, $ph2, $pwd, $status = 1, $access = 0, $showAdvRanks = 0){
 		  
            $query  = "UPDATE tbl_users SET ";
            $query .= "     username = '%s', fullname = '%s', email = '%s', active = %d, access_level = %d, show_advanced_ranks = %d ";
            
            if(strlen($pwd) > 0){
                $query .= ", password = '%s' ";
            }
            
            $query .= "WHERE id = %d";
            
            if(strlen($pwd) > 0){
	   		    $query  = sprintf($query,
	                              /*mysql_real_escape_string*/($username),
	                              /*mysql_real_escape_string*/($fullname),
	                              /*mysql_real_escape_string*/($email),
	                              /*mysql_real_escape_string*/($status),
	                              /*mysql_real_escape_string*/($access),
	                              /*mysql_real_escape_string*/($showAdvRanks),
	                              /*mysql_real_escape_string*/($pwd),
								  /*mysql_real_escape_string*/($userID));
			}else{
	   		    $query  = sprintf($query,
	                              /*mysql_real_escape_string*/($username),
	                              /*mysql_real_escape_string*/($fullname),
	                              /*mysql_real_escape_string*/($email),
	                              /*mysql_real_escape_string*/($status),
	                              /*mysql_real_escape_string*/($access),
	                              /*mysql_real_escape_string*/($showAdvRanks),
								  /*mysql_real_escape_string*/($userID));
			}
			
			mysqli_query($link, $query);

			if(mysqli_affected_rows() == 1){
			    return true;
			}else{
			    return false;
			}
   		}
   		
   		//Update User/School Access Rights
   		//     0 = Denied
   		//     1 = Read Only
   		//     2 = Read/Write
   		function updateSchoolAccess($userID, $schoolID, $level){
   		
            $query  = 'DELETE FROM tbl_user_school_access ';
            $query .= 'WHERE school_id = %d AND user_id = %d ';
            
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($schoolID),
                             /*mysql_real_escape_string*/($userID));
                             
            $result = mysqli_query($link, $query) or die(mysqli_error() . "<br><br>Orig. Query = $query");
                              
            if($level > 0){
                $query  = 'INSERT INTO tbl_user_school_access ';
                $query .= '     (user_id, school_id, access_level) ';
                $query .= 'VALUES ';
                $query .= '     (%d, %d, %d) ';
                
                $query = sprintf($query,
	                             /*mysql_real_escape_string*/($userID),
	                             /*mysql_real_escape_string*/($schoolID),
	                             /*mysql_real_escape_string*/($level));
	                             
                mysqli_query($link, $query) or die(mysqli_error() . "<br><br>Orig. Query = $query");
            }
   		}
   		
	}
?>
