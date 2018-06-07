<?php
    if (isset($testPayButton)){
        echo '<form action="' . $return . '" method="GET">';
    }else{
        echo '<form action="http://www.wongism.com/jriworld/paypal/testPay.php" method="POST">';
        $return = str_replace('&', '&amp;', $return);
    }
?>
<table align="center" width="80%">
    <tr><th align="center" colspan="3">Test Payment Page</th></tr>
    <tr><td align="center" colspan="3">(Normally to be done via <i>http://www.paypal.com</i>)</td></tr>
    <tr><td colspan="3"><hr width="100%"><br><br></td></tr>
<?php
    if (isset($testPayButton)){
?>
    <tr><td colspan="3" align="center">You have made a successful payment of $<b><?= $amount ?></b>!</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td colspan="3" align="center"><input name="returnButton" type="submit" value="Return"></td></tr>
<?php
    }else{
?>
    <tr><td align="right" width="30%">Payment To: </td><td width="5%">&nbsp;</td><td align="left"><?= $business ?></td></tr>
    <tr><td align="right" width="30%">Amount: </td><td width="5%">&nbsp;</td><td align="left">$<?= $amount ?></td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td align="right" width="30%">Name: </td><td width="5%">&nbsp;</td><td align="left"><input name="name" type="text" size="20" maxlength="50"></td></tr>
    <tr><td align="right" width="30%">E-mail: </td><td width="5%">&nbsp;</td><td align="left"><input name="email" type="text" size="30" maxlength="50"></td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td colspan="3" align="center"><input name="testPayButton" type="submit" value="Make a Payment"></td></tr>
<?php
    }
?>
</table>
<input name="return" type="hidden" value="<?= $return ?>"/>
<input name="amount" type="hidden" value="<?= $amount ?>"/>
</form>
