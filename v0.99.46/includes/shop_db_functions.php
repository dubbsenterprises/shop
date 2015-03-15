<?php
function dbconnect() {
	$mc = @mysql_connect($_SESSION['MYSQL_HOST'], $_SESSION['MYSQL_USER'], $_SESSION['MYSQL_PASS']) or die(mysql_error());
	@mysql_select_db($_SESSION['MYSQL_DATABASE'], $mc);
}
function update_db_version() {
	if (isset($_SESSION['current_db_version'])) {
		$v = $_SESSION['current_db_version'];

		if ($v == 0) {
		}

		unset($_SESSION['current_db_version']);
	}
}
function get($var_name = '', $select, $from, $where, $sub_var_name = '', $sub_select = '', $sub_from = '', $sub_where = '') {
	if ($var_name != '' && $select != '' && $from != '') {
		$result = select_db($select, $from, $where);
		unset($_SESSION['results'][$var_name]);

		while ($result_array = mysql_fetch_array($result)) {
			if ($result_array['id'] != '') {
				foreach (array_keys($result_array) as $key) {
					if (!is_numeric($key) && $key != 'id') {
						$_SESSION['results'][$var_name][$result_array['id']][$key] = $result_array[$key];
					}
				}
			}
		}

		mysql_free_result($result);

		if (is_array($_SESSION['results'][$var_name]) && count($_SESSION['results'][$var_name]) > 0 && $sub_var_name != '' && $sub_select != '' && $sub_from != '') {
			$result = select_db($sub_select, $sub_from, str_replace('[main_ids]', implode(', ', array_keys($_SESSION['results'][$var_name])), $sub_where));

			while ($result_array = mysql_fetch_array($result)) {
				if ($result_array['id'] != '' && $result_array['main_id'] != '') {
					foreach (array_keys($result_array) as $key) {
						if (!is_numeric($key) && $key != 'id' && $key != 'main_id') {
							$_SESSION['results'][$var_name][$result_array['main_id']][$sub_var_name][$result_array['id']][$key] = $result_array[$key];
						}
					}
				}
			}

			mysql_free_result($result);
		}
	}
}
function select_db($columns, $from = '', $where = '', $all = 0) {
	$sqlstr = sprintf('select %s%s%s', $columns, $from != '' ? " from $from" : '', $where != '' ? " where $where" : '');
	if ($all == 1) {
		$sqlstr = preg_replace('/\bitems\b/', ' items_all ', $sqlstr);
		$sqlstr = preg_replace('/\bsuppliers\b/', ' suppliers_all ', $sqlstr);
		$sqlstr = preg_replace('/\bbrands\b/', ' brands_all ', $sqlstr);
		$sqlstr = preg_replace('/\bdepartments\b/', ' departments_all ', $sqlstr);
	}
	if (isset($_SESSION['settings']['user']) && $_SESSION['settings']['user'] == 'admin' && isset($_GET['showsql']) && $_GET['showsql'] == 1 && "$from$where" != '') {
		print "$sqlstr<br/>\n<br>";
	}
	$result = mysql_query($sqlstr) or die("MYSQL ERROR!<br/><br/>QUERY WAS: $sqlstr<br/>ERROR: " . mysql_error());
	return $result;
}
function insert_db($type, $set) {
	if (!(is_array($set) && is_string($type) && strlen($type) > 0)) { return; }
	if (substr($type, -1) != 's') { $table = $type . 's'; } else { $table = $type; }
	if (substr($table, -2) == "ys") { $table = substr($table, 0, strlen($table) - 2) . "ies"; }
	$add = array();
	if ($set['old_id'] > 0) {
		$sqlstr = sprintf('select * from %s where id = %s', $table, quote_smart($set['old_id']));
		$result = mysql_query($sqlstr) or die("MYSQL ERROR!<br/><br/>QUERY WAS: $sqlstr<br/>ERROR: " . mysql_error());
		if ($result_array = mysql_fetch_array($result)) {
			foreach (array_keys($result_array) as $key) {
				$add[$key] = $result_array[$key] === null ? 'null' : $result_array[$key];
			}
		} else {
			return false;
		}
		unset($set['old_id']);
		unset($add['id']);
		unset($add['deleted']);
	}
	foreach (array_keys($set) as $key) {
		if (!is_numeric($key)) { $add[$key] = $set[$key]; }
	}
	$left = $right = '';
	foreach (array_keys($add) as $key) {
		if (!is_numeric($key)) {
			if ($left != '') { $left .= ','; $right .= ','; }
			$left .= $key;
			if ($key == 'added' && $add[$key] == 0) {
				$right .= 'utc_timestamp()';
			} else {
				if (substr($add[$key], 0, 1) == '[' && substr($add[$key], -1) == ']') {
					$right .= substr($add[$key], 1, strlen($add[$key]) - 2);
				} else {
					$right .= quote_smart($add[$key]);
				}
			}
		}
	}
	if ($left != '') {
		$sqlstr = sprintf('insert into %s (%s) values (%s)', $table, $left, $right);

		if ($_SESSION['settings']['admin'] == 1 && $_GET['showsql'] == 1) {
			print "$sqlstr<br/>";
		}

		log_sql($sqlstr);
		mysql_query($sqlstr) or die("MYSQL ERROR!<br/><br/>QUERY WAS: $sqlstr<br/>ERROR: " . mysql_error());
		return mysql_insert_id();
	}
	return false;
}
function update_db($type, $set, $where) {
	if (!(is_string($type) && is_array($set) && is_string($where))) { return false; }
	if (strpos($type, ' ') > 0 || substr($type, -1) == 's') {
		$table = $type;
	} else {
		if (substr($table, -1) == "y") { $table = substr($table, 0, strlen($table) - 1) . "ie"; }
		$table = $type . 's';
	}
	$ws = '';
	if (strlen($where) > 0) { $ws = " where $where"; }
	$update = '';
	foreach (array_keys($set) as $key) {
		if (!is_numeric($key)) {
			if ($update != '') { $update .= ','; }
			if (substr($set[$key], 0, 1) == '[' && substr($set[$key], -1) == ']') {
				$value = substr($set[$key], 1, strlen($set[$key]) - 2);
			} else {
				$value = quote_smart($set[$key]);
			}
			$update .= sprintf('%s = %s', $key, $value);
		}
	}
	if ($update != '') {
		$sqlstr = sprintf('update %s set %s where %s', $table, $update, $where);

		if ($_SESSION['settings']['admin'] == 1 && $_GET['showsql'] == 1) {
			print "$sqlstr<br/>";
		}

		log_sql($sqlstr);
		mysql_query($sqlstr) or die("MYSQL ERROR!<br/><br/>QUERY WAS: $sqlstr<br/>ERROR: " . mysql_error());
		if (mysql_affected_rows() > 0) { return true; }
	}
	return false;
}
function delete_db($type, $where) {
	if (!(is_string($type) && is_string($where))) { return; }
	$table = $type . 's';
	if (substr($table, -2) == 'ys') { $table = substr($table, 0, strlen($table) - 2) . 'ies'; }
	$ws = '';
	if (strlen($where) > 0) { $ws = " where $where"; }
	$sqlstr = sprintf('update %s set deleted = utc_timestamp()%s', $table, $ws);

	if ($_SESSION['settings']['admin'] == 1 && $_GET['showsql'] == 1) {
		print "$sqlstr<br/>";
	}

	log_sql($sqlstr);
	mysql_query($sqlstr) or die("MYSQL ERROR!<br/><br/>QUERY WAS: $sqlstr<br/>ERROR: " . mysql_error());
	if (mysql_affected_rows() > 0) { return true; }

	return false;
}
?>