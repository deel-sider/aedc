<?php
require_once ROOT_LOC . "/includes/headers.php";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $tpiece; ?> &raquo; Assorted Explorations</title>
		<meta name="viewport" content="width=device-width,intial-scale=1.0" />
		<meta name="description" content="<?php echo $meta3; ?>" />
		<?php if (isset($mextras)) echo $mextras; ?>
        <link href="/bootstrap5/css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <link href="/css/main5.css" rel="stylesheet" media="screen" />
        <link href="/css/cc.css" rel="stylesheet" media="screen" />
		<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
		<link rel="manifest" href="/favicons/site.webmanifest">
		<link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="theme-color" content="#ffffff">
	</head>
	<body>
<?php
require_once ROOT_LOC . "/includes/navigation5.php";
?>
