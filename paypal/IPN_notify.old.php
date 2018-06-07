<?php
$DB_server = 'p50mysql25.secureserver.net';
$DB_user = "jogmaSQL";
$DB_pass = "j0gm@SQL";
$DB_conn = "jogmaSQL";
$paypal_IPN_URL = 'www.sandbox.paypal.com';

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ($paypal_IPN_URL, 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_date = $_POST['payment_date'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

// store values in the DB
$dbh = mysql_connect ($DB_server, $DB_user, $DB_pass);
mysql_select_db ($DB_conn);

$query  = 'INSERT INTO tbl_paypal_response (txn_id, item_name, item_number, payment_date, payment_status, payment_amount, ';
$query .= '                                 payment_currency, receiver_email, payer_email) ';
$query .= "                         VALUES ('$txn_id', '$item_name', '$item_number', '$payment_date', '$payment_status', $payment_amount, ";
$query .= "					                '$payment_currency', '$receiver_email', '$payer_email')";

$resultSet = mysql_query($query) or mail('timothy.t.wong@gmail.com', 'error', mysql_error());

if (!$fp) {
	// HTTP ERROR
	mail('timothy.t.wong@gmail.com', 'error', 'HTTP Error: Unable to open socket to ' . $paypal_IPN_URL);
} else {	
	/*fputs ($fp, $header . $req) or mail('timothy.t.wong@gmail.com', 'error', 'fputs Error has occurred in sending data to paypal');

	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		
		if (strcmp ($res, "VERIFIED") == 0) {
		// check the payment_satus is Completed
			mail('timothy.t.wong@gmail.com', 'error', 'payment_status = ' . $payment_status);
			if (strcmp($payment_status, 'Completed') == 0){
				
				// - MARK GRADLIST AS PAID -
	            $query  = 'INSERT INTO tbl_gradlist_paid ';
	            $query .= '     (user_id, payer_email, gradlist_id, amount) ';
	            $query .= 'VALUES ';
	            $query .= "     (0, '$payer_email', $item_number, $payment_amount)";
		
	            mysql_query($query) or mail('timothy.t.wong@gmail.com', 'error', 'error inserting payment <br> ' . mysql_error());
			}
		}
	}*/
	
	fclose($fp);
}
?>