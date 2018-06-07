<form action="index.php?action=main" method="post">
<table align="center" bgcolor="White" width="<?= $bodySize-10 ?>">
    <tr><td align="center" colspan="2" class="error"><br><?php if(isset($errMsg)){echo $errMsg;} ?><br><br></td></tr>
	<tr>
		<td align="right" width="45%">Username:&nbsp;&nbsp;</td>
		<td align="left" width="55%"><input name="user" type="text" maxlength="15" size="10" tabindex="1"></td>
	</tr>
	<tr>
		<td align="right" width="45%">Password:&nbsp;&nbsp;</td>
		<td align="left" width="55%"><input name="pwd" type="password" maxlength="15" size="15" tabindex="2"></td>
	</tr>
	<tr>
		<td align="right" width="45%">&nbsp;</td>
		<td align="left" width="55%"><br><input name="loginButton" type="submit" value="Login" tabindex="3"></td>
	</tr>
</table>
</form>

<script language="JavaScript" type="text/javascript">
	window.onload = function() {
  		document.getElementById('user').focus();
	}  
</script>
  
