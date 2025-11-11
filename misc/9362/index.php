<?php
if ($_GET) {
    $subjectv = $_GET['s'];
    $cu = $_GET['cu'];
    $version = $_GET['v'];
    $width = $_GET['w'];
    $height = $_GET['h'];
} else {
    $subjectv = "s1";
    $version = "e1";
    $width = "740";
    $height = "800";
}

$subjectversion = $subjectv . $version;

switch ($version) {
    case "e1":
        $subject = "[NAME], another feature of your ASDA membership";
		$preheader = "Life Insurance and Disability Insurance activated for you";
        break;
    case "e2":
        $subject = "[NAME], another benefit of your ASDA membership";
		$preheader = "Activate your Life Insurance and Disability Insurance with guaranteed acceptance";
        break;
    case "e3":
        $subject = "[NAME], another benefit of ASDA membership";
		$preheader = "Activate your Life Insurance and Disability Insurance with guaranteed approval";
        break;

    default:
        $subject = "";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="cache-control" content="no-cache">
<title>Email Viewer</title>
<style type="text/css">
* {
	font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
}
body {
	margin: 0px;
	padding: 0px;
}
#container {
	width: 1020px;
	margin-top: 20px;
}
#navigation {
	float: left;
	width: 200px;
	background-color: #FFFFFF;
	padding-top: 5px;
	padding-right: 0px;
	padding-bottom: 5px;
	padding-left: 10px;
}
#email  {
	-moz-border-radius: 10px;
	border-radius: 10px;
	padding: 20px;
	width: 760px;
	background-color: #BAD6EA;
	float: right;
}
#header {
	float: right;
	width: 760px;
}
.caps {
	text-transform: uppercase;
}
#device {
	margin-top: 15px;
	margin-bottom: 15px;
}
#header p {
	background-color: #FFF;
	padding: 5px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 10px;
	margin-left: 0px;
}
#content {
	clear: right;
	float: right;
	width: 760px;
}
.note{
	padding: 15px;
    background-color: #ffe200;
    clear: both;
    color: rgba(0,0,0,.7);
}
</style>
</head>

<body>
	<div id="container">
		<div id="navigation">
			<p><strong>ALL-9362 ASDA Emails</strong></p>
			<p><strong>Versions</strong></p>
			<ul>
			<?php
echo "<li><a href=index.php?s=s1&v=e1&w=" . $width . "&h=" . $height . ">E1 Auto-Enrolled in ASDA & Insurance Offer</a></li>";
echo "<li><a href=index.php?s=s1&v=e2&w=" . $width . "&h=" . $height . ">E2 Auto-Enrolled in ASDA but not Insurance Offer</a></li>";
echo "<li><a href=index.php?s=s1&v=e3&w=" . $width . "&h=" . $height . ">E3 Not Auto-Enrolled in ASDA or Insurance Offer</a></li>";
?>
			</ul>
		</div>
		<div id="email">
			<div id="header">
				<p>Version: <span class="caps"><strong><?php echo $version ?></strong></span>    </p>
				<p>From: [TBD]    </p>
				<p>To: [EMAIL ADDRESS]    </p>
				<p><?php echo "Subject: " . $subject ?></p>
  <p><?php echo "Preheader: " . $preheader ?></p>
			</div>
			<div id="content">
				<div id="device">
					<?php
echo "<a href=index.php?s=" . $subjectv . "&v=" . $version . "&cu=" . $cu . "&w=740&h=800>Desktop</a> | <a href=index.php?s=" . $subjectv . "&v=" . $version . "&cu=" . $cu . "&w=320&h=480>iPhone</a> | <a href=ada_misc_" . $version . ".html>Content Only</a>";
?>
				</div>
				<div>
					<?php
echo "<iframe width=" . $width . " height=" . $height . " src=ada_misc_" . $version . ".html></iframe>";
?>
				</div>
			</div>
		</div>
		<p style="clear:both;">&nbsp;</p>
	</div>
</body>
</html>