<?php
	/***********************************************
	 * 	  File: UserValidater.php
	 * 	  Desc: Class UserValidater manages User's 
	 *          and their sessions
	 *    Date: 02/01/05
	 *  Author: T. Wong
	 ***********************************************/

	//Requires User DAO class to interface with
	//the user table in the DB
	include('classes/UserDAO.php');
	
	class UserValidater{
	    var $userDB;
		var $result;
		
	    function UserValidater($server, $user, $pass, $db){
	        $this->userDB = new UserDAO($server, $user, $pass, $db);
	    }
	    
	    //Login a user
	    function login($username, $pass){
			$this->result = $this->userDB->loginUser($username, $pass);
			return $this->result;
	    }
	    
	    //Logout a user
	    function logout($userID){
			$this->result = $this->userDB->logoutUser($userID);
			return $this->result;
		}

		//Check user session
		function validateSession($userID, $sessionID){
		    $this->result = $this->userDB->validateSession($userID, $sessionID);
		    return $this->result;
		}
		
		//initailize a user session
		function initSession($userID, $sessionID){
		    $this->result = $this->userDB->setSessionID($userID, $sessionID);
		    return $this->result;
		}
	}
?>
