<?php
    include('Application.php');
	
	//Setting GET Vars
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	
	//Setting POST Vars
    $getCSVButton = isset($_POST['getCSVButton']) ? $_POST['getCSVButton'] : '';    
	
	//for downloading of graduation lists in CSV format	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($getCSVButton)){
		include('gradlist_csv.php');
	}elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['getBBCSVButton'])){
	  	include('gradlist_bb_csv.php');
	}else{
	    include('includes/userValidation.php');
	    include('includes/header.php');
	    
	    if ($action == 'stu'){
	        $bodySize += 100;
	    }
	    
	    if ($action == 'login' || $action == 'logout' || $action == ''){
	        include('login.php');
	    }
	    else{
?>
        <table align="center" width="100%">
        <tr>
            <td valign="top" width="<?= $navSize ?>"><?php include('includes/navbar.php'); ?></td>
            <td valign="top" width="<?= $bodySize ?>">
                <table bgcolor="Blue" border="0" cellpadding="1" width="100%">
                <tr><td>
<?php
                    if ($action == 'pro'){
                        include('profile.php');
                    }elseif ($action == 'am'){
                        include('academy.php');
                    }elseif ($action == 'stu'){
                        include('students.php');
                    }elseif ($action == 'stu.add'){
                        include('student_add.php');
                    }elseif ($action == 'stu.search'){
                        include('student_search.php');
                    }elseif ($action == 'stu.edit'){
                        include('student_edit.php');
                    }elseif ($action == 'gl'){
                        include('gradlist.php');
                    }elseif ($action == 'gl.bb'){
                        include('gradlist_bb_add.php');
                    }elseif ($action == 'gl.bbedit'){
                        include('gradlist_bb_edit.php');
                    }elseif ($action == 'gl.add'){
                        include('gradlist_add.php');
                    }elseif ($action == 'gl.edit' || $action == 'gl.rem'){
                        include('gradlist_edit.php');
                    }elseif ($action == 'gl.merge'){
                        include('gradlist_merge.php');
                    }elseif ($action == 'gl.order'){
                        include('diploma_order.php');
                    }elseif ($action == 'main'){
                        include('news.php');
                    }
?>
                </td></tr>
                </table>
            </td>
        </tr>
        </table>
<?php
    	}
    
    	include('includes/footer.php');
    }
?>
