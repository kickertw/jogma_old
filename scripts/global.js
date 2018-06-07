//Global Util Functions

$(document).ready(function(){
	$('input[name="checkAll"]').click(function(event){
		$('input[name="studentIDs[]"]').prop('checked', $(this).is(':checked'));
	});
});