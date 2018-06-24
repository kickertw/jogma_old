<?php
        /***********************************************
         *           File: GradListDAO.php
         *           Desc: class GradListDAO is needed to interface
         *                        with tbl_gradlist and tbl_gradlist_students
         *    Date: 10/25/05
         *  Author: T. Wong
         ***********************************************/

        class GradListDAO {
            public $link;
            public $DB_server;
            public $DB_user;
            public $DB_pass;
            public $DB_conn;

            function GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn) {
                $this->DB_server = $DB_server;
                $this->DB_user = $DB_user;
                $this->DB_pass = $DB_pass;
                $this->DB_conn = $DB_conn;

                $this->link = mysqli_connect($DB_server, $DB_user, $DB_pass, $DB_conn) or DIE("unable to connect to $DB_server");
            }

            function getGradListByUser($creatorID, $getCountOnly = 0){
                $query  = 'SELECT g.*, s.name as school_name FROM tbl_gradlist g, tbl_schools s ';
                $query .= 'WHERE g.created_by = %d ';
                $query .= '  AND g.school_id = s.id ';
                $query .= 'ORDER BY g.school_id, g.grad_date DESC';

                $query  = sprintf($query,
                                  /*mysql_real_escape_string*/($creatorID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if ($getCountOnly == 0){
                    return $resultSet;
                }else{
                    return mysqli_num_rows($resultSet);
                }
            }

            function getGradLists($userID, $readOnly = -1){
	            $query  = 'SELECT g.*, s.name as school_name FROM tbl_gradlist g, tbl_schools s, tbl_user_school_access usa ';
	            $query .= 'WHERE usa.user_id = %d ';
	            $query .= '  AND g.school_id = usa.school_id ';
	            $query .= '  AND s.id = g.school_id ';
	
	            if($readOnly == 0 || $readOnly == 1){$query .= "  AND g.read_only = $readOnly ";}
	
	            $query .= 'ORDER BY g.school_id, g.grad_date DESC';	
                $query  = sprintf($query,
                                  /*mysql_real_escape_string*/($userID));
	
	            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
                return $resultSet;
            }

            function getGradListName($gradListID){
                $query  = 'SELECT g.*, s.name as school_name, s.location_code FROM tbl_gradlist g, tbl_schools s ';
                $query .= 'WHERE g.id = %d AND s.id = g.school_id';
                $query  = sprintf($query,
                                  /*mysql_real_escape_string*/($gradListID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if (mysqli_num_rows($resultSet) == 1){
                    $row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC);

                    $gradListTitle[0] = $row['school_name'];
                    $gradListTitle[1] = date("F",strtotime($row['grad_date'])) . ' ' . date("j",strtotime($row['grad_date'])) . ', ' . date("Y",strtotime($row['grad_date']));
                    $gradListTitle[2] = $row['location_code'] . '_' . date("M",strtotime($row['grad_date'])) . date("j",strtotime($row['grad_date'])) . date("Y",strtotime($row['grad_date']));

                    return $gradListTitle;
                }

                return 'Unknown GradList...';
            }

            function getAllBBGradLists() {
                $query  = 'SELECT * FROM tbl_bb_gradlist ';
                $query .= 'ORDER BY grad_date DESC';
                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                return $resultSet;
            }

            function getBBGradList($gradDate, $creatorID = 0, $getCountOnly = 0){
                $query  = 'SELECT * FROM tbl_bb_gradlist ';
                $query .= "WHERE grad_date = '%s' ";

                if($creatorID == 0){
                    $query .= ' AND created_by = %d ';
                    $query  = sprintf($query,
                                      /*mysql_real_escape_string*/($gradDate),
                                      /*mysql_real_escape_string*/($creatorID));
                }else{
                    $query  = sprintf($query,
                                      /*mysql_real_escape_string*/($gradDate));
                }

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if ($getCountOnly == 0){
                    return $resultSet;
                }else{
                    return mysqli_num_rows($resultSet);
                }
            }

            function getGradList($schoolID, $gradDate, $creatorID = 0, $getCountOnly = 0){
                $query  = 'SELECT * FROM tbl_gradlist ';
                $query .= 'WHERE school_id = %d ';
                $query .= "  AND grad_date = '%s' ";

                if ($creatorID == 0) {
                    $query .= '  AND created_by = %d ';
                    $query  = sprintf($query,
                                      /*mysql_real_escape_string*/($schoolID),
                                      /*mysql_real_escape_string*/($gradDate),
                                      /*mysql_real_escape_string*/($creatorID));
                } else {
                    $query  = sprintf($query,
                                      /*mysql_real_escape_string*/($schoolID),
                                      /*mysql_real_escape_string*/($gradDate));
                }

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if ($getCountOnly == 0) {
                    return $resultSet;
                } else {
                    return mysqli_num_rows($resultSet);
                }
            }

            function getGradListByID($gradListID){

                $query  = "SELECT * from tbl_gradlist ";
                $query .= "WHERE id = %d ";
                $query = sprintf($query,
                                /*mysql_real_escape_string*/($gradListID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
                return mysqli_fetch_array($resultSet, MYSQLI_ASSOC);
            }

            function getBBGradListByID($gradListID) {
                $query  = "SELECT * from tbl_bb_gradlist ";
                $query .= "WHERE id = %d ";
                $query = sprintf($query,
                                 /*mysql_real_escape_string*/($gradListID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
                return mysqli_fetch_array($resultSet, MYSQLI_ASSOC);
            }

            //Return students in a graduation list
            function getGradStudents($gradListID, $getCountOnly = 0, $orderForCSV = 0, $getFamiliesOnly = 0, $rankType = 0){

                if ($getCountOnly == 1) {
                    $query = 'SELECT * FROM tbl_gradlist_students WHERE gradlist_id = %d';
                } else {
                        if ($orderForCSV > 0) {
                                $query  = 'SELECT s.*, r2.sequence as old_sequence, r1.rank_name as new_rank, r1.sequence as new_sequence, g.grad_date, sch.name as school_name ';
                                $query .= 'FROM tbl_students s, tbl_ranks r1, tbl_ranks r2, tbl_gradlist g, tbl_gradlist_students gs, tbl_schools sch ';
                                $query .= 'WHERE gs.gradlist_id = %d ';
                                $query .= '  AND g.id = gs.gradlist_id ';
                                $query .= '  AND s.id = gs.student_id ';
                                $query .= '  AND s.school_id = sch.id ';
                                $query .= '  AND r1.id = gs.new_rank_id ';
                                $query .= '  AND r2.id = gs.old_rank_id ';

                                if($rankType == 1){
                                            //show Solid Ranks only
                                                $query .= '   AND r1.payable = 1 ';
                                            }elseif($rankType == 2){
                                                    //show Adv Ranks only
                                                    $query .= '   AND r1.payable = 0 ';
                                            }

                                $query .= 'ORDER BY r2.sequence, s.last_name, s.first_name';
                        }elseif ($getFamiliesOnly == 1){
                                $query  = 'SELECT s.*, sf.family_id, r2.sequence as old_sequence, r1.sequence as new_sequence ';
                                $query .= 'FROM tbl_students s, tbl_ranks r1, tbl_ranks r2, tbl_gradlist g, tbl_gradlist_students gs, tbl_student_families sf ';
                                $query .= 'WHERE gs.gradlist_id = %d ';
                                $query .= '  AND g.id = gs.gradlist_id ';
                                $query .= '  AND s.id = gs.student_id ';
                                $query .= '  AND sf.student_id = s.id';
                                $query .= '  AND r1.id = gs.new_rank_id ';
                                $query .= '  AND r2.id = gs.old_rank_id ';
                                $query .= 'ORDER BY sf.family_id, r2.sequence, s.last_name, s.first_name';
                                    }else{
                            $query  = 'SELECT s.*, r.rank_name as old_rank_name, r.sequence as old_rank_sequence, gs.id as gs_id, gs.old_rank_id, gs.new_rank_id ';
                            $query .= 'FROM tbl_students s, tbl_gradlist_students gs, tbl_ranks r ';
                            $query .= 'WHERE gs.gradlist_id = %d ';
                            $query .= '  AND s.id = gs.student_id ';
                            $query .= '  AND r.id = gs.old_rank_id ';
                            $query .= 'ORDER BY r.sequence, s.last_name, s.first_name';
                        }
                }

                $query  = sprintf($query,
                                /*mysql_real_escape_string*/($gradListID));
                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if ($getCountOnly == 1){
                    return mysqli_num_rows($resultSet);
                }else{
                    return $resultSet;
                }
            }

            //Return students in a black belt graduation list
            function getBBGradStudents($gradListID, $getCountOnly = 0, $orderForCSV = 0, $getFamiliesOnly = 0, $rankType = 0){

                if ($getCountOnly == 1) {
                    $query = 'SELECT * FROM tbl_bb_gradlist_students WHERE gradlist_id = %d';
                } else {
                    if ($orderForCSV > 0) {
                        $query  = 'SELECT s.*, r2.sequence as old_sequence, r1.rank_name as new_rank, r1.sequence as new_sequence, g.grad_date, sch.name as school_name, sch.city as city, sch.state as state, sch.country as country ';
                        $query .= 'FROM tbl_students s, tbl_ranks r1, tbl_ranks r2, tbl_bb_gradlist g, tbl_bb_gradlist_students gs, tbl_schools sch ';
                        $query .= 'WHERE gs.gradlist_id = %d ';
                        $query .= '  AND g.id = gs.gradlist_id ';
                        $query .= '  AND s.id = gs.student_id ';
                        $query .= '  AND s.school_id = sch.id ';
                        $query .= '  AND r1.id = gs.new_rank_id ';
                        $query .= '  AND r2.id = gs.old_rank_id ';
                        $query .= 'ORDER BY s.school_id, r2.sequence, s.last_name, s.first_name';
                    } elseif ($getFamiliesOnly == 1) {
                        $query  = 'SELECT s.*, sf.family_id, r2.sequence as old_sequence, r1.sequence as new_sequence ';
                        $query .= 'FROM tbl_students s, tbl_ranks r1, tbl_ranks r2, tbl_bb_gradlist g, tbl_bb_gradlist_students gs, tbl_student_families sf ';
                        $query .= 'WHERE gs.gradlist_id = %d ';
                        $query .= '  AND g.id = gs.gradlist_id ';
                        $query .= '  AND s.id = gs.student_id ';
                        $query .= '  AND sf.student_id = s.id';
                        $query .= '  AND r1.id = gs.new_rank_id ';
                        $query .= '  AND r2.id = gs.old_rank_id ';
                        $query .= 'ORDER BY sf.family_id, r2.sequence, s.last_name, s.first_name';
                    } else {
                        $query  = 'SELECT s.*, r.rank_name as old_rank_name, r.sequence as old_rank_sequence, gs.id as gs_id, gs.old_rank_id, gs.new_rank_id ';
                        $query .= 'FROM tbl_students s, tbl_bb_gradlist_students gs, tbl_ranks r ';
                        $query .= 'WHERE gs.gradlist_id = %d ';
                        $query .= '  AND s.id = gs.student_id ';
                        $query .= '  AND r.id = gs.old_rank_id ';
                        $query .= 'ORDER BY r.sequence, s.last_name, s.first_name';
                    }
                }

                $query  = sprintf($query,
                                /*mysql_real_escape_string*/($gradListID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if ($getCountOnly == 1){
                    return mysqli_num_rows($resultSet);
                }else{
                    return $resultSet;
                }
            }

            //Create a gradlist
            function createGradList($schoolID, $gradDate, $creatorID){
                $listExists = $this->getGradList($schoolID, $gradDate, $creatorID, 1);

                if ($listExists == 0) {
                    $query  = 'INSERT INTO tbl_gradlist';
                    $query .= '  (school_id, grad_date, created_by) ';
                    $query .= 'VALUES ';
                    $query .= "  (%d, '%s', %d) ";
                    $query  = sprintf($query,
                                      /*mysql_real_escape_string*/($schoolID),
                                      /*mysql_real_escape_string*/($gradDate),
                                      /*mysql_real_escape_string*/($creatorID));
                    mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                    //Return the ID of the list just created
                    $result = $this->getGradList($schoolID, $gradDate, $creatorID);

                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    return $row["id"];
                } else {
                    return 0;
                }
            }

            //Create a black beltgradlist
            function createBBGradList($gradDate, $creatorID){
                $listExists = $this->getBBGradList($gradDate, $creatorID, 1);

                if ($listExists == 0){
                        $query  = 'INSERT INTO tbl_bb_gradlist';
                                $query .= '  (grad_date, created_by) ';
                                $query .= 'VALUES ';
                                $query .= "  ('%s', %d) ";
                                $query  = sprintf($query,
                                                                /*mysql_real_escape_string*/($gradDate),
                                    /*mysql_real_escape_string*/($creatorID));

                                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                                //Return the ID of the list just created
                    $result = $this->getBBGradList($gradDate, $creatorID);

                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    return $row["id"];
                            }else{
                    return 0;
                }
            }

            //Insert data into a gradlist
            function addGrad($gradlistID, $studentID, $oldRankID, $newRankID){
                $query  = 'INSERT INTO tbl_gradlist_students';
                        $query .= '  (gradlist_id, student_id, old_rank_id, new_rank_id) ';
                        $query .= 'VALUES ';
                        $query .= "  (%d, %d, %d, %d) ";
                        $query  = sprintf($query,
                                                          /*mysql_real_escape_string*/($gradlistID),
                                                          /*mysql_real_escape_string*/($studentID),
                                                          /*mysql_real_escape_string*/($oldRankID),
                              /*mysql_real_escape_string*/($newRankID));

                        mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

            //Insert data into a black belt gradlist
            function addBBGrad($gradlistID, $studentID, $oldRankID, $newRankID){
                $query  = 'INSERT INTO tbl_bb_gradlist_students';
                $query .= '  (gradlist_id, student_id, old_rank_id, new_rank_id) ';
                $query .= 'VALUES ';
                $query .= "  (%d, %d, %d, %d) ";
                $query  = sprintf($query,
                                                    /*mysql_real_escape_string*/($gradlistID),
                                                    /*mysql_real_escape_string*/($studentID),
                                                    /*mysql_real_escape_string*/($oldRankID),
                        /*mysql_real_escape_string*/($newRankID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

            //Update student rank in gradlist
            function updateGradRank($gradlistID, $studentID, $newRankID){
                $query  = "UPDATE tbl_gradlist_students ";
                $query .= "SET new_rank_id = %d ";
                        $query .= "WHERE student_id = %d AND gradlist_id = %d";
                        $query  = sprintf($query,
                                                          /*mysql_real_escape_string*/($newRankID),
                                                          /*mysql_real_escape_string*/($studentID),
                              /*mysql_real_escape_string*/($gradlistID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

            //Update students current rank in gradlist
            function updateGradOldRank($gradlistID, $studentID){
                    $subQuery = 'SELECT rank_id FROM tbl_students WHERE id = %d';

                    $subQuery = sprintf($subQuery, /*mysql_real_escape_string*/($studentID));

                $query  = "UPDATE tbl_gradlist_students ";
                $query .= "SET old_rank_id = ($subQuery) ";
                        $query .= "WHERE student_id = %d AND gradlist_id = %d";
                        $query  = sprintf($query,
                                                          /*mysql_real_escape_string*/($studentID),
                              /*mysql_real_escape_string*/($gradlistID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

                //Update student rank in black belt gradlist
            function updateBBGradRank($gradlistID, $studentID, $newRankID){
                $query  = "UPDATE tbl_bb_gradlist_students ";
                $query .= "SET new_rank_id = %d ";
                        $query .= "WHERE student_id = %d AND gradlist_id = %d";
                        $query  = sprintf($query,
                                                          /*mysql_real_escape_string*/($newRankID),
                                                          /*mysql_real_escape_string*/($studentID),
                              /*mysql_real_escape_string*/($gradlistID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

            //Update a school's credit after grad list finalization
            function updateSchoolCredit($schoolID, $creditAmount){
                $query = 'UPDATE tbl_schools SET credit = %d WHERE id = %d';

                $query = sprintf($query,
                                                    /*mysql_real_escape_string*/($creditAmount),
                            /*mysql_real_escape_string*/($schoolID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

            function updateGradDiplomaRequirement($gradlistID, $qty){
                $query  = "UPDATE tbl_gradlist ";
                $query .= "SET diplomas_needed = %d ";
                        $query .= "WHERE id = %d";
                        $query  = sprintf($query,
                                                          /*mysql_real_escape_string*/($qty),
                                                          /*mysql_real_escape_string*/($gradlistID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                return mysqli_affected_rows($this->link) == 1;
            }

            //Remove student in gradlist
            function removeGrad($gradStudentID){
                $query  = "DELETE FROM tbl_gradlist_students ";
                $query .= "WHERE id = %d";
                $query  = sprintf($query, $gradStudentID);

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

            //Remove student in black belt gradlist
            function removeBBGrad($gradStudentID){
                $query  = "DELETE FROM tbl_bb_gradlist_students ";
                $query .= "WHERE id = %d";
                $query  = sprintf($query, $gradStudentID);

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }
            
            //Undo a student's graduation from a finalized grad list
            //
            //NOTE: Can only perform this if there are no future graduations for this student.
            //If an error is caught...will return error string
            function undoGrad($gradStudentID, $schoolID, $userID, $gradFee){
                $errMsg = '';	
            
                //STEP 1: Check to see if this is latest students grad.
                $results = explode(',', $this->checkForLatestGrad($gradStudentID));
                
                if ($results !== FALSE){
                    $studentID = $results[0];
                    $currentRank = $results[1];
                    $priorRank = $results[2];
                    
                    if(!isset($currentRank) && !isset($priorRank)){
                        $errMsg = 'Unable to undo graduate due to existance in future grad lists.';	
                    }else{				
                        //STEP 2: Reset student to prior rank
                        $studentDAO = new StudentDAO($this->DB_server,$this->DB_user, $this->DB_pass, $this->DB_conn, 0);
                        $studentDAO->updateStudentRank($studentID, $priorRank);
        
                        //STEP 3: Give credit in system to proper academy (tricky)				
                        if(strlen($errMsg) == 0){
                            if(!is_null($priorRank) || !is_null($currentRank)){
                                $creditAmt = $this->calculateSchoolCredit($priorRank, $currentRank);
                                $creditAmt = $creditAmt * $gradFee;
                                $this->updateSchoolCredit($schoolID,$creditAmt);
                            }else{
                                $errMsg = 'Unable to credit proper amount in system';
                            }
                        }
                        
                        //STEP 4: Remove student from gradlist
                        if(strlen($errMsg) == 0){
                            $this->removeGrad($gradStudentID);
                        }
                    }
                }else{
                    $errMsg = 'Unable to validate for lastest grad.';
                }
                
                return $errMsg;			
            }

            //Made just for undoGrad(...)
            //Compares the student's current rank to new_rank_id in grad list
            //If (=) Returns student_id
            //Else   Returns 0
            function checkForLatestGrad($gradStudentID){
                $query  = 'SELECT gs.student_id, s.rank_id as current_rank, gs.new_rank_id as grad_rank, gs.old_rank_id as prior_rank ';
                $query .= ' FROM tbl_gradlist_students gs, tbl_students s ';
                $query .= 'WHERE gs.id = %d ';
                $query .= '  AND s.id = gs.student_id';
                
                $query = sprintf($query, $gradStudentID);
                
                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " [$query]");
                if($row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC)){
                    if($row['current_rank'] == $row['grad_rank']){
                        return $row['student_id'] . ',' . $row['current_rank'] . ',' . $row['prior_rank'];
                    }    
                }
                
                return 0;		
            }

            //Made just for undoGrad(...)
            //Determines how much credit to give back
            function calculateSchoolCredit($oldRank, $newRank){
                $query  = 'SELECT sum(payable) as credit FROM tbl_ranks ';
                $query .= 'WHERE sequence > (SELECT sequence FROM tbl_ranks WHERE id = %d) ';
                $query .= 'AND  sequence <= (SELECT sequence FROM tbl_ranks WHERE id = %d)';
                
                $query = sprintf($query,
                                mysqli_real_escape_string($this->link, $oldRank),
                                mysqli_real_escape_string($this->link, $newRank));
                
                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " [$query]");
                if ($row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC)) {
                    return $row['credit'];   
                }
                
                return 0;		
            }

            //Removes a grad list and it's graduates
            function removeGradList($gradListID){
                $query  = "DELETE FROM tbl_gradlist_students ";
                $query .= "WHERE gradlist_id = %d";
                $query  = sprintf($query,
                                  mysqli_real_escape_string($this->link, $gradListID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                $query  = "DELETE FROM tbl_gradlist ";
                $query .= "WHERE id = %d";
                $query  = sprintf($query,
                                  mysqli_real_escape_string($tihs->link, $gradListID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }

            //Returns if the graduation list has been paid for
            //This is needed due to the old way of Payment Verification
            function isGradListPaid($gradListID){
                $query  = 'SELECT * FROM tbl_gradlist_paid ';
                $query .= 'WHERE gradlist_id = %d ';

                $query  = sprintf($query,
                                  /*mysql_real_escape_string*/($gradListID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if (mysqli_num_rows($resultSet) > 0){
                    return true;
                }else{
                    return $this->isGradListPaid2($gradListID);
                }
            }
            
            //Returns if the graduation list has been paid for
            //This is needed due to the new IPN Paypal Verification Method
            function isGradListPaid2($gradListID){
                //NEW WAY --> IPN PayPal Verification
                $query  = 'SELECT * FROM tbl_paypal_response ';
                $query .= "WHERE item_number = %d AND payment_status = 'Completed'";
                
                $query = sprintf($query, $gradListID);
                $result = mysqli_query($this->link, $query) or die('Paypal response still pending...');
                
                if (mysqli_num_rows($result) == 0){return false;}
                else{return true;}
            }        

            //Returns if the graduation list has been paid for
            function isBBGradListReadOnly($gradListID){
                $query  = 'SELECT * FROM tbl_bb_gradlist ';
                $query .= 'WHERE id = %d AND read_only = 1';

                $query  = sprintf($query,
                                  /*mysql_real_escape_string*/($gradListID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                if (mysqli_num_rows($resultSet) > 0){
                    return true;
                }else{
                    return false;
                }
            }

            //Returns the amount of grad fees owed
            function calculateGradFees($gradListID, $userID, $fullFee){

                //retrieval of ranks
                $studentDAO = new StudentDAO($this->DB_server,$this->DB_user, $this->DB_pass, $this->DB_conn, $userID);
                $rankRS = $studentDAO->getRankList();
                while($rankRow = mysqli_fetch_array($rankRS, MYSQLI_ASSOC)){
                    $rankList[intval($rankRow['sequence'])] = $rankRow['rank_name'];
                    $rankListPay[intval($rankRow['sequence'])] = $rankRow['payable'];
                }

                //retrieval of students per grad list
                $studentRS = $this->getGradStudents($gradListID, 0, 1);
                $totalFees = 0;
                $diplomaCount = 0;
                while($row = mysqli_fetch_array($studentRS, MYSQLI_ASSOC)){
                    $oldSeq = intval($row['old_sequence']);
                    $newSeq = intval($row['new_sequence']);

                    //Calculate payable rank fee total
                    for($ii = $oldSeq; $ii < $newSeq; $ii++){
                        if($rankListPay[$ii+1] == 1){
                            $totalFees += $fullFee;
                            $diplomaCount++;
                        }
                    }
                }

                //Once we now how many JRI diplomas we need, let's save that information
                $this->updateGradDiplomaRequirement($gradListID, $diplomaCount);
                return $totalFees;
            }

            //-Returns the credit a school has toward payment of a gradlist
            //-Credits can only be given via Administrators
            function getSchoolCredit($gradListID)
            {
                $query  = 'SELECT s.credit FROM tbl_schools s, tbl_gradlist g ';
                $query .= 'WHERE g.id = %d AND s.id = g.school_id ';
                $query  = sprintf($query,
                                  /*mysql_real_escape_string*/($gradListID));

                $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                $creditAmount = 0.00;
                            while($row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC)){
                                    $creditAmount = $row['credit'];
                            }

                            return $creditAmount;
            }

            function calculateGradDiscount($gradListID, $discount = 0){

                //retrieval of ranks
                $studentDAO = new StudentDAO($this->DB_server, $this->DB_user, $this->DB_pass, $this->DB_conn, $userID);
                $rankRS = $studentDAO->getRankList();
                while($rankRow = mysqli_fetch_array($rankRS, MYSQLI_ASSOC)){
                        $rankList[intval($rankRow['sequence'])] = $rankRow['rank_name'];
                        $rankListPay[intval($rankRow['sequence'])] = $rankRow['payable'];
                }

                //retrieval of students per grad list
                $studentRS = $this->getGradStudents($gradListID,0,0,1);
                $totalCount = 0;
                $oldFamily = -1;
                $applyDiscount = -1;
                while($row = mysqli_fetch_array($studentRS, MYSQLI_ASSOC)){
                        $oldSeq = intval($row['old_sequence']);
                        $newSeq = intval($row['new_sequence']);

                        $currentFamily = $row['family_id'];
                        if($oldFamily != $currentFamily){
                                $oldFamily = $currentFamily;
                                if($applyDiscount > 0){$totalCount += $applyDiscount;}
                                $applyDiscount = -1;
                        }

                        //Calculate discount total
                        for($ii = $oldSeq; $ii < $newSeq; $ii++){
                                if($rankListPay[$ii+1] == 1){
                                            //echo $row['last_name'] . ' is getting ' . $rankList[$ii+1] . ' in which payable = ' . $rankListPay[$ii+1] . '<br>';
                                        $applyDiscount++;
                                        $ii = $newSeq;
                                }
                        }
                }

                if($applyDiscount > 0){$totalCount += $applyDiscount;}

                return ($discount * $totalCount);
            }

            function priceVerifyStart($gradListID){
                $PVCValue = 'testPVCValue' . time();//substr(hash('ripemd160', 'PVC Value is ' . time()), 0, 15);
                $query  = 'UPDATE tbl_gradlist ';
                $query .= "SET pvc = '$PVCValue' WHERE id = $gradListID";

                mysqli_query($this->link, $query) or die('Unable to start local price verification step...');

                return $PVCValue;
            }

            function priceVerifyFinal($gradListID, $PVCValue){
                
                //NEW WAY --> IPN PayPal Verification
                $query  = 'SELECT * FROM tbl_paypal_response ';
                $query .= "WHERE item_number = %d AND payment_status = 'Completed'";

                $query = sprintf($query, $gradListID);
                $result = mysqli_query($this->link, $query) or die('Paypal response still pending...');

                if (mysqli_num_rows($result) != 0){
                    return 1;
                }else{
                    //If NEW WAY isn't working...
                    //OLD WAY        	
                    $query  = 'SELECT * FROM tbl_gradlist ';
                    $query .= "WHERE id = %d AND pvc = '%s' ";

                    $query = sprintf($query, $gradListID, $PVCValue);

                    $result = mysqli_query($this->link, $query) or die('Unable to start local price verification step...');

                    if (mysqli_num_rows($result) == 0){return 0;}
                    else{return 1;}
                }
            }
            
            function insertPaypalTransaction($txn_id, $item_name, $item_number, $payment_date, $payment_amount){
                $query  = 'INSERT INTO tbl_paypal_response (txn_id, item_name, item_number, payment_date, payment_status, payment_amount, ';
                $query .= '                                 payment_currency, receiver_email, payer_email) ';
                $query .= "                         VALUES ('$txn_id', 'JOGMA Internal Credit', '$item_number', '$payment_date', 'Completed', $payment_amount, ";
                $query .= "					                'USD', 'n/a', 'n/a')";	
                
                try {
                    mysqli_query($this->link, $query);
                }
                catch (Exception $e) {
                    die('An error has occurred at GradListDAO->insertPaypalTransaction() - ' . $e->getMessage());
                }
            }        

            function markListPaid($userID, $gradListID, $amount){
                $query  = 'INSERT INTO tbl_gradlist_paid ';
                $query .= '     (user_id, gradlist_id, amount) ';
                $query .= 'VALUES ';
                $query .= '     (%d, %d, %d)';

                $query = sprintf($query,
                                 /*mysql_real_escape_string*/($userID),
                                 /*mysql_real_escape_string*/($gradListID),
                                 /*mysql_real_escape_string*/($amount));

                mysqli_query($this->link, $query) or die('Unable to mark GradList as Paid due to ... ' . mysqli_error($this->link));
            }

            function markListReadOnly($gradListID, $status = 1){
                $query  = 'UPDATE tbl_gradlist ';
                $query .= '   SET read_only = '. $status;
                $query .= ' WHERE id = %d';
                $query = sprintf($query,
                                /*mysql_real_escape_string*/($gradListID));
                mysqli_query($this->link, $query) or die('Unable to mark graduation list as READ ONLY due to ... ' . mysqli_error($this->link));
            }

            function markBBListReadOnly($gradListID, $status = 1) {
                $query  = 'UPDATE tbl_bb_gradlist ';
                $query .= '   SET read_only = ' . $status;
                $query .= ' WHERE id = %d';
                $query = sprintf($query,
                                /*mysql_real_escape_string*/($gradListID));

                mysqli_query($this->link, $query) or die('Unable to mark graduation list as READ ONLY due to ... ' . mysqli_error($this->link));
            }
        }
?>