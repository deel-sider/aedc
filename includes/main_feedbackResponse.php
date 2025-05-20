<?php
if (preg_match("/^\w+\s\w+$/i",$_POST['nameVar'])
	&& (preg_match("/^(\w+)(@)(\w+)\.(\w+)$/i",$_POST['emailAddress']) || preg_match("/^[\w\.\-\_]+\@[\w\.\-\_]+$/i",$_POST['emailAddress']))
	&& (!empty($_POST['feedback'])) ) {
	echo "<p>Hello " . htmlentities($_POST['nameVar']) . ",</p>\n<p>Thanks for your message. If you need an answer from me, it will come via email. Otherwise, your comments will be invaluable for improving this website.</p>\n<p>Hope you enjoyed the site.</p>\n<p>John.</p>\n";
	$randno   = (mt_rand()/mt_rand())*mt_rand();
	$dateTime = getdate();
	$timevar  = $dateTime['hours'] . $dateTime["minutes"] . $dateTime["seconds"];
	$fileName = ROOT_LOC . "/feedback/feedback_" . $timevar . "_" . $randno . ".txt";
	$feedFile = fopen($fileName,"w+");
	$output   = implode($_POST,"\n");
	fputs($feedFile,$output);
	$to='johnthennessy@yahoo.co.uk';
	$subject="Feedback from assortedexplorations.com";
	$from_user=$_POST['nameVar'];
	$from_email=$_POST['emailAddress'];
	$headers = "From: $from_user <$from_email>\r\n";
	mail($to,$subject,$_POST['feedback'],$headers);
}
else {
echo <<< END1
<p>Whoops!</p>\n<p>You seem to have encountered an a problem. Here are some suggestions:</p>
<p>1. You may have not have completed a field in the form and all are needed.</p>
<p>2. You may have made your message too long.</p>
<p>3. You may have included a character that is not a number, a letter or a punctation mark.</p>
<p>Please, click <a href="javascript:history.go(-1);">here</a> to return to correct any of the above.</p>
<p>Thanks,</p>
<p>John.</p>
END1;
}
?>
