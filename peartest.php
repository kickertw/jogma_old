<?php
if(isset($download) && $download == 1){
    header("Content-Type: application/CSV");
    header("Content-Disposition: attachment; filename=test.csv");
    echo 'first_name,last_name,rank
tim,wong,white
jay,wong,blue
joe,doe,green';
}else{
    echo '<a href="peartest.php?download=1">get me</a>';
}
?>
