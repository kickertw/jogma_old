<?php
    /***********************************************
     *  File: StudentDAO.php
     *  Desc: class StudentDAO is needed to interface
     *        with tbl_Students
     *  Date: 09/18/05
     *  Author: T. Wong
     ***********************************************/

    class StudentDAO{
        public $link;
        public $currentUserID;
        public $isSuperAdmin;

        function StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $userID = 0, $adminFlag = 0){
            $this->link = mysqli_connect($DB_server, $DB_user, $DB_pass, $DB_conn) or DIE("unable to connect to $DB_server");
            $this->currentUserID = $userID;
            $this->isSuperAdmin = $adminFlag;
        }

        //Return student based upon their ID
        function getStudent($studentID){
            $query  = "SELECT * FROM tbl_students ";
            $query .= "WHERE id = $studentID ";

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return $resultSet;
        }

        //Return students based upon thier school/rank/belt/name
        function getStudents($schoolID, $rankID, $firstName, $lastName, $isActive, $orderBy = '', $orderDir = '', $orderBy2 = '', $orderDir2 = '', $gradListID = 0, $birthDate = '', $isUnderbeltGrad = false) {
            $fromClause = 'FROM tbl_students s, tbl_ranks r, tbl_schools sch';
            $whereClause = "WHERE 1 = 1 ";

            if($schoolID > 0){
                $whereClause .= "AND s.school_id = $schoolID ";
            }

            if(isset($rankID) && $rankID > 0){
                $whereClause .= "AND s.rank_id = $rankID ";
            }

            if(strlen(trim($firstName)) > 0){
                $whereClause .= "AND s.first_name LIKE '$firstName%' ";
            }

            if(strlen(trim($lastName)) > 0){
                $whereClause .= "AND s.last_name LIKE '$lastName%' ";
            }

            if(strlen(trim($birthDate)) > 0){
                $whereClause .= "AND s.birthdate = '$birthDate' ";
            }

            if($isActive == 0){
                $whereClause .= 'AND s.active = 0 ';
            }elseif($isActive == 1){
                $whereClause .= 'AND s.active = 1 ';
            }

            if($gradListID > 0){

                    if ($isUnderbeltGrad){
                            $gradStudentIDs = $this->getGradStudentIDs($gradListID);
                    }else{
                        $gradStudentIDs = $this->getBBGradStudentIDs($gradListID);
                    }

                if(strlen($gradStudentIDs) > 0){
                    $whereClause .= "AND s.id NOT IN ($gradStudentIDs) ";
                }
            }

            if ($isUnderbeltGrad){
                $whereClause .= 'AND r.bb_promotable = 0 ';
            }

            $query  = "SELECT s.*, r.rank_name, r.sequence, sch.location_code $fromClause ";//, tbl_user_school_access usa ";
            $query .= $whereClause;
            $query .= "AND s.school_id = sch.id ";
            $query .= "AND r.id = s.rank_id ";

            /*          
                if($this->isSuperAdmin == 0){
                    $query .= "AND s.school_id = usa.school_id ";
                    $query .= "AND usa.user_id = " . $this->currentUserID . " ";
                }
            */

            if(strlen(trim($orderBy)) > 0 ){
                $query .= "ORDER BY $orderBy $orderDir ";

                if(strlen(trim($orderBy2)) > 0){
                    $query .= ", $orderBy2 $orderDir2";
                }
            }

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return $resultSet;
        }

        //Returns a string of comma delimited Student_ids of a specific gradlist
        function getGradStudentIDs($gradListID){
            $query = "SELECT student_id FROM tbl_gradlist_students WHERE gradlist_id = %d";
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($gradListID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            if(mysqli_num_rows($resultSet) > 0){
                while($row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC)){
                    if(strlen($studentIDs) == 0){
                        $studentIDs = $row['student_id'];
                    }else{
                        $studentIDs .= ', ' . $row['student_id'];
                    }
                }
            }else{
                $studentIDs = '';
            }

            return $studentIDs;

        }

        //Returns a string of comma delimited Student_ids of a specific black belt gradlist
        function getBBGradStudentIDs($gradListID){
            $query = "SELECT student_id FROM tbl_bb_gradlist_students WHERE gradlist_id = %d";
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($gradListID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            if(mysqli_num_rows($resultSet) > 0){
                while($row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC)){
                    if(strlen($studentIDs) == 0){
                        $studentIDs = $row['student_id'];
                    }else{
                        $studentIDs .= ', ' . $row['student_id'];
                    }
                }
            }else{
                $studentIDs = '';
            }

            return $studentIDs;

        }

        function getRankList($startSeq = 0, $onlyPayable = -1, $onlyUnderbelt = 1) {

            if ($onlyUnderbelt == 1){
                $whereClause = 'WHERE blackbelt = 0 ';
            }else{
                $whereClause = 'WHERE blackbelt >= 0 ';
            }

            if ($startSeq > 0){
                $whereClause .= ' AND sequence >= ' . $startSeq;
            }

            if ($onlyPayable > -1){
                $whereClause .= ' AND payable = ' . $onlyPayable;
            }

            $query = "SELECT * FROM tbl_ranks $whereClause ORDER BY sequence, id";
            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return $resultSet;
        }

        function getRankListByUser($userID, $includeBlack = 0) {
            $query = 'SELECT show_advanced_ranks FROM tbl_users WHERE id = %d';
            $query = sprintf($query,
                        /*mysql_real_escape_string*/($userID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            $currentUser = mysqli_fetch_array($resultSet, MYSQLI_ASSOC);

            $query  = 'SELECT * FROM tbl_ranks ';

            if($currentUser['show_advanced_ranks'] == 0){
                $query .= ' WHERE payable = 1 ';
                
                if ($includeBlack == 1){
                    $query .= ' OR blackbelt = 1 ';
                }
            }

            $query .= 'ORDER BY sequence, id';
            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
		    return $resultSet;
        }

        function getBBCandidateRankList() {
            $query  = 'SELECT * FROM tbl_ranks ';
            $query .= "WHERE rank_name = '1st Brown' OR blackbelt = 1 ";
            $query .= 'ORDER BY sequence, id';

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return $resultSet;
        }

        function getSchool($schoolID) {
            $query  = "SELECT * from tbl_schools ";
            $query .= "WHERE id = %d ";
            $query .= "ORDER BY location_code";

            $query = sprintf($query,
                             /*mysql_real_escape_string*/($schoolID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_fetch_array($resultSet, MYSQLI_ASSOC);
        }

        function getSchoolByGradList($gradListID) {
            $query  = "SELECT s.* from tbl_schools s, tbl_gradlist g ";
            $query .= "WHERE g.id = %d AND s.id = g.school_id ";

            $query = sprintf($query,
                             /*mysql_real_escape_string*/($gradListID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_fetch_array($resultSet, MYSQLI_ASSOC);
        }

        function insertSchool($code, $name, $addy1, $addy2, $city, $state, $country, $postal, $poc, $parentID){
            $query  = "INSERT INTO tbl_schools ";
            $query .= "     (location_code, name, address1, address2, city, state, country, postal, poc";
            if (is_int($parentID) && $parentID > 0){
                $query .= "parent_id) ";
            }else{
                $query .= ") ";	
            }
            
            $query .= "VALUES";
            $query .= "     ('%s','%s','%s','%s','%s','%s','%s','%s','%s'";
            if (is_numeric($parentID) && $parentID != '0'){
                $query .= ", " . $parentID . ") ";
            }else{
                $query .= ") ";	
            }

            $query = sprintf($query,
                                /*mysql_real_escape_string*/($code),
                                /*mysql_real_escape_string*/($name),
                                /*mysql_real_escape_string*/($addy1),
                                /*mysql_real_escape_string*/($addy2),
                                /*mysql_real_escape_string*/($city),
                                /*mysql_real_escape_string*/($state),
                                /*mysql_real_escape_string*/($country),
                                /*mysql_real_escape_string*/($postal),
                                /*mysql_real_escape_string*/($poc));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_affected_rows($this->link) == 1;
        }

        function updateSchool($id, $code, $name, $addy1, $city, $state, $country,
                                $postal, $poc, $addy2 = ' ', $isActive = 1, $credit = 0.00, $parentID){

            $updateFields = '';
            $query  = "UPDATE tbl_schools SET ";
            $query .= "        location_code = '%s', name = '%s', address1 = '%s', address2 = '%s', city = '%s', ";
            $query .= "        state = '%s', country = '%s', postal = '%s', poc = '%s', active = %d, credit = %d ";
            if (is_numeric($parentID) && $parentID != '0'){
                $query .= ", parent_id = " . $parentID . " ";
            }
            $query .= "WHERE id = %d";
            $query = sprintf($query,
                                /*mysql_real_escape_string*/($code),
                                /*mysql_real_escape_string*/($name),
                                /*mysql_real_escape_string*/($addy1),
                                /*mysql_real_escape_string*/($addy2),
                                /*mysql_real_escape_string*/($city),
                                /*mysql_real_escape_string*/($state),
                                /*mysql_real_escape_string*/($country),
                                /*mysql_real_escape_string*/($postal),
                                /*mysql_real_escape_string*/($poc),
                                /*mysql_real_escape_string*/($isActive),
                                /*mysql_real_escape_string*/($credit),
                                /*mysql_real_escape_string*/($id));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return mysqli_affected_rows($this->link) == 1;
        }

        function getSchoolList($userID, $getAll = 0, $orderBy = 'location_code') {
            if($this->isSuperAdmin == 1 || $getAll == 1){
                $query = "SELECT * from tbl_schools ORDER BY $orderBy";
            }else{
                $query  = "SELECT s.* from tbl_schools s, tbl_user_school_access usa ";
                $query .= "WHERE usa.user_id = %d AND s.id = usa.school_id ";
                $query .= "ORDER BY $orderBy";

                $query = sprintf($query,
                                 /*mysql_real_escape_string*/($userID));
            }

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return $resultSet;
        }

        function getSubSchoolList($userID, $getAll = 0)
        {
            if($this->isSuperAdmin == 1 || $getAll == 1){
                $query = "SELECT * from tbl_schools WHERE parent_id IS NOT NULL";
            }else{
                $query  = "SELECT s.* from tbl_schools s, tbl_user_school_access usa ";
                $query .= "WHERE usa.user_id = %d AND s.parent_id = usa.school_id AND s.parent_id IS NOT NULL";

                $query = sprintf($query,
                                 /*mysql_real_escape_string*/($userID));
            }
            
            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return $resultSet;
        }
			
        function getSchoolAccess($userID, $schoolID){
            $query  = "SELECT access_level from tbl_user_school_access ";
            $query .= "WHERE user_id = %d AND school_id = %d ";

            $query = sprintf($query,
                             /*mysql_real_escape_string*/($userID),
                             /*mysql_real_escape_string*/($schoolID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            if(mysqli_num_rows($resultSet) == 0){
                return 0;
            }else{
                $row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC);
                return $row['access_level'];
            }
        }

        function updateSchoolDiplomaCount($schoolID, $checksum, $qtyToAdd){
            $query  = "UPDATE tbl_schools SET ";
            $query .= "        diploma_count = diploma_count + %d, diploma_order_checksum = '' ";
            $query .= "WHERE id = %d AND diploma_order_checksum = '%s'";
            $query = sprintf($query,
                            /*mysql_real_escape_string*/($qtyToAdd),
                            /*mysql_real_escape_string*/($schoolID),
                            /*mysql_real_escape_string*/($checksum));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_affected_rows($this->link) == 1;
        }

        function decrementSchoolDiplomaCount($schoolID, $qtyToAdd){
            $query  = "UPDATE tbl_schools SET ";
            $query .= "        diploma_count = diploma_count - %d ";
            $query .= "WHERE id = %d";
            $query = sprintf($query,
                            /*mysql_real_escape_string*/($qtyToAdd),
                            /*mysql_real_escape_string*/($schoolID));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_affected_rows($this->link) == 1;
        }

        function diplomaOrderVerifyStart($schoolID, $checkVal){
            $query  = 'UPDATE tbl_schools ';
            $query .= "SET diploma_order_checksum = '$checkVal'";
            $query .= "WHERE id = $schoolID";

            mysqli_query($this->link, $query) or die('Unable to start diploma price verification step...');

            return $PVCValue;
        }

        function updateStudent($id, $first, $last, $rankID, $phone1, $phone2, $addy1, $addy2,
                                $city, $state, $zip, $country, $beltSize, $schoolID, $familyID, $familyName,
                                $birthdate, $parentName, $programID, $enrollDate, $expireDate, $active, $subSchoolID){

            if($familyID < 0 && strlen(trim($familyName)) > 0){
                $familyID = $this->insertFamily(trim($familyName), $schoolID);
            }

            $updStatus = $this->addStudentToFamily($familyID, $id);

            if($updStatus == true){
                $updateFields = '';
                $query  = "UPDATE tbl_students SET ";
                $query .= "first_name = '$first', last_name = '$last', rank_id = $rankID, ";
                $query .= "phone1 = '$phone1', phone2 = '$phone2', address1 = '$addy1', address2 = '$addy2', ";
                $query .= "city = '$city', state= '$state', country = '$country', postal_code = '$zip', ";
                $query .= "school_id = $schoolID, birthdate = '$birthdate', program_id = $programID, active = $active, sub_school_id = $subSchoolID ";

                if (strlen(trim($beltSize)) > 0){
                    $updateFields .= ", belt_size = '" . trim($beltSize) . "' ";
                }

                if (strlen(trim($parentName)) > 0){
                    $updateFields .= ", parent_name = '" . trim($parentName) . "' ";
                }

                // if (strlen(trim($enrollDate)) > 0){
                //     $updateFields .= ", enroll_date = '" . trim($enrollDate) . "' ";
                // }

                // if (strlen(trim($expireDate)) > 0){
                //     $updateFields .= ", expire_date = '" . trim($expireDate) . "' ";
                // }

                $query .= $updateFields;
                $query .= "WHERE id = $id";

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            }else{
                return false;
            }

            return mysqli_affected_rows($this->link) == 1 || $updStatus;
        }

        function updateStudentStatus($studentIDs, $active){
            $query  = 'UPDATE tbl_students SET active = %d ';
            $query .= 'WHERE id IN (%s)';

            $query = sprintf($query,
                            mysqli_real_escape_string($this->link, $active),
                            mysqli_real_escape_string($this->link, $studentIDs));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_affected_rows($this->link) >= 1;
        }

        function updateStudentRank($studentID, $newRankID){
            $query  = 'UPDATE tbl_students ';
            $query .= 'SET rank_id = %d ';
            $query .= 'WHERE id = %d ';

            $query = sprintf($query,
                            /*mysql_real_escape_string*/($newRankID),
                            /*mysql_real_escape_string*/($studentID));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_affected_rows($this->link) == 1;
        }

        function updateStudentRankByGraduation($gradListID, $studentID) {
            $query  = 'UPDATE tbl_students s, tbl_gradlist_students gs ';
            $query .= 'SET s.rank_id = gs.new_rank_id ';
            $query .= 'WHERE s.id = %d ';
            $query .= '  AND gs.student_id = s.id ';
            $query .= '  AND gs.gradlist_id = %d ';

            $query = sprintf($query,
                             /*mysql_real_escape_string*/($studentID),
                             /*mysql_real_escape_string*/($gradListID));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_affected_rows($this->link) == 1;
        }

        function insertStudent($first, $last, $rankID, $phone1, $phone2, $addy1, $addy2,
                               $city, $state, $zip, $country, $beltSize, $schoolID, $birthDate,
                               $parentName, $programID, $enrollDate, $expireDate,
                               $active=1, $subSchoolID=0) {

            $childSchoolID = is_numeric($subSchoolID) ? intval($subSchoolID) : 0;

            $query  = "INSERT INTO tbl_students ";
            $query .= "     (first_name, last_name, rank_id, phone1, phone2, address1, address2, ";
            $query .= "                 city, state, postal_code, country, ";
            $query .= "                 belt_size, school_id, birthdate,  ";
            $query .= "                 parent_name, program_id, enroll_date, expire_date, active, sub_school_id, last_certification_date) ";
            $query .= "VALUES";
            $query .= "     ('%s','%s',%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s',%d,'%s','%s',%d,'%s','%s',%d, %d, NULL)";
            $query = sprintf($query,
                            /*mysql_real_escape_string*/($first),
                            /*mysql_real_escape_string*/($last),
                            /*mysql_real_escape_string*/($rankID),
                            /*mysql_real_escape_string*/($phone1),
                            /*mysql_real_escape_string*/($phone2),
                            /*mysql_real_escape_string*/($addy1),
                            /*mysql_real_escape_string*/($addy2),
                            /*mysql_real_escape_string*/($city),
                            /*mysql_real_escape_string*/($state),
                            /*mysql_real_escape_string*/($zip),
                            /*mysql_real_escape_string*/($country),
                            /*mysql_real_escape_string*/($beltSize),
                            /*mysql_real_escape_string*/($schoolID),
                            /*mysql_real_escape_string*/($birthDate),
                            /*mysql_real_escape_string*/($parentName),
                            /*mysql_real_escape_string*/($programID),
                            /*mysql_real_escape_string*/($enrollDate),
                            /*mysql_real_escape_string*/($expireDate),
                            /*mysql_real_escape_string*/($active),
                            /*mysql_real_escape_string*/($childSchoolID));
                    
            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return mysqli_affected_rows($this->link) == 1;
        }

        function insertFamily($displayName, $schoolID){
            $query  = 'INSERT INTO tbl_families ';
            $query .= '                (display_name, school_id) ';
            $query .= 'VALUES ';
            $query .= "                ('%s',%d)";

            $query = sprintf($query,
                            /*mysql_real_escape_string*/($displayName),
                            /*mysql_real_escape_string*/($schoolID));

            mysqli_query($this->link, $query);

            if(strlen(trim(mysqli_error($this->link))) > 0 && strpos(strtolower(mysqli_error($this->link)), 'duplicate entry') === false) {
                die(mysqli_error($this->link) . ' ' . $query);
            } elseif(strpos(strtolower(mysqli_error($this->link)), 'duplicate entry') === true) {
                $errMsg = '<div align="center">The family name being entered already exists and cannot be duplicated.<br><br>';
                $errMsg .= '<input type="button" value="Go Back" onclick="return history.back(-1);"/></div>';
                die($errMsg);
            }

            if(mysqli_affected_rows($this->link) == 1){
                    //Now we need to retrieve the ID of the user just inserted
                    $resultSet = mysqli_query($this->link, 'SELECT @@IDENTITY as familyID') or die('Unable to return FAMILY_ID after insert');
                    $row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC);
                    return $row['familyID'];
            }
                
            return -1;
        }

        function addStudentToFamily($familyID, $studentID){
            $query = 'DELETE FROM tbl_student_families WHERE student_id = %d';
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($studentID));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            if($familyID > 0) {
                $query  = 'INSERT INTO tbl_student_families ';
                $query .= ' (family_id, student_id) ';
                $query .= 'VALUES ';
                $query .= " (%d,%d)";

                $query = sprintf($query,
                                 /*mysql_real_escape_string*/($familyID),
                                 /*mysql_real_escape_string*/($studentID));

                mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

                return mysqli_affected_rows($this->link) == 1;
            }

            return true;
        }

        function getFamily($familyID){
            $query = 'SELECT * FROM tbl_families WHERE id = %d';
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($familyID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return $resultSet;
        }

        function getFamilyIDByStudent($studentID){
            $query  = 'SELECT family_id FROM tbl_student_families ';
            $query .= 'WHERE student_id = %d';
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($studentID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            if($row = mysqli_fetch_array($resultSet, MYSQLI_ASSOC)){
                return $row['family_id'];
            }

            return -1;
        }

        function getFamilyList($schoolID){
            $query = 'SELECT * FROM tbl_families WHERE school_id = %d ORDER BY display_name ASC';
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($schoolID));
            
            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return $resultSet;
        }

        function getProgramList(){
            $query = 'SELECT * FROM tbl_programs';

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return $resultSet;
        }

        function getStudentGradHistory($studentID){
            $query  = 'SELECT   gl.grad_date, r.rank_name ';
            $query .= 'FROM     tbl_gradlist gl, tbl_gradlist_students gls, tbl_ranks r ';
            $query .= 'WHERE    gls.student_id = %d ';
            $query .= '  AND    gl.id = gls.gradlist_id ';
            $query .= '  AND    gl.read_only = 1 ';
            $query .= '  AND    r.id = gls.new_rank_id ';
            $query .= 'ORDER BY gl.grad_date DESC';

            $query = sprintf($query,
                             /*mysql_real_escape_string*/($studentID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return $resultSet;
        }

        function getStudentBBGradHistory($studentID){
            $query  = 'SELECT   gl.grad_date, r.rank_name ';
            $query .= 'FROM     tbl_bb_gradlist gl, tbl_bb_gradlist_students gls, tbl_ranks r ';
            $query .= 'WHERE    gls.student_id = %d ';
            $query .= '  AND    gl.id = gls.gradlist_id ';
            $query .= '  AND    gl.read_only = 1 ';
            $query .= '  AND    r.id = gls.new_rank_id ';
            $query .= 'ORDER BY gl.grad_date DESC';

            $query = sprintf($query,
                             /*mysql_real_escape_string*/($studentID));

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return $resultSet;
        }

        function getStudentOldGradHistory($studentID){
            $query  = 'SELECT   h.date_rank_earned, h.rank_name ';
            $query .= 'FROM     tbl_old_grad_history h, tbl_students s ';
            $query .= "WHERE         s.id = $studentID ";
            $query .= 'AND                 h.firstname = s.first_name ';
            $query .= 'AND                 h.lastname = s.last_name ';
            $query .= 'ORDER BY date_rank_earned DESC';

            $resultSet = mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");
            return $resultSet;
        }

        function diplomaOrderVerifyFinal($schoolID, $checkVal) {
            $query  = 'SELECT * FROM tbl_schools ';
            $query .= "WHERE id = %d AND diploma_order_checksum = '%s' ";

            $query = sprintf($query,
                             /*mysql_real_escape_string*/($schoolID),
                             /*mysql_real_escape_string*/($checkVal));

            $result = mysqli_query($this->link, $query) or die('Unable to verify diploma price (final step)...' . mysqli_error($this->link));

            if (mysqli_num_rows($result) == 0){return 0;}
            else{return 1;}
        }

        function removeSchool($schoolID){
            $query  = 'DELETE FROM tbl_schools ';
            $query .= 'WHERE id = %d AND id NOT IN (SELECT distinct(school_id) FROM tbl_students)';
            $query = sprintf($query,
                             /*mysql_real_escape_string*/($schoolID));

            mysqli_query($this->link, $query) or die(mysqli_error($this->link) . " $query");

            return mysqli_affected_rows($this->link) == 1;
        }
    }
?>