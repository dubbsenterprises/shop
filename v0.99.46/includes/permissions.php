<?php

function get_permissions() {
	if ($_SESSION['admin'] == 1) {
	}
	if ($_SESSION['manager'] == 1) {
	}
	$GLOBALS['permissions']['make_sales'] = 1;
	$GLOBALS['permissions']['sales_management'] = 0;
	$GLOBALS['permissions']['supplier_management'] = 0;
	$GLOBALS['permissions']['brands_management'] = 0;
	$GLOBALS['permissions']['items_management'] = 0;
	$GLOBALS['permissions']['delivery_management'] = 0;
}
