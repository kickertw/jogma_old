<table align="left" bgcolor="White" width="100%">
	<tr>
	    <th class="title" colspan="2">Graduation List Manager</th>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;&nbsp;<img valign="bottom" src="images/nav_minus.jpg"> <a href="index.php?action=gl.add">Create a new list</a></td></tr>
	<tr><td>&nbsp;&nbsp;<img valign="bottom" src="images/nav_minus.jpg"> <a href="index.php?action=gl.edit">Search/edit a list</a></td></tr>
	
<?php
	require_once($classpath . 'UserDAO.php');

    $userDAO = new UserDAO($DB_server, $DB_user, $DB_pass, $DB_conn);
	$e_userInfo = $userDAO->getUserInfo($_COOKIE["uid"]);
	
	if ($e_userInfo['active'] == 1){
?>	  
	<tr><td>&nbsp;&nbsp;<img valign="bottom" src="images/nav_minus.jpg"> <a href="index.php?action=gl.merge">Finalize a list</a></td></tr>
	<tr><td>&nbsp;&nbsp;<img valign="bottom" src="images/nav_minus.jpg"> <a href="index.php?action=gl.order">Purchase Underbelt Diplomas</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><a href="downloads/JRICertTemplate.doc"><b>Microsoft Word Diploma Template Download</b> <i>(right-click to save)</i></a></td></tr>
<?php
	}
	
	if ($e_userInfo['access_level'] == $UAL_JRI_ADMIN ||
		$e_userInfo['access_level'] == $UAL_SITE_ADMIN){
?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;&nbsp;<img valign="bottom" src="images/nav_minus.jpg"> <a href="index.php?action=gl.bb">Create a Black Belt Graduate List</a></td></tr>
	<tr><td>&nbsp;&nbsp;<img valign="bottom" src="images/nav_minus.jpg"> <a href="index.php?action=gl.bbedit">Review a Black Belt Graduate List</a></td></tr>
<?php		
	}
?>	
</table>