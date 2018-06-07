<?php
    //If there is no step set...
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addMoreStudentsButton'])){
        $editMode = true;
        $step = 3;
        include('gradlist_add.php');
?>
                </td></tr>
                </table>
            </td>
        </tr>
        </table>
<?php
        include('includes/footer.php');
		die();
    }

    //Including DAO Classes
    require_once($classpath . 'GradListDAO.php');
    require_once($classpath . 'StudentDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($_COOKIE["uid"]);

    $gradListDAO = new GradListDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $studentDAO = new StudentDAO($DB_server, $DB_user, $DB_pass, $DB_conn, $_COOKIE["uid"], $isAdmin);
    
    //SETTING POST VARS
    $updateListButton = $_POST['updateListButton'];
    $userIDs = $_POST['userIDs'];
    $gradListID = $_POST['gradListID'];
    
    //SETTTING REQUEST VARS
    $gsid = $_REQUEST['gsid'];
    $glid = $_REQUEST['glid'];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($updateListButton)){
    	
        $idList = explode(',',$userIDs);

        for($ii = 0;$ii < count($idList);$ii++){
            eval('$newRank = $_POST[\'newRank_' . $idList[$ii]. '\'];');
            $gradListDAO->updateGradRank($gradListID,$idList[$ii],$newRank);
        }
        
        $glid = $gradListID;
        $rankUpdateStatus = "Student's new ranks have been updated.";
    }

    if($gsid > 0){
        $gradListDAO->removeGrad($gsid);
    }

    if($glid > 0){
      	if($action == 'gl.rem'){
      		$gradListDAO->removeGradList($glid);
      		$glid = -1;
      		
			//Returning a list of graduation lists this user has access to
        	$gradListRS = $gradListDAO->getGradLists($_COOKIE["uid"]);      		
		}
		
        $studentListRS = $gradListDAO->getGradStudents($glid);
        $rankListRS = $studentDAO->getRankListByUser($_COOKIE["uid"]);

        if($rankListRS != false){
        	
        	$showRank = true;
        	$ii = 0;
            while($row = mysql_fetch_array($rankListRS)){
            	
            	if($showRank){
                	$rankListName[$ii] = $row['rank_name'];
                	$rankListID[$ii++] = $row['id'];
            	}
            	
				//We need to filter out anything above 2nd brown					                                        
                if($row['rank_name'] == '1st Brown'){
                	$showRank = false;	
                }                
            }
        }
    }else{
        //Returning a list of graduation lists this user has access to
        $gradListRS = $gradListDAO->getGradLists($_COOKIE["uid"]);
    }
?>

    <table align="left" bgcolor="White" width="100%">
    <tr>
        <th class="title" colspan="2">Edit Your Graduation List</th>
    </tr>
    <tr><td>&nbsp;</td></tr>
<?php
    if(strlen($rankUpdateStatus) > 0){
        echo '  <tr><td colspan="2" align="center" class="success">' . $rankUpdateStatus . '</td></tr><tr><td>&nbsp;</td></tr>';
    }

    if($glid > 0){
?>
    <tr><td colspan="2">
<?php
        include('gradlist_table.php');
?>
    </td></tr>
<?php
    }else{
?>
    <tr>
        <td width="5%">&nbsp;</td>
        <td align="left">
<?php
        if(mysql_num_rows($gradListRS) > 0){
            $currentSchool = '';
            $firstSchool = true;
?>
<?php
            while($glRow = mysql_fetch_array($gradListRS)){
                if($currentSchool != $glRow['school_id']){
                    $currentSchool = $glRow['school_id'];
                    if(!$firstSchool){echo '            </ul>';}
                    else{$firstSchool = false;}
                    
?>
            <u><b><?= $glRow['school_name'] ?></b></u>
            <ul>
<?php
                }
                
                $gradDate = date("F",strtotime($glRow['grad_date'])) . ' ' . date("j",strtotime($glRow['grad_date'])) . ', ' . date("Y",strtotime($glRow['grad_date']));
				$removeButton = '';
				                
                if($glRow['read_only'] == 0){
					$removeButton = ' <a href="index.php?action=gl.rem&glid=' . $glRow['id'] . '" onclick="return confirm(\'Are you sure you want to remove the graduation list for [' . $gradDate . ']?\');"><img src="' . $imgRoot . 'button_delete.png" border="0" alt="Remove List"></a>';
				}
?>
                <li><a href="index.php?action=gl.edit&scid=<?= $glRow['school_id'] ?>&glid=<?= $glRow['id'] ?>"><?= $gradDate ?></a><?= $removeButton ?></li>
<?php
            }
?>
            </ul>
<?php
        }
?>
        </td>
    </tr>
<?php
    }
?>
    </table>
