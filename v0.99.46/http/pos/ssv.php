<?

	session_start();
	#if ($_SESSION['settings']['admin'] != 1) { exit; }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> 
    <title>SSV</title>
    <script src="includes/clear-default-text.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="includes/pos.css"/>
    <link rel="stylesheet" type="text/css" href="includes/printpos.css" media="print"/>
  </head>
  <body>
    <table>
<?

	foreach (array_keys($_SESSION) as $key) {
		if (is_array($_SESSION[$key])) {
			foreach (array_keys($_SESSION[$key]) as $key2) {
				if (is_array($_SESSION[$key][$key2])) {
					foreach (array_keys($_SESSION[$key][$key2]) as $key3) {
						if (is_array($_SESSION[$key][$key2][$key3])) {
							foreach (array_keys($_SESSION[$key][$key2][$key3]) as $key4) {

?>
      <tr><td class='left s08 bold wp75'><?=$key?>['<?=$key2?>']['<?=$key3?>']['<?=$key4?>'] </td><td class='left s08 bold ml20'><?=$_SESSION[$key][$key2][$key3][$key4]?></td></tr>
<?

							}
						} else {

?>
      <tr><td class='left s08 bold  wp75'><?=$key?>['<?=$key2?>']['<?=$key3?>'] </td><td class='left s08 bold ml20'><?=$_SESSION[$key][$key2][$key3]?></td></tr>
<?

						}
					}
				} else {

?>
      <tr><td class='left s08 bold wp75'><?=$key?>['<?=$key2?>'] </td><td class='left s08 bold ml20'><?=$_SESSION[$key][$key2]?></td></tr>
<?

				}
			}
		} else {

?>
      <tr><td class='left s08 bold  wp75'><?=$key?></td><td class='left s08 bold ml20'><?=$_SESSION[$key]?></td></tr>
<?

		}
	}

?>
    </table>
  </body>
</html>
