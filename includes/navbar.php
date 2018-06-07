<?php
	/**********************************
	 * 	File: navbar.php
	 * 	Desc: Main navigation side bar
	 *    Date: 09/13/05
	 *  Author: T. Wong
	 **********************************/

    if(isset($_COOKIE["uid"])){
	  	$loginID = $_COOKIE["uid"];
	}
		 
    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
    $isAdmin = $userDAO->isSuperAdmin($loginID);	 
?>

<table bgcolor="Blue" border="0" cellpadding="1" width="100%">
<tr><td>
	<table align="left" bgcolor="White" width="100%">
	<tr><td class="tableHeader"><b>MENU</b></td></tr>
	<tr><td><a href="index.php?action=logout">Click here to logout</a></td></tr>
	<?php
	    for($i=0; $i < count($navLink); $i++){
			if(strpos($navURL[$i],'action=' . $action)){
			    $navImg = 'nav_plus.jpg';
			    $linkDisplay = '<b>' . $navLink[$i] . '</b>';
			}else{
			    $navImg = 'nav_minus.jpg';
				$linkDisplay = '<a href="' . $navURL[$i] . '">' . $navLink[$i] . '</a>';
			}

			if(strcmp($navLink[$i],'Academy Manager') == 0){
			  	if($isAdmin == 1){
					echo('      <tr><td><img src="images/' . $navImg . '"> ' . $linkDisplay . '</tr></td>');    
				}
			}else{
				echo('      <tr><td><img src="images/' . $navImg . '"> ' . $linkDisplay . '</tr></td>');  
			}	        
	    }
	?>
		<tr><td>&nbsp;</td></tr>
		<tr><td><a href="downloads/JOGM_Manual_v1_0.doc">JOGMa User Manual Download</a></td></tr>
	</table>
</td></tr>
</table>
