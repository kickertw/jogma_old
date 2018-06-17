<html>
<head>
	<LINK REL=stylesheet HREF="css/main.css" TYPE="text/css"/>
	<LINK REL=stylesheet HREF="css/ui-lightness/jquery-ui-1.10.3.custom.min.css" TYPE="text/css"/>
	<title>Jhoon Rhee Institute - Web Manager</title>
	<script type="text/javascript" src="scripts/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="scripts/jquery-ui-1.8.18.custom.min.js"></script>
	<script type="text/javascript" src="scripts/global.js"></script>
	<?php if ($action == 'am') { ?>
		<script type="text/javascript">
			$(document).ready(function(){
				var schoolOptions = $('select[name="schoolID"]').html();
				$('select[name="parentSchoolID"]').append(schoolOptions);
				$('option[value="-1"]', 'select[name="parentSchoolID"]').remove();
			});
		</script>
	<?php } else if ($action == 'stu.add') { ?>
		<script type="text/javascript">
			$(document).ready(function(){
				$('select[name="childSchoolID"]').val(0);
			});
		</script>
	<?php } ?>	
</head>
<body>

<table width="<?= $appSize ?>" align="center">
<tr align="center"><th><img src="images/header.jpg" border="0" style="width:40%"/></th></tr>
<tr align="center"><td>