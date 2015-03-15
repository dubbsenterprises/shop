<?php
require_once('shop_general_functions.php');

function processVariables() {
	$forward = 0;
	$subdomain = str_replace(".$GLOBALS[domain]", "", $_SERVER['HTTP_HOST']);
        $company_id_sql = "SELECT id, db_version from companies";
        
        $Shop_General_DAL = new GENERAL_DAL();
        $host_n_domain_info = $Shop_General_DAL->get_company_info_by_Host_or_Domain($_SESSION['settings']['subdomain'],$_SESSION['settings']['domain']);
        
	if (isset($host_n_domain_info) ) {
		$company_id = $_SESSION['settings']['company_id']   = $host_n_domain_info[0]->id;
		$_SESSION['current_db_version']                     = $host_n_domain_info[0]->db_version;
	} else {
                echo "processVariables() else";
		exit;
	}
	if (isset($_POST['post_values'])) {
		$posts = explode('|', $_POST['post_values']);
		foreach ($posts as $post) {
			$vars = explode('=', $post);
			if (count($vars) == 2) {
				$_POST[$vars[0]] = $vars[1];
			}
		}
	}
   
	if (isset($_POST['mainlogin']) && $_POST['mainlogin'] == 1) {
		#session_unset();
		$result = select_db('id, level, status', 'logins', sprintf('username = %s and password = md5(%s) and company_id = %s and deleted is null', quote_smart($_POST['login_name']), quote_smart($_POST['login_password']), quote_smart($company_id)));
		if ($result_array = mysql_fetch_array($result)) {
			$_SESSION['settings']['login_id'] = $result_array['id'];
                        $_SESSION['settings'][$result_array['id']]['level'] = $result_array['level'];
			$_SESSION['settings']['user'] = $_POST['login_name'];
        		if ($result_array['level'] > 0) {
				$_SESSION['settings']['AsstMgr'] = 1;
			}
			if ($result_array['level'] > 1) {
				$_SESSION['settings']['manager'] = 1;
			}
			if ($result_array['level'] > 2) {
				$_SESSION['settings']['admin'] = 1;
			}
                        if ($result_array['status'] == 0) {
                                session_unset();
                                $_GET = $_POST = array();
                                $_SESSION['settings']['badlogin'] = 1;
                                $_SESSION['settings']['badlogin_message'] = "Login is INACTIVE. Login Denied...";
                        }
		} else {
			session_unset();
			$_GET = $_POST = array();
			$_SESSION['settings']['badlogin'] = 1;
                        $_SESSION['settings']['badlogin_message'] = "LOGIN FAILED! Try again...";
                }
	} else {
		if (isset($_SESSION['settings']['login_id']) && mysql_num_rows(select_db('id', 'logins', sprintf('id = %s and company_id = %s and deleted is null', quote_smart($_SESSION['settings']['login_id']), quote_smart($company_id)))) == 0) {
			$_SESSION['settings']['action'] = 'logout';
		}

		if (isset($_SESSION['settings']['action']) && $_SESSION['settings']['action'] == 'logout') {
			session_unset();
			unset($_GET);
			unset($_POST);
			$forward = 1;
		}
	}
        
	if (isset($_SESSION['settings']['login_id'])) {
		get_permissions();
		update_db_version();

		if (isset($_POST['page_management'])) {
			if (isset($_POST['new_site'])) {
				foreach (array_keys($_SESSION['settings']) as $key) {
					if (substr($key, 0, strlen($_SESSION['settings']['site'])) == $_SESSION['settings']['site']) {
                                            unset($_SESSION['settings'][$key]);
					}
				}
				$_SESSION['settings']['site'] = $_POST['new_site'];
			}
		}

		if (isset($_POST['new_settings'])) {
			$settings = explode(',', $_POST['new_settings']);
			foreach ($settings as $setting) {
				$var = explode('=', $setting, 2);
				if ($var[0] != '') {
					if ($var[0] == 'site') {
						foreach (array_keys($_SESSION['settings']) as $key) {
							if (substr($key, 0, strlen($_SESSION['settings']['site'])) == $_SESSION['settings']['site']) {
								unset($_SESSION['settings'][$key]);
							}
						}
					}
					if (strpos($var[0], ':') > 0) {
						$var2 = explode(':', $var[0], 2);
						$_SESSION['settings'][$var2[0]][$var2[1]] = $var[1];
					} else {
						$_SESSION['settings'][$var[0]] = $var[1];
					}
				}
			}
		}

		if (isset($_POST['drop_settings'])) {
			$settings = explode(',', $_POST['drop_settings']);
			foreach ($settings as $setting) {
				if (strpos($setting, ':') > 0) {
					$parts = explode(':', $setting, 2);
					unset($_SESSION['settings'][$parts[0]][$parts[1]]);
				} else {
					unset($_SESSION['settings'][$setting]);
				}
			}
		}

		unset($_SESSION['preferences']);
		$preferences = array();
		$result = select_db('name, value', 'preferences', sprintf('company_id = %s', quote_smart($_SESSION['settings']['company_id'])));

		while ($result_array = mysql_fetch_array($result)) {
			$_SESSION['preferences'][preg_replace('/ /', '_', $result_array['name'])] = $result_array['value'];
		}

		if (isset($_POST['update_my_password']) && $_POST['update_my_password'] == 1) {
			if ($_POST['new_password'] != '') {
				update_db('logins', array('password' => '[md5(' . quote_smart($_POST['new_password']) . ')]'), sprintf('id = %s and company_id = %s and deleted is null', quote_smart($_SESSION['settings']['login_id']), quote_smart($_SESSION['settings']['company_id'])));
				$_SESSION['message'] = 'Your password was updated successfully.';
			} else {
				$_SESSION['bad']['password'] = 1;
				$_SESSION['message'] = 'ERROR: Your password was not updated since empty passwords are not allowed!';
			}
		}

		if (isset($_POST['new_sale']) && $_POST['new_sale'] == 1) {
			$sale = &$_SESSION['sale'];
			if ($_POST['modify_basket'] == 1) {
				if (isset($_POST['saleitemid'])) {
					if ($_POST['new_lastitemid'] > 0) { $sale['basket']['lastitemid'] = $_POST['new_lastitemid']; }
					if ($_POST['saleitemid'] == '+' || $_POST['saleitemid'] == '-') {
						if (isset($sale['basket']['items'][$sale['basket']['lastitemid']])) {
							if ($sale['basket']['lastitemid'] > 0) {
								if ($_POST['saleitemid'] == '+') {
									$total = $sale['basket']['items'][$sale['basket']['lastitemid']]['quantity'] += 1;
									$_SESSION['message'] = "One more of the item '" . $sale['basket']['items'][$sale['basket']['lastitemid']]['brand'] . " " . $sale['basket']['items'][$sale['basket']['lastitemid']]['name'] . "' was added to the current sale. The current quantity of this item for this sales is: $total";
								} else {
									if (is_array($sale['basket']['items'][$sale['basket']['lastitemid']]) && $sale['basket']['items'][$sale['basket']['lastitemid']]['quantity'] > 0) {
										$total = $sale['basket']['items'][$sale['basket']['lastitemid']]['quantity'] -= 1;
										if ($total == 0) {
											unset($sale['basket']['items'][$sale['basket']['lastitemid']]);
											$_SESSION['message'] = "You removed the last item of '" . $sale['basket']['items'][$sale['basket']['lastitemid']]['brand'] . " " . $sale['basket']['items'][$sale['basket']['lastitemid']]['name'] . "'.";
										} else {
											$_SESSION['message'] = "One of the item '" . $sale['basket']['items'][$sale['basket']['lastitemid']]['brand'] . " " . $sale['basket']['items'][$sale['basket']['lastitemid']]['name'] . "' was removed from the current sale. The current quantity of this item for this sales is: $total";
										}
									} else {
										$_SESSION['message'] = "One of the item '" . $sale['basket']['items'][$sale['basket']['lastitemid']]['brand'] . " " . $sale['basket']['items'][$sale['basket']['lastitemid']]['name'] . "' cannot be removed from the current sale since there is none left.";
									}
								}
							} else {
								$_SESSION['message'] = "This is a new sale. Using '+' and '-' does only work after the sale was started.";
							}
						} else {
							$_SESSION['message'] = 'ERROR: The last item is unknown...<br/>Either you do not have a last item or the last item was completely removed from this sale.';
						}
					} else {
						if ($_POST['saleitemid'] == "=") {
							$sale['encash'] = 1;
						} else if ($_POST['saleitemid'] > 0) {
							$result = select_db('i.id, i.barcode, i.number, i.attribute1, i.attribute2, i.price, i.discount, tg.tax, b.name as brand, i.name, i.archived, c.attribute1 as catt1, c.attribute2 as catt2', 'items as i join categories as c on c.id = i.category_id join tax_groups as tg on tg.id = i.tax_group_id left join brands as b on (b.id = i.brand_id and b.deleted is null)', sprintf('c.company_id = %s and c.type = %s and i.barcode = %s and coalesce(c.deleted, tg.deleted, i.deleted) is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['saleitemid'])));
							if ($result_array = mysql_fetch_array($result)) {
								if ($result_array['archived'] == 0) {
									$item = &$sale['basket']['items'][$result_array['id']];
									$item['quantity']++;
									$item['real_discount'] = $item['discount'] = $result_array['discount'];
									$item['real_price'] = $item['price'] = $result_array['price'];
									$item['rtax'] = $result_array['tax'] === null ? $_SESSION['preferences']['tax'] : $result_array['tax'];
									$item['tax'] = isset($sale['no_tax']) ? 0 : $item['rtax'];
									$item['brand'] = $result_array['brand'];
									$item['name'] = $result_array['name'];
									$item['attribute1'] = $result_array['attribute1'];
									$item['attributename1'] = $result_array['catt1'];
									$item['attribute2'] = $result_array['attribute2'];
									$item['attributename2'] = $result_array['catt2'];
									$item['barcode'] = $result_array['barcode'];
									$item['number'] = $result_array['number'];
									$sale['basket']['lastitemid'] = $item['id'] = $result_array['id'];
									$_SESSION['message'] = "The item '$result_array[brand] $result_array[name]' was added to the current sale.";
								} else {
									$_SESSION['message'] = "ERROR: The item '$result_array[brand] $result_array[name]' is an archived item. This means that it cannot be added to a sale.";
								}
							} else {
								$_SESSION['message'] = "ERROR: No item was found with matching barcode!";
							}
						} else {
							$_SESSION['message'] = 'ERROR: You did not send a real barcode.';
						}
					}
				}

				if (isset($_POST['gift_certificate_value'])) {
					if ($_POST['gift_certificate_value'] > 0) {
						$sale['basket']['gift_certificates'][$_POST['gift_certificate_value']][] = date('dymsHi');
						$_SESSION['message'] = "One gift certificate with value of '" . money($_POST['gift_certificate_value']) . "' was added to this sale.";
					} else {
						$_SESSION['message'] = 'ERROR: The gift certificate value must be a number and greater than 0.';
					}
				}

				if (isset($_POST['change_gift_certificate_value']) && $_POST['change_gift_certificate_value'] > 0) {
					if ($_POST['change_how_gift_certificate'] == '+') {
						$sale['basket']['gift_certificates'][$_POST['change_gift_certificate_value']][] = date('dymsHi');
					} else if ($_POST['change_how_gift_certificate'] == '-') {
						array_pop($sale['basket']['gift_certificates'][$_POST['change_gift_certificate_value']]);
					}
					$_SESSION['message'] = "Changed the count of gift certificates with value of '" . money($_POST['change_gift_certificate_value']) . "' to: " . count($sale['basket']['gift_certificates'][$_POST['change_gift_certificate_value']]);
					if (count($sale['basket']['gift_certificates'][$_POST['change_gift_certificate_value']]) == 0) {
						unset($sale['basket']['gift_certificates'][$_POST['change_gift_certificate_value']]);
					}
				}

				if (isset($_POST['remove_gift_certificate_value']) && $_POST['remove_gift_certificate_value'] > 0) {
					if (isset($sale['basket']['gift_certificates'][$_POST['remove_gift_certificate_value']])) {
						unset($sale['basket']['gift_certificates'][$_POST['remove_gift_certificate_value']]);
						$_SESSION['message'] = "All gift certificates with value of '" . money($_POST['remove_gift_certificate_value']) . "' were removed from this sale.";
					}
				}
			}

			if ($_POST['remove_item_from_basket'] == 1) {
				$_SESSION['message'] = "The item '" . $sale['basket']['items'][$_POST['removeitemid']]['brand'] . " " . $sale['basket']['items'][$_POST['removeitemid']]['name'] . "' was removed completely from the current sale.";
				unset($sale['basket']['items'][$_POST['removeitemid']]);
			}

			if ($_POST['set_item_discount'] == 1) {
				$discount = $_POST['itemdiscount'];
				if ($discount > 100) { $discount = 100; }
				if ($discount < 1 || !is_numeric($discount)) { $discount = 0; }
				$discount = number($discount);
				if (isset($sale['basket']['items'][$_POST['itemid']])) {
					$sale['basket']['items'][$_POST['itemid']]['additional_discount'] = $discount;
				}
			}

			if ($_POST['encash'] == 1) {
				$sale['encash'] = 1;
			}

			if ($_POST['no_encash'] == 1) {
				unset($sale['encash']);
				unset($sale['cash_payment']);
				unset($sale['card_payments']);
				unset($sale['change']);
				unset($sale['finish']);
			}

			if ($_POST['cancel_sale'] == 1) {
				if (isset($_SESSION['sale'])) {

					unset($_SESSION['sale']);
				}
			}

			if ($_POST['paid_cash'] == 1) {
				if (!is_numeric($_POST['paidamount'])) {
					$_SESSION['message'] = 'ERROR: The entered amount was not numeric.';
				} else {
					$sale['cash_payment'] += $_POST['paidamount'];
					$sale['check_finish'] = 1;
					$_SESSION['message'] = 'Added ' . money($_POST['paidamount']) . ' to the cash payment.';
				}
			}
                        
			if (isset($_SESSION['sale']['customer_id'])) {
                                    $sale['customer_id'] = $_SESSION['sale']['customer_id'];
				} else {
                                    $sale['customer_id'] = 0;
                        }
                        
			if ($_POST['card_payment_management'] == 1) {
				if ($_POST['add_card_payment'] == 1) {
					if (!is_numeric($_POST['paidamount'])) {
						$_SESSION['message'] = 'ERROR: The entered amount was not numeric.';
					} else {
						$result = select_db('name', 'card_types', sprintf('id = %s', quote_smart($_POST['cardtype'])));
						if ($result_array = mysql_fetch_array($result)) {
							$id = is_array($sale['card_payments']) ? count($sale['card_payments']) + 1 : 1;
							$sale['card_payments'][$id]['type'] = $_POST['cardtype'];
							$sale['card_payments'][$id]['name'] = $result_array['name'];
							$sale['card_payments'][$id]['amount'] = $_POST['paidamount'];
						}
						$sale['check_finish'] = 1;
						$_SESSION['message'] = 'One card payment was added.';
					}
				}

				if ($_POST['delete_card_payment'] == 1) {
					if ($_POST['delete_card_payment_id'] > 0 && isset($sale['card_payments'][$_POST['delete_card_payment_id']])) {
						unset($sale['card_payments'][$_POST['delete_card_payment_id']]);
						$_SESSION['message'] = 'The card payment was deleted.';
					}
				}
			}

			if ($_POST['voucher_payment_management'] == 1) {
				if ($_POST['add_voucher_payment'] == 1) {
					$result = select_db(sprintf('id, value, barcode, date_format(convert_tz(added, "utc", %s), %s) as issued', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y @ %H:%i:%S'"), 'vouchers', sprintf('company_id = %s and barcode = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['voucherbarcode'])));
					if ($result_array = mysql_fetch_array($result)) {
						if (isset($sale['voucher_payments'][$result_array['id']])) {
							$_SESSION['message'] = 'This voucher was already added before.';
						} else {
							$sale['voucher_payments'][$result_array['id']]['issued'] = $result_array['issued'];
							$sale['voucher_payments'][$result_array['id']]['barcode'] = $result_array['barcode'];
							$sale['voucher_payments'][$result_array['id']]['value'] = $result_array['value'];
							$sale['check_finish'] = 1;
							$_SESSION['message'] = 'One voucher payment was added.';
						}
					} else {
						$_SESSION['message'] = 'No valid voucher was found.';
					}
				}

				if ($_POST['delete_voucher_payment'] == 1) {
					if ($_POST['delete_voucher_payment_id'] > 0 && isset($sale['voucher_payments'][$_POST['delete_voucher_payment_id']])) {
						unset($sale['voucher_payments'][$_POST['delete_voucher_payment_id']]);
						$_SESSION['message'] = 'The voucher payment was deleted.';
					}
				}
			}

			if ($_POST['save_sale'] == 1) {
				if (!isset($sale['receipt_id']) && $_SESSION['settings']['site'] == 'sales') {
					save_sale($sale);

					$sale['receipt_date'] = currentTimestamp();

					if ($_POST['sales_person_id'] > 0) {
						$result = select_db('username', 'logins', sprintf('id = %s and company_id = %s and deleted is null', quote_smart($_POST['sales_person_id']), quote_smart($_SESSION['settings']['company_id'])));

						if ($result_array = mysql_fetch_array($result)) {
							$sale['clerk'] = $result_array['username'];
						}
					}

					if ($sale['clerk'] == '') {
 						$sale['clerk'] = $_SESSION['settings']['user'];
					}
				}

				$_SESSION['show_receipt'] = 1;
			}
		}

		if (isset($_SESSION['settings']['manager']) && $_SESSION['settings']['manager'] == 1) {
			if ($_SESSION['keep_debug'] == 1) {
				unset($_SESSION['keep_debug']);
			} else {
				unset($_SESSION['debug']);
			}

			if (isset($_GET['debug'])) {
				if ($_GET['debug'] == 1) {
					$_SESSION['debug_active'] = 1;
				} else {
					unset($_SESSION['debug_active']);
				}
			}

			if ($_SESSION['debug_active'] == 1) {
				foreach (array_keys($_POST) as $key) {
					$_SESSION['debug']['post'][$key] = $_POST[$key];
				}
			}

			if (isset($_POST['no_management_type']) && $_POST['no_management_type'] == 1) {
				if (isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) {
					unset($_SESSION['settings']['itemmgnt']['style_edit_item_id']);
				} else if (isset($_SESSION['settings']['itemmgnt']['category_id']) || isset($_SESSION['settings']['itemmgnt']['department_id'])) {
					if (isset($_SESSION['settings']['itemmgnt']['category_id'])) { unset($_SESSION['settings']['itemmgnt']['category_id']); }
					if (isset($_SESSION['settings']['itemmgnt']['department_id'])) { unset($_SESSION['settings']['itemmgnt']['department_id']); }
					if (isset($_SESSION['settings']['itemmgnt']['brand_id'])) { unset($_SESSION['settings']['itemmgnt']['brand_id']); }
				} else {
					unset($_SESSION['settings']['itemmgnt']['type']);
				}
			}

			if ($_POST['delivery_management'] == 1) {
				foreach (array_keys($_POST) as $key) {
					if (substr($key, 0, 9) == 'delivery_') {
						if (substr($key, 9, 4) == 'add_') {
							$_SESSION['delivery']['add'][substr($key, 13)] = $_POST[$key];
						} else if (substr($key, 9, 7) == 'update_') {
							$_SESSION['delivery']['update'][substr($key, 16)] = $_POST[$key];
						} else if (substr($key, 9, 7) == 'delete_') {
							$_SESSION['delivery']['delete'][substr($key, 16)] = $_POST[$key];
						} else {
							$_SESSION['delivery'][substr($key, 9)] = $_POST[$key];
						}
					}
				}

				if ((!isset($_SESSION['settings']['itemmgnt']['delivery_id']) && (isset($_SESSION['delivery']['supplier_id']) && mysql_num_rows(select_db('id', 'suppliers', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['delivery']['supplier_id'])))) == 0) || (isset($_POST['cancel_delivery']) && $_POST['cancel_delivery'] == 1))) {
					if (isset($_SESSION['delivery'])) {
						unset($_SESSION['delivery']);
					}
					if (isset($_SESSION['settings']['itemmgnt']['delivery_id'])) {
						unset($_SESSION['settings']['itemmgnt']['delivery_id']);
					}
				} else {
					if ($_POST['form_action'] == 'add_delivery_item') {
						$item = $_SESSION['delivery']['add'];
						$check = array('item_id', 'quantity', 'buy_price', 'sell_price');
						foreach ($check as $name) {
							if (!($item[$name] > 0)) {
								$_SESSION['bad'][$name] = 1;
							}
						}
						if (isset($_SESSION['bad'])) {
							$_SESSION['message'] = "ERROR: Delivery item could not be added since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.";
						} else {
							$result = select_db('concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info', 'items as i join brands as b on b.id = i.brand_id join categories as c on c.id = i.category_id', sprintf('i.id = %s and c.company_id = %s and coalesce(i.deleted, b.deleted, c.deleted) is null', quote_smart($item['item_id']), quote_smart($_SESSION['settings']['company_id'])));
							if ($result_array = mysql_fetch_array($result)) {
								$_SESSION['delivery']['items'][$item['item_id']]['info'] = $result_array['info'];
								foreach (array_keys($_SESSION['delivery']['add']) as $key) {
									$_SESSION['delivery']['items'][$item['item_id']][$key] = $_SESSION['delivery']['add'][$key];
								}
								unset($_SESSION['delivery']['add']);
								$_SESSION['message'] = "Delivery item was added successfully.";
							} else {
								$_SESSION['message'] = 'ERROR: The delivery item could not be added since it is not valid anymore!';
							}
						}
					}

					if ($_POST['form_action'] == 'edit_delivery_item') {
						if (isset($_SESSION['delivery']['item_id']) && isset($_SESSION['delivery']['items'][$_SESSION['delivery']['item_id']])) {
							$_SESSION['delivery']['update']['item_id'] = $_SESSION['settings']['itemmgnt']['deliveries_item_id'] = $_SESSION['delivery']['item_id'];

							foreach (array('quantity', 'buy_price', 'sell_price') as $name) {
								$_SESSION['delivery']['update'][$name] = $_SESSION['delivery']['items'][$_SESSION['delivery']['item_id']][$name];
							}
						}
					}

					if ($_POST['form_action'] == 'update_delivery_item') {
						if (isset($_SESSION['delivery']['update'])) {
							$update = $_SESSION['delivery']['update'];
							foreach (array('item_id', 'quantity', 'buy_price', 'sell_price') as $name) {
								if (!($_SESSION['delivery']['update'][$name] > 0)) {
									$_SESSION['bad'][$name] = 1;
								}
							}

							if (isset($_SESSION['bad'])) {
								$_SESSION['message'] = "ERROR: Delivery item could not be updated since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.";
							} else {
								if ($result_array = mysql_fetch_array(select_db('concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info', 'items as i join brands as b on b.id = i.brand_id join categories as c', sprintf('i.id = %s and c.company_id = %s and coalesce(i.deleted, b.deleted, c.deleted) is null', quote_smart($_SESSION['delivery']['update']['item_id']), quote_smart($_SESSION['settings']['company_id']))))) {
									unset($_SESSION['delivery']['items'][$_SESSION['delivery']['item_id']]);
									$_SESSION['delivery']['items'][$_SESSION['delivery']['update']['item_id']]['info'] = $result_array['info'];
									foreach (array_keys($_SESSION['delivery']['update']) as $key) {
										if ($key != 'item_id') {
											$_SESSION['delivery']['items'][$_SESSION['delivery']['update']['item_id']][$key] = $_SESSION['delivery']['update'][$key];
										}
									}

									unset($_SESSION['settings']['itemmgnt']['deliveries_item_id']);
									$_SESSION['message'] = "Delivery item was updated successfully.";
								} else {
									$_SESSION['bad']['update_item_id'] = 1;
									$_SESSION['message'] = "ERROR: Delivery item could not be updated since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.";
								}
							}
						}
					}

					if ($_POST['form_action'] == 'cancel_edit_delivery_item') {
						unset($_SESSION['settings']['itemmgnt']['deliveries_item_id']);
					}

					if ($_POST['form_action'] == 'delete_delivery_item') {
						if ($_SESSION['delivery']['item_id'] > 0 && isset($_SESSION['delivery']['items'][$_SESSION['delivery']['item_id']])) {
							unset($_SESSION['delivery']['items'][$_SESSION['delivery']['item_id']]);
						}
					}

					if ($_POST['form_action'] == 'add_delivery') {
						foreach (array('ordered', 'sent', 'received') as $name) {
							$checkdate = explode('-', $_SESSION['delivery'][$name], 3);
							if (!checkdate($checkdate[1], $checkdate[2], $checkdate[0])) {
								$_SESSION['bad'][$name] = 1;
							}
						}

						foreach (array('invoice_no', 'delivered_via', 'shipping_costs', 'purchase_order_no') as $name) {
							if (empty($_SESSION['delivery'][$name])) {
								$_SESSION['bad'][$name] = 1;
							}
						}

						if (!($_SESSION['delivery']['receiver_id'] > 0)) {
							$_SESSION['bad']['receiver_id'] = 1;
						}

						if (isset($_SESSION['bad'])) {
							$_SESSION['message'] = "ERROR: Delivery could not be added since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.";
						} else {
							$delivery_id = insert_db('deliveries', array('supplier_id' => $_SESSION['delivery']['supplier_id'], 'ordered' => $_SESSION['delivery']['ordered'], 'invoice_no' => $_SESSION['delivery']['invoice_no'], 'shipped' => $_SESSION['delivery']['sent'], 'delivered_via' => $_SESSION['delivery']['delivered_via'], 'shipping_costs' => $_SESSION['delivery']['shipping_costs'], 'received' => $_SESSION['delivery']['received'], 'receiver_id' => $_SESSION['delivery']['receiver_id'], 'purchase_order_no' => $_SESSION['delivery']['purchase_order_no'], 'added' => 0));

							foreach (array_keys($_SESSION['delivery']['items']) as $item_id) {
								$item = $_SESSION['delivery']['items'][$item_id];
								insert_db('delivery_items', array('delivery_id' => $delivery_id, 'item_id' => $item_id, 'buy_price' => $item['buy_price'], 'sell_price' => $item['sell_price'], 'quantity' => $item['quantity']));
								update_db('items', array('quantity' => sprintf('[quantity + %s]', quote_smart($item['quantity'])), 'buy_price' => $item['buy_price'], 'price' => $item['sell_price']), sprintf('id = %s', quote_smart($item_id)));
							}

							unset($_SESSION['delivery']);
							$_SESSION['settings']['itemmgnt']['delivery_id'] = $delivery_id;
							$_SESSION['message'] = 'You successfully added a delivery.';
						}
					}

					if ($_POST['form_action'] == 'show_delivery') {
						if ($_SESSION['delivery']['id'] > 0) {
							$_SESSION['settings']['itemmgnt']['delivery_id'] = $_SESSION['delivery']['id'];
						}
					}

					if ($_POST['form_action'] == 'add_item') {
						$_SESSION['settings']['itemmgnt']['add_item'] = 1;
					}
				}
			}

			if ($_POST['supplier_management'] == 1) {
				if (!(isset($_POST['update_supplier_id']) || isset($_POST['delete_supplier_id']) || isset($_POST['edit_supplier_id']))) {
					if ($_POST['new_supplier_name'] == '') {
						foreach(array_keys($_POST) as $key) {
							if (substr($key, 0, 4) == 'new_') { $_SESSION['wronginput']['supplier'][$key] = $_POST[$key]; }
						}
						$_SESSION['message'] = "Supplier could not be added since the supplier name is blank!";
					} else {
						insert_db('supplier', array('company_id' => $_SESSION['settings']['company_id'], 'name' => $_POST['new_supplier_name'], 'address' => $_POST['new_address'], 'phone' => $_POST['new_phone'], 'contact' => $_POST['new_contact'], 'email' => $_POST['new_email'], 'added' => 0));
						unset($_SESSION['wronginput']['supplier']);
						$_SESSION['message'] = "Supplier was added.";
					}
				}

				if (isset($_POST['edit_supplier_id'])) {
					unset($_SESSION['wronginput']['supplier']);
					if ($_POST['edit_supplier_id'] > 0) {
						$_SESSION['settings']['itemmgnt']['sid'] = $_POST['edit_supplier_id'];
					} else {
						unset($_SESSION['settings']['itemmgnt']['sid']);
					}
				}

				if ($_POST['update_supplier_id'] > 0) {
					if (delete_db('supplier', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_supplier_id'])))) {
						$_SESSION['settings']['itemmgnt']['sid'] = insert_db('supplier', array('old_id' => $_POST['update_supplier_id'], 'name' => $_POST['new_supplier_name'], 'address' => $_POST['new_address'], 'phone' => $_POST['new_phone'], 'contact' => $_POST['new_contact'], 'email' => $_POST['new_email'], 'added' => 0));
						update_db('items as i join categories as c on i.category_id = c.id', array('supplier_id' => $_SESSION['settings']['itemmgnt']['sid']), sprintf('c.company_id = %s and i.supplier_id = %s and c.deleted is null and i.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_supplier_id'])));
						if (isset($_SESSION['delivery']['supplier_id']) && $_SESSION['delivery']['supplier_id'] == $_POST['update_supplier_id']) {
							$_SESSION['delivery']['supplier_id'] = $_SESSION['settings']['itemmgnt']['sid'];
						}

						$_SESSION['message'] = 'supplier was updated.';
						unset($_SESSION['settings']['itemmgnt']['sid']);
					}
				}

				if ($_POST['delete_supplier_id'] > 0) {
					if (delete_db('supplier', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['delete_supplier_id'])))) {
						$_SESSION['message'] = 'supplier was deleted.';
					}
				}
			}

			if ($_POST['brand_management'] == 1) {
				if (!(isset($_POST['update_brand_id']) || isset($_POST['delete_brand_id']))) {
					insert_db('brand', array('company_id' => $_SESSION['settings']['company_id'], 'name' => $_POST['new_brand_name'], 'added' => 0));
					$_SESSION['message'] = 'brand was added.';
				}

				if ($_POST['update_brand_id'] > 0) {
					if (delete_db('brand', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_brand_id'])))) {
						$bid = insert_db('brand', array('old_id' => $_POST['update_brand_id'], 'name' => $_POST['new_brand_name'], 'added' => 0));
						update_db('items as i join categories as c on i.category_id = c.id', array('i.brand_id' => $bid), sprintf('c.company_id = %s and i.brand_id = %s and c.deleted is null and i.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_brand_id'])));
						$_SESSION['message'] = 'brand was updated.';
					}
				}

				if ($_POST['delete_brand_id'] > 0) {
					if (delete_db('brand', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['delete_brand_id'])))) {
						$_SESSION['message'] = 'brand was deleted.';
					}
				}
			}

			if ($_POST['department_management'] == 1) {
				if (!(isset($_POST['update_department_id']) || isset($_POST['delete_department_id']))) {
					insert_db('department', array('company_id' => $_SESSION['settings']['company_id'], 'name' => $_POST['new_name'], 'location' => $_POST['new_location'], 'contact' => $_POST['new_contact'], 'phone' => $_POST['new_phone'], 'added' => 0));
					$_SESSION['message'] = 'department was added.';
				}

				if ($_POST['update_department_id'] > 0) {
					if (delete_db('department', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_department_id'])))) {
						$bid = insert_db('department', array('old_id' => $_POST['update_department_id'], 'name' => $_POST['new_name'], 'location' => $_POST['new_location'], 'contact' => $_POST['new_contact'], 'phone' => $_POST['new_phone'], 'added' => 0));
						update_db('items as i join categories as c on i.category_id = c.id', array('i.department_id' => $bid), sprintf('c.company_id = %s and i.department_id = %s and c.deleted is null and i.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_department_id'])));
						$_SESSION['message'] = 'department was updated.';
					}
				}

				if ($_POST['delete_department_id'] > 0) {
					if (delete_db('department', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['delete_department_id'])))) {
						$_SESSION['message'] = 'department was deleted.';
					}
				}
			}

			if ($_POST['category_management'] == 1) {
				if (!(isset($_POST['update_category_id']) || isset($_POST['delete_category_id']))) {
					insert_db('category', array('company_id' => $_SESSION['settings']['company_id'], 'type' => $_SESSION['settings']['pagetype'], 'name' => $_POST['new_itemcategory_name'], 'attribute1' => $_POST['new_attribute1'], 'attribute2' => $_POST['new_attribute2'], 'added' => 0));
					$_SESSION['message'] = 'item category was added.';
				}

				if ($_POST['update_category_id'] > 0) {
					if (delete_db('category', sprintf('company_id = %s and type = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['update_category_id'])))) {
						$cid = insert_db('category', array('old_id' => $_POST['update_category_id'], 'name' => $_POST['new_itemcategory_name'], 'attribute1' => $_POST['new_attribute1'], 'attribute2' => $_POST['new_attribute2']));
						update_db('items as i join categories as c on i.category_id = c.id', array('i.category_id' => $cid), sprintf('c.company_id = %s and i.category_id = %s and c.deleted is not null and i.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_category_id'])));
						$_SESSION['message'] = 'item category was updated.';
					}
				}

				if ($_POST['delete_category_id'] > 0) {
					if (delete_db('category', sprintf('company_id = %s and type = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['delete_category_id'])))) {
						$_SESSION['message'] = 'item category was deleted.';
					}
				}
			}

			if ($_POST['taxgroup_management'] == 1) {
				if (!(isset($_POST['update_taxgroup_id']) || isset($_POST['delete_taxgroup_id']))) {
					insert_db('tax_group', array('company_id' => $_SESSION['settings']['company_id'], 'name' => $_POST['new_taxgroup_name'], 'tax' => $_POST['new_tax'], 'added' => 0));
					$_SESSION['message'] = 'tax group was added.';
				}

				if ($_POST['update_taxgroup_id'] > 0) {
					if (delete_db('tax_group', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_taxgroup_id'])))) {
						$tgid = insert_db('tax_group', array('old_id' => $_POST['update_taxgroup_id'], 'name' => $_POST['new_taxgroup_name'], 'tax' => $_POST['new_tax']));
						update_db('items as i join tax_groups as tg on tg.id = i.tax_group_id', array('i.tax_group_id' => $tgid), sprintf('tg.company_id = %s and i.tax_group_id = %s and tg.deleted is not null and i.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_taxgroup_id'])));
						$_SESSION['message'] = 'tax group was updated.';
					}
				}

				if ($_POST['delete_taxgroup_id'] > 0) {
					if (delete_db('tax_group', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['delete_taxgroup_id'])))) {
						$_SESSION['message'] = 'tax group was deleted.';
					}
				}
			}

			if ($_POST['sale_management'] == 1) {
				if (isset($_POST['sale_tax'])) {
                                    if ($_POST['sale_tax'] == 1) {
                                        unset($_SESSION['sale']['no_tax']);
                                        if (is_array($_SESSION['sale']['basket']['items'])) {
                                            foreach (array_keys($_SESSION['sale']['basket']['items']) as $item_id) {
                                                $item = &$_SESSION['sale']['basket']['items'][$item_id];
                                                $item['tax'] = $item['rtax'];
                                            }
                                        }
                                        $_SESSION['message'] = 'Tax for this sale was turned on.';
                                    } else {
                                        $_SESSION['sale']['no_tax'] = 1;
                                        if (is_array($_SESSION['sale']['basket']['items'])) {
                                                foreach (array_keys($_SESSION['sale']['basket']['items']) as $item_id) {
                                                        $item = &$_SESSION['sale']['basket']['items'][$item_id];
                                                        $item['tax'] = 0;
                                                }
                                        }
                                        $_SESSION['message'] = 'Tax for this sale was turned off.';
                                    }
				}

				if ($_POST['update_item_price'] == 1) {
					if (is_array($item = &$_SESSION['sale']['basket']['items'][$_POST['item_id']])) {
						$vals = calc($item['price'], $item['discount']);
						if ($vals['price'] - $vals['discount'] != $_POST['new_price']) {
							if (is_numeric($_POST['new_price'])) {
								$item['discount'] = 0;
								$item['price'] = $_POST['new_price'];

								$_SESSION['message'] = 'You updated an item price for this sale. But remember that this is for this sale only!';
							} else {
								$_SESSION['message'] = 'The item price was not updated since the new price value is not a valid number.';
							}
						} else {
							$_SESSION['message'] = 'The item price was not updated since the new price has the same value.';
						}
					}
				}

				if ($_POST['receiptshow'] == 1 || $_POST['show_details'] == 1) {
					if ($_POST['third'] == 1) {
						unset($_SESSION['sale3']);
						unset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id2']);
						$sale = &$_SESSION['sale3'];
					} else {
						unset($_SESSION['sale2']);
						$sale = &$_SESSION['sale2'];
					}

					$result = select_db(sprintf('s.id, coalesce(l2.username, l.username, "-") as clerk, convert_tz(s.added, "utc", %s) as added, s.receipt_id, s.taxed, s.paid', quote_smart($_SESSION['preferences']['timezone'])), 'sales as s left join logins as l on l.id = s.login_id left join logins as l2 on l2.id = s.sales_person_id', sprintf('s.company_id = %s and s.id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['saleid'])));
					#print sprintf('s.id, coalesce(l2.username, l.username, "-") as clerk, convert_tz(s.added, "utc", %s) as added, s.receipt_id, s.taxed, s.paid', quote_smart($_SESSION['preferences']['timezone']));

					if ($result_array = mysql_fetch_array($result)) {
						$totalprice = $totaldiscount = $totaltax = 0;
						$sale['clerk'] = $result_array['clerk'];
						$sale['receipt_date'] = $result_array['added'];
						$sale['receipt_id'] = $result_array['receipt_id'];

						if ($result_array['taxed'] != 1) {
							$sale['no_tax'] = 1;
						}

						$sale['cash_payment'] = $result_array['paid'];

						$result2 = select_db('text', 'sale_notes', sprintf('sale_id = %s', quote_smart($result_array['id'])));

						if ($result_array2 = mysql_fetch_array($result2)) {
							$sale['note'] = $result_array2['text'];
						}

						$result2 = select_db('si.item_id, i.name, i.price as real_price, i.discount as real_discount, si.quantity, si.price, si.tax, si.discount, si.additional_discount, i.attribute1, i.attribute2, i.number, i.barcode, c.attribute1 as attributename1, c.attribute2 as attributename2', 'sale_items as si left join items as i on si.item_id = i.id join categories as c on c.id = i.category_id', sprintf('si.sale_id = %s', quote_smart($_POST['saleid'])));

						while ($result_array2 = mysql_fetch_array($result2)) {
							foreach (array_keys($result_array2) as $key) {
								if (!is_numeric($key) && $key != 'item_id') {
									$sale['basket']['items'][$result_array2['item_id']][$key] = $result_array2[$key];
								}
							}

							$vals = calc($result_array2['price'], $result_array2['discount'], $result_array2['additional_discount'], $result_array2['tax'], $result_array2['quantity']);

							$totalprice += $vals['price'];
							$totaldiscount += $vals['discount'];
							$totaltax += $vals['tax'];
						}

						$result2 = select_db('value, barcode', 'vouchers', sprintf('company_id = %s and type = "gift_certificate" and origin_id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['saleid'])));
						while ($result_array2 = mysql_fetch_array($result2)) {
							$sale['basket']['gift_certificates'][$result_array2['value']][] = $result_array2['barcode'];
							$totalprice += $result_array2['value'];
						}

						$result2 = select_db('cp.id, cp.amount, ct.name', 'card_payments as cp join card_types as ct on ct.id = cp.card_type_id', sprintf('cp.sale_id = %s', quote_smart($_POST['saleid'])));
						$ctotal = 0;
						while ($result_array2 = mysql_fetch_array($result2)) {
							$sale['card_payments'][$result_array2['id']]['name'] = $result_array2['name'];
							$sale['card_payments'][$result_array2['id']]['amount'] = $result_array2['amount'];
							$ctotal += $result_array2['amount'];
						}

						$result2 = select_db(sprintf('id, value, barcode, date_format(convert_tz(added, "utc", %s), %s) as issued', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y @ %H:%i:%S'"), 'vouchers', sprintf('company_id = %s and sale_id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['saleid'])));
						$vtotal = 0;
						while ($result_array2 = mysql_fetch_array($result2)) {
							$sale['voucher_payments'][$result_array2['id']]['issued'] = $result_array2['issued'];
							$sale['voucher_payments'][$result_array2['id']]['barcode'] = $result_array2['barcode'];
							$sale['voucher_payments'][$result_array2['id']]['value'] = $result_array2['value'];
							$vtotal += $result_array2['value'];
						}

						$payinfo = pay($totalprice - $totaldiscount + $totaltax, $sale['cash_payment'], $ctotal, $vtotal);
						$sale['change']['cash'] = $payinfo['cash'];
						$sale['change']['voucher'] = $payinfo['voucher'];
						$sale['totals']['price'] = $totalprice;
						$sale['totals']['discount'] = $totaldiscount;
						$sale['totals']['tax'] = $totaltax;

						if ($_POST['receiptshow'] == 1) {
							$_SESSION['show_receipt'] = 1;
 						}

						if ($_POST['show_details'] == 1) {
							$_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id'] = $sale['receipt_id'];
						}
					}
				}

				if ($_POST['delete_sale_id'] > 0) {
					return_items($_POST['delete_sale_id']);
					delete_db('sale', sprintf('id = %s', quote_smart($_POST['delete_sale_id'])));
					$_SESSION['message'] = 'The selected sale was deleted.';
				}
			}
		}

		if ($_SESSION['settings']['admin'] == 1) {
			if ($_GET['fix'] == 1) {
				$result = select_db('r.id, s.id as sale_id, r.barcode, r.added', 'sales as s join returns as r on r.company_id = s.company_id and r.barcode = s.receipt_id left join vouchers as v on v.origin_id = r.id and v.type="return"', sprintf('s.company_id = %s and v.id is null', quote_smart($_SESSION['settings']['company_id'])));

				$done = 0;

				while ($result_array = mysql_fetch_array($result)) {
					$value = 0;
					$result2 = select_db('si.price, si.discount, si.additional_discount, si.tax, ri.quantity', 'return_items as ri join sale_items as si on si.id = ri.sale_item_id join items as i on i.id = si.item_id', sprintf('ri.return_id = %s', quote_smart($result_array['id'])));

					while ($result_array2 = mysql_fetch_array($result2)) {
						$vals = calc($result_array2['price'], $result_array2['discount'], $result_array2['additional_discount'], $result_array2['tax'], $result_array2['quantity']);
						$value += $vals['total'];
					}

					if ($value > 0) {
						insert_db('voucher', array('company_id' => $_SESSION['settings']['company_id'], 'origin_id' => $result_array['id'], 'sale_id' => $result_array['sale_id'], 'barcode' => $result_array['barcode'], 'type' => 'return', 'value' => $total, 'added' => $result_array['added']));
						$done++;
					}
				}

				print('Fixed ' . $done . ' exchanges...<br/>');

				$result = select_db('s.id, s.cardtype_id, if(s.paid = 0, s.total + s.tax - s.discount, s.paid) as paid', 'sales as s join card_types as ct on ct.id = s.cardtype_id left join card_payments as cp on cp.sale_id = s.id', sprintf('s.company_id = %s and s.paymode like "card" and cp.id is null', quote_smart($_SESSION['settings']['company_id'])));

				while ($result_array = mysql_fetch_array($result)) {
					$paid = $result_array['paid'];
					$result2 = select_db('value', 'vouchers', sprintf('sale_id = %s', $result_array['id']));

					while ($result_array2 = mysql_fetch_array($result2)) {
						$paid -= $result_array2['value'];
					}

					insert_db('card_payment', array('sale_id' => $result_array['id'], 'card_type_id' => $result_array['card_type_id'], 'amount' => $paid));
					update_db('sales', array('paid' => 0), sprintf('id = %s', $result_array['id']));
				}

				print('Fixed ' . mysql_num_rows($result) . ' card payments.<br/>');

				$result = select_db('si.id, si.price, si.discount', 'sales as s join sale_items as si on s.id = si.sale_id join items as i on i.id = si.item_id', sprintf('s.company_id = %s and (si.price != i.price or si.discount != i.discount)', quote_smart($_SESSION['settings']['company_id'])));

				while ($result_array = mysql_fetch_array($result)) {
					update_db('sale_items', array('price' => $result_array['price'], 'discount' => $result_array['discount']), sprintf('id = %s', quote_smart($result_array['id'])));
				}

				print('Fixed ' . mysql_num_rows($result) . ' sale items regarding price and discount.<br/>');

				$result = select_db('si.id, i.tax', 'sales as s join sale_items as si on s.id = si.sale_id join items as i on i.id = si.item_id', sprintf('s.company_id = %s and si.tax = 0', quote_smart($_SESSION['settings']['company_id'])));

				while ($result_array = mysql_fetch_array($result)) {
					update_db('sale_items', array('tax' => ($result_array['tax'] === null ? $_SESSION['settings']['tax'] : $result_array['tax'])), sprintf('id = %s', quote_smart($result_array['id'])));
				}

				print('Fixed ' . mysql_num_rows($result) . ' sale items regarding tax.<br/>');
			}

			if ($_GET['check'] == 1) {
				$result = select_db('id, paid', 'sales', sprintf('company_id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id'])));

				while ($result_array = mysql_fetch_array($result)) {
					$count = 0;
					$totals = array();
					$totals['paid'] += $result_array['paid'];
					$result2 = select_db('si.price, si.discount, si.additional_discount, si.tax, si.quantity', 'sales as s join sale_items si on si.sale_id = s.id', sprintf('company_id = %s and sale_id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($result_array['id'])));

					while ($result_array2 = mysql_fetch_array($result2)) {
						$vals = calc($result_array2['price'], $result_array2['discount'], $result_array2['additional_discount'], $result_array2['tax'], $result_array2['quantity']);
						$totals['total'] += $vals['total'];
						$count++;
					}

					$result2 = select_db('value', 'vouchers', sprintf('type = "gift_certificate" and origin_id = %s', quote_smart($result_array['id'])));

					while ($result_array2 = mysql_fetch_array($result2)) {
						$totals['total'] += $result_array2['value'];
						$count++;
					}

					$result2 = select_db('amount', 'card_payments', sprintf('sale_id = %s', quote_smart($result_array['id'])));

					while ($result_array2 = mysql_fetch_array($result2)) {
						$totals['paid'] += $result_array2['amount'];
						$totals['card'] += $result_array2['amount'];
						$totals['nocash'] += $result_array2['amount'];
					}

					$result2 = select_db('value', 'vouchers', sprintf('sale_id = %s', quote_smart($result_array['id'])));

					while ($result_array2 = mysql_fetch_array($result2)) {
						$totals['paid'] += $result_array2['value'];
						$totals['nocash'] += $result_array2['value'];
					}

					if (number($totals['paid']) < number($totals['total'])) {
						print "sale $result_array[id] payment is wrong. ($totals[paid] < $totals[total])</br>\n";
					} else {
						#print "sale $result_array[id] payment is ok. ($totals[paid] >= $totals[total])</br>\n";
					}

					if (number($totals['card']) > number($totals['total'])) {
						print "sale $result_array[id] card payment is wrong. ($totals[card] > $totals[total])<br/>\n";
					}

					if (number($totals['nocash']) > number($totals['total'])) {
						$overpayment = number($totals['nocash'] - $totals['total']);
						$result2 = select_db('value', 'vouchers', sprintf('type = "overpayment" and origin_id = %s', quote_smart($result_array['id'])));

						if ($result_array2 = mysql_fetch_array($result2)) {
							if ($result_array2['value'] != $totals['nocash'] - $totals['total']) {
								print "sale $result_array[id] overpayment voucher value is wrong. ($result_array2[value] != $overpayment)<br/>\n";
							}
						} else {
							print "sale $result_array[id] overpayment voucher is missing. ($overpayment)<br/>\n";
						}
					}

					if ($count == 0) {
						print "sale $result_array[id] has no sale items.<br/>\n";
					}
				}
			}

			if ($_POST['preferences_management'] == 1) {
				if ($_POST['update_preferences'] == 1) {
					foreach (array_keys($_POST) as $key) {
						if (substr($key, 0, 11) == "preference_") {
							update_db('preference', array('value' => $_POST[$key]), sprintf('company_id = %s and name = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart(preg_replace('/_/', ' ', substr($key, 11)))));
						}
					}
					$_SESSION['message'] = "Preferences were updated successfully.";
				}
			}

			if ($_POST['user_management'] == 1) {
				if ($_POST['edit_user'] == 1) {
					mysql_num_rows(select_db('id', 'logins', sprintf('id = %s and company_id = %s and deleted is null', $_POST['edit_user_id'], $_SESSION['settings']['company_id']))) > 0 && $_SESSION['profiles']['user_id'] = $_POST['edit_user_id'];
				}

				if ($_POST['cancel_edit_user'] == 1) {
					if (isset($_SESSION['profiles']['user_id'])) {
						unset($_SESSION['profiles']['user_id']);
					}
				}

				if ($_POST['add_user'] == 1 || $_POST['update_user'] == 1) {
					if (isset($_SESSION['edit']['user']['new'])) {
						unset($_SESSION['edit']['user']['new']);
					}

					foreach (array_keys($_POST) as $key) {
						if (substr($key, 0, 4) == 'new_') {
							$_SESSION['edit']['user']['new'][substr($key, 4)] = substr($key, 4) == 'password' && $_POST[$key] != '' ? sprintf('[md5(%s)]', quote_smart($_POST[$key])) : $_POST[$key];
						}
					}

					foreach (array('lastname', 'firstname', 'username', 'password') as $var) {
						if (empty($_SESSION['edit']['user']['new'][$var]) && !($var == 'password' && $_POST['update_user'] == 1)) {
							$_SESSION['bad'][$var] = 1;
						}
					}

					if ($_SESSION['edit']['user']['new']['password'] == '') {
						unset($_SESSION['edit']['user']['new']['password']);
					}

					if (isset($_SESSION['bad'])) {
						$_SESSION['message'] = "ERROR: Profile could not be " . ($_POST['add_user'] == 1 ? 'added' : 'updated') . " since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.";
					} else {
						if ($_POST['add_user'] == 1) {
							$_SESSION['edit']['user']['new']['added'] = 0;
							$_SESSION['edit']['user']['new']['company_id'] = $_SESSION['settings']['company_id'];
							$insert_array = $_SESSION['edit']['user']['new'];
						} else {
							$insert_array = array('old_id' => $_POST['update_user_id'], 'deleted' => 0);
						}

						insert_db('login', $insert_array);

						if ($_POST['update_user'] == 1) {
							update_db('logins', $_SESSION['edit']['user']['new'], sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['update_user_id'])));
							foreach (array_keys($_SESSION['edit']['user']['new']) as $key) {
								$_SESSION['edit']['user']['old'][$key] = $_SESSION['edit']['user']['new'][$key];
							}
						}

						$_SESSION['message'] = "User '" . $_SESSION['edit']['user']['new']['username'] . "' was " . ($_POST['add_user'] == 1 ? 'added' : 'updated') . ($_POST['new_password'] != '' ? ' (including password)' : '') . ".";

						if ($_SESSION['settings']['login_id'] == $_POST['update_user_id']) {
							$_SESSION['settings']['user'] = $_SESSION['edit']['user']['new']['username'];
						}

						unset($_SESSION['edit']['user']['new']);
					}
				}

				if ($_POST['delete_user'] == 1) {
					if (update_db('login', array('deleted' => '[utc_timestamp()]'), sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['delete_user_id'])))) {
						$result_array = mysql_fetch_array(select_db('username', 'logins', sprintf('company_id = %s and id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['delete_user_id']))));

						$_SESSION['message'] = "User '$result_array[username]' was deleted.";
					}
				}
			}
		}

		if ($_POST['return_management'] == 1) {
			$return = &$_SESSION['return'];
			if ($_POST['action'] == 'show_sale') {
				$_POST['show_sale_details'] = 1;
				$_POST['receipt_id'] = $_POST['var'];
			}

			if ($_POST['show_sale_details'] == 1 && $_POST['receipt_id'] != "") {
				unset($_SESSION['return_receipt_barcode']);
				if (mysql_num_rows(select_db('id', 'sales', sprintf('company_id = %s and receipt_id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['receipt_id'])))) > 0) {
					$_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id'] = $_POST['receipt_id'];
				} else {
					$_SESSION['message'] = 'ERROR: No sale found with matching receipt barcode!';
				}
			}

			if ($_POST['show_return_details'] == 1 && $_POST['return_id'] > 0) {
				$result = select_db('r.barcode, l.username', 'returns as r left join logins as l on l.id = r.login_id', sprintf('r.company_id = %s and r.id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['return_id'])));
				if ($result_array = mysql_fetch_array($result)) {
					unset($_SESSION['return2']);
					$return = &$_SESSION['return2'];
					$return['receipt_id'] = $result_array['barcode'];
					$return['clerk'] = $result_array['username'];
					$return['totals'] = array();

					$result = select_db('si.id, s.added as sale_date, b.name as brand, i.id as item_id, i.name, i.number, i.style, si.price, si.discount, si.additional_discount, si.tax, si.quantity as sale_quantity, ri.quantity', 'return_items as ri join sale_items as si on si.id = ri.sale_item_id join sales as s on s.id = si.sale_id join items as i on i.id = si.item_id left join brands as b on b.id = i.brand_id', sprintf('ri.return_id = %s and s.deleted is null', quote_smart($_POST['return_id'])));
					while ($result_array = mysql_fetch_array($result)) {
						foreach (array_keys($result_array) as $key) {
							if (!is_numeric($key) && $key != 'id') {
								$return['items'][$result_array['id']][$key] = $result_array[$key];
							}
						}

						if ($result_array['tax'] === null) {
							$return['items'][$result_array['id']]['tax'] = $_SESSION['preferences']['tax'];
						}

						$result2 = select_db('coalesce(sum(ri.quantity), 0) as returns', 'returns as r join return_items as ri on ri.return_id = r.id', sprintf('ri.sale_item_id = %s and r.deleted is null', quote_smart($result_array['id'])));
						$result_array2 = mysql_fetch_array($result2);
						$return['items'][$result_array['id']]['returns_yet'] = $result_array2['returns'];
					}

					$_SESSION['settings']['reports']['show_return_details'] = 1;
				}
			}

			if ($_POST['update_return_items'] == 1) {
				$added = $removed = 0;
				foreach (array_keys($_POST) as $key) {
					if (substr($key, 0, 10) == 'sale_item_') {
						$sale_item_id = substr($key, 10);
						if (is_numeric($sale_item_id) && $_POST[$key] >= 0) {
							$result = select_db('s.receipt_id, s.added, b.name as brandname, i.id, si.price, si.discount, si.additional_discount, si.tax, si.quantity, i.name, c.attribute1 as attribute_name1, c.attribute2 as attribute_name2, i.attribute1, i.attribute2, i.barcode, i.number, i.style', 'sale_items as si join sales as s on s.id = si.sale_id join items as i on i.id = si.item_id join categories as c on c.id = i.category_id left join brands as b on b.id = i.brand_id', sprintf('si.id = %s', quote_smart($sale_item_id)));
							if ($result_array = mysql_fetch_array($result)) {
								$result2 = select_db('coalesce(sum(ri.quantity), 0) as returns', 'returns as r join return_items as ri on ri.return_id = r.id', sprintf('r.company_id = %s and ri.sale_item_id = %s and r.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($sale_item_id)));
								$result_array2 = mysql_fetch_array($result2);
								$returns_yet = $result_array2['returns'];
								$max = $result_array['quantity'];
								$_SESSION['debug']['max'] = $max;

								$result2 = select_db('coalesce(sum(ri.quantity), 0) as returned', 'return_items as ri join returns as r on r.id = ri.return_id', sprintf('ri.sale_item_id = %s and r.deleted is null', quote_smart($sale_item_id)));

								if ($result_array2 = mysql_fetch_array($result2)) {
									$max -= $result_array2['returned'];
								}

								if ($_POST[$key] > $max) {
									$_POST[$key] = $max;
								}

								if (isset($return['items'][$sale_item_id])) {
									if ($_POST[$key] > $return['items'][$sale_item_id]['quantity']) {
										$added += $_POST[$key] - $return['items'][$sale_item_id]['quantity'];
									}

									if ($_POST[$key] < $return['items'][$sale_item_id]['quantity']) {
										$removed += $return['items'][$sale_item_id]['quantity'] - $_POST[$key];
									}
								} else {
									if ($_POST[$key] > 0) {
										$added += $_POST[$key];
									}
								}

								if ($_POST[$key] > 0) {
									$vals = calc($result_array['price'], $result_array['discount'], $result_array['additional_discount'], $result_array['tax'], $_POST[$key]);
									$return['items'][$sale_item_id]['sale_receipt_id'] = $result_array['receipt_id'];
									$return['items'][$sale_item_id]['sale_date'] = $result_array['added'];
									$return['items'][$sale_item_id]['item_id'] = $result_array['id'];
									$return['items'][$sale_item_id]['brand'] = $result_array['brandname'];
									$return['items'][$sale_item_id]['price'] = $result_array['price'];
									$return['items'][$sale_item_id]['discount'] = $result_array['discount'];
									$return['items'][$sale_item_id]['tax'] = $result_array['tax'];
									$return['items'][$sale_item_id]['name'] = $result_array['name'];
									$return['items'][$sale_item_id]['attribute1'] = $result_array['attribute1'];
									$return['items'][$sale_item_id]['attributename1'] = $result_array['attribute_name1'];
									$return['items'][$sale_item_id]['attribute2'] = $result_array['attribute2'];
									$return['items'][$sale_item_id]['attributename2'] = $result_array['attribute_name2'];
									$return['items'][$sale_item_id]['barcode'] = $result_array['barcode'];
									$return['items'][$sale_item_id]['number'] = $result_array['number'];
									$return['items'][$sale_item_id]['style'] = $result_array['style'];
									$return['items'][$sale_item_id]['additional_discount'] = $result_array['additional_discount'];
									$return['items'][$sale_item_id]['sale_quantity'] = $result_array['quantity'];
									$return['items'][$sale_item_id]['returns_yet'] = $returns_yet;
									$return['items'][$sale_item_id]['quantity'] = $_POST[$key];
									$return['items'][$sale_item_id]['subtotal'] = $vals['total'];
								} else {
									unset($return['items'][$sale_item_id]);
								}
							}
						}
					}
				}

				if ($added > 0) {
					$_SESSION['message'] = $added . ' item' . ($added == 1 ? '' : 's') . ' ' . ($added == 1 ? 'was' : 'were') . ' added to the return.';
				}

				if ($removed > 0) {
					$_SESSION['message'] .= $_SESSION['message'] == '' ? '' : '<br/>';
					$_SESSION['message'] .= $removed . ' item' . ($removed == 1 ? '' : 's') . ' ' . ($removed == 1 ? 'was' : 'were') . ' removed from the return.';
				}

				if ($added == 0 && $removed == 0) {
					$_SESSION['message'] = 'Nothing was changed for the return.';
				}

				unset($_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id']);
			}

			if ($_POST['action'] == 'delete_item' && $_POST['var'] > 0) {
				unset($return['items'][$_POST['var']]);
			}

			if ($_POST['encash_return'] == 1) {
				if (!isset($return['receipt_id']) && $_SESSION['settings']['site'] == 'returns') {
					save_return($_SESSION['return']);
					$return['receipt_date'] = currentTimestamp();
					$return['clerk'] = $_SESSION['settings']['user'];
				}
				$_SESSION['show_return_receipt'] = 1;
			}

			if ($_POST['return_action'] == 'cancel') {
				if (isset($_SESSION['return'])) {
					if (!isset($_SESSION['return']['receipt_id'])) {
						$_SESSION['message'] = 'The return was cancelled.';
					}
					unset($_SESSION['return']);
					unset($_SESSION['settings']['returns']['sale_receipt_id']);
				}
			}

			if ($_POST['no_return_details'] == 1) {
				if ($_SESSION['settings']['site'] == 'reports') {
					unset($_SESSION['settings']['reports']['show_return_details']);
				}

				if ($_SESSION['settings']['site'] == 'exchanges') {
					unset($_SESSION['settings']['exchanges']['manage_type']);
				}
			}

			if ($_POST['return_receipt_show'] == 1) {
				unset($_SESSION['return2']);
				$return = &$_SESSION['return2'];

				$result = select_db(sprintf('coalesce(l.username, "-") as clerk, convert_tz(r.added, "utc", %s) as added, r.barcode as return_barcode, si.item_id, i.name, ri.quantity, si.price, si.tax, si.discount, si.additional_discount, i.attribute1, i.attribute2, i.number, i.barcode, c.attribute1 as catt1, c.attribute2 as catt2', quote_smart($_SESSION['preferences']['timezone'])), 'returns as r join return_items as ri on ri.return_id = r.id join sale_items as si on si.id = ri.sale_item_id join items as i on i.id = si.item_id join categories as c on c.id = i.category_id left join logins as l on r.login_id = l.id', sprintf('r.company_id = %s and r.id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['return_id'])));
				$totalprice = $totaldiscount = $totaltax = 0;
				while ($result_array = mysql_fetch_array($result)) {
					$return['clerk'] = $result_array['clerk'];
					$return['receipt_date'] = $result_array['added'];
					$return['receipt_id'] = $result_array['return_barcode'];
					$vals = calc($result_array['price'], $result_array['discount'], $result_array['additional_discount'], $result_array['tax'], $result_array['quantity']);
					$item = &$return['items'][$result_array['item_id']];
					$item['name'] = $result_array['name'];
					$item['quantity'] = $result_array['quantity'];
					$item['attribute1'] = $result_array['attribute1'];
					$item['attributename1'] = $result_array['catt1'];
					$item['attribute2'] = $result_array['attribute2'];
					$item['attributename2'] = $result_array['catt2'];
					$item['barcode'] = $result_array['barcode'];
					$item['number'] = $result_array['number'];
					$item['price'] = $result_array['price'];
					$item['discount'] = $result_array['discount'];
					$item['additional_discount'] = $result_array['additional_discount'];
					$item['tax'] = $result_array['tax'];
					$totalprice += $vals['price'];
					$totaldiscount += $vals['discount'];
					$totaltax += $vals['tax'];
				}

				$return['totals']['price'] = $totalprice;
				$return['totals']['discount'] = $totaldiscount;
				$return['totals']['tax'] = $totaltax;
				$_SESSION['show_return_receipt'] = 1;
			}
		}

		if ($_POST['exchange_management'] == 1) {
			$exchange = &$_SESSION['exchange'];
			$sale = &$_SESSION['sale'];
			$return = &$_SESSION['return'];

			if ($_POST['choose_manage_type'] == 1) {
				if (!isset($exchange['receipt_id'])) {
					$_SESSION['settings']['exchanges']['manage_type'] = $_POST['manage_type'];
				}
			}

			if ($_POST['encash'] == 1) {
				$exchange['encash'] = 1;
				$exchange['check_finish'] = 1;
			}

			if ($_POST['no_encash'] == 1) {
				unset($exchange['encash']);
				unset($exchange['cash_payment']);
				unset($exchange['card_payments']);
				unset($exchange['change']);
				unset($exchange['finish']);
			}

			if ($_POST['paid_cash'] == 1) {
				if (!is_numeric($_POST['paidamount'])) {
					$_SESSION['message'] = 'ERROR: The entered amount was not numeric.';
				} else {
					$exchange['cash_payment'] += $_POST['paidamount'];
					$exchange['check_finish'] = 1;
					$_SESSION['message'] = 'Added ' . money($_POST['paidamount']) . ' to the cash payment.';
				}
			}

			if ($_POST['card_payment_management'] == 1) {
				if ($_POST['add_card_payment'] == 1) {
					if ($_POST['paidamount'] != '' && !is_numeric($_POST['paidamount'])) {
						$_SESSION['message'] = 'ERROR: The entered amount was not numeric.';
					} else {
						if ($_POST['paidamount'] == '') {
							$_POST['paidamount'] = $exchange['totals']['price'] - $exchange['totals']['discount'] + $exchange['totals']['tax'];
						}
						$result = select_db('name', 'card_types', sprintf('id = %s', quote_smart($_POST['cardtype'])));
						if ($result_array = mysql_fetch_array($result)) {
							$id = is_array($exchange['card_payments']) ? count($exchange['card_payments']) + 1 : 1;
							$exchange['card_payments'][$id]['type'] = $_POST['cardtype'];
							$exchange['card_payments'][$id]['name'] = $result_array['name'];
							$exchange['card_payments'][$id]['amount'] = $_POST['paidamount'];
						}
						$exchange['check_finish'] = 1;
						$_SESSION['message'] = 'One card payment was added.';
					}
				}

				if ($_POST['delete_card_payment'] == 1) {
					if ($_POST['delete_card_payment_id'] > 0 && isset($exchange['card_payments'][$_POST['delete_card_payment_id']])) {
						unset($exchange['card_payments'][$_POST['delete_card_payment_id']]);
						$_SESSION['message'] = 'The card payment was deleted.';
					}
				}
			}

			if ($_POST['voucher_payment_management'] == 1) {
				if ($_POST['add_voucher_payment'] == 1) {
					$result = select_db(sprintf('id, value, barcode, date_format(convert_tz(added, "utc", %s), %s) as issued', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y @ %H:%i:%S'"), 'vouchers', sprintf('company_id = %s and barcode = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['voucherbarcode'])));
					if ($result_array = mysql_fetch_array($result)) {
						if (isset($exchange['voucher_payments'][$result_array['id']])) {
							$_SESSION['message'] = 'This voucher was already added before.';
						} else {
							$exchange['voucher_payments'][$result_array['id']]['issued'] = $result_array['issued'];
							$exchange['voucher_payments'][$result_array['id']]['barcode'] = $result_array['barcode'];
							$exchange['voucher_payments'][$result_array['id']]['value'] = $result_array['value'];
							$exchange['check_finish'] = 1;
							$_SESSION['message'] = 'One voucher payment was added.';
						}
					} else {
						$_SESSION['message'] = 'No valid voucher was found.';
					}
				}

				if ($_POST['delete_voucher_payment'] == 1) {
					if ($_POST['delete_voucher_payment_id'] > 0 && isset($exchange['voucher_payments'][$_POST['delete_voucher_payment_id']])) {
						unset($exchange['voucher_payments'][$_POST['delete_voucher_payment_id']]);
						$_SESSION['message'] = 'The voucher payment was deleted.';
					}
				}
			}

			if ($_POST['save_exchange'] == 1) {
				if (!isset($exchange['receipt_id'])) {
					$receipt_id = date('dymsHi');

					$sale_id = save_sale($sale, 0, $receipt_id);
					$exchange['clerk'] = $sale['clerk'];

					$return_id = save_return($return, 0, $receipt_id);

					if (is_array($exchange['card_payments'])) {
						foreach (array_keys($exchange['card_payments']) as $id) {
							insert_db('card_payment', array('sale_id' => $sale_id, 'card_type_id' => $exchange['card_payments'][$id]['type'], 'amount' => $exchange['card_payments'][$id]['amount']));
						}
					}

					if ($exchange['cash_payment'] > 0) {
						update_db('sale', array('paid' => $exchange['cash_payment']), sprintf('id = %s', quote_smart($sale_id)));
					}

					update_db('voucher', array('sale_id' => $sale_id), sprintf('origin_id = %s and type = "return"', quote_smart($return_id)));

					if (is_array($exchange['voucher_payments']) && count(array_keys($exchange['voucher_payments'])) > 0) {
						update_db('voucher', array('deleted' => '[utc_timestamp()]', 'sale_id' => $sale_id), sprintf('company_id = %s and id in (%s)', quote_smart($_SESSION['settings']['company_id']), implode(array_keys($exchange['voucher_payments']), ',')));
					}

					if ($exchange['change']['voucher'] > 0) {
						insert_db('voucher', array('company_id' => $_SESSION['settings']['company_id'], 'barcode' => $receipt_id, 'type' => 'overpayment', 'value' => $exchange['change']['voucher'], 'added' => 0));
					}

					$exchange['receipt_id'] = $receipt_id;
					$_SESSION['message'] = 'The exchange was saved.';
				}

				$_SESSION['show_exchange_receipt'] = 1;
			}

			if ($_POST['cancel']) {
				if (isset($_SESSION['sale']) || isset($_SESSION['return'])) {
					if (!isset($_SESSION['exchange']['receipt_id'])) {
						$_SESSION['message'] = 'The exchange (sale and return) was cancelled.';
					}
					unset($_SESSION['sale']);
					unset($_SESSION['return']);
					unset($_SESSION['exchange']);
				}
			}
		}

		if ($_POST['showiteminfo'] == 1) {
			$_SESSION['settings']['itemmgnt']['search_option_zero'] = ($_POST['search_option_zero'] == 1 ? '1' : '0');
			$_SESSION['settings']['itemmgnt']['search_option_archived'] = ($_POST['search_option_archived'] == 1 ? '1' : '0');
			if (preg_replace('/\s/', '', $_POST['infoitemid']) != "") {
                        	$result = select_db('i.id, i.category_id, i.number', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and c.type = %s and i.barcode = %s and coalesce(i.deleted, c.deleted) is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['infoitemid'])));

				if ($result_array = mysql_fetch_array($result)) {
                                	$_SESSION['settings']['itemmgnt']['highlight_item_id'] = $result_array['id'];
					$_SESSION['settings']['itemmgnt']['category_id'] = $result_array['category_id'];
					$_SESSION['settings']['itemmgnt']['highlight_item_number'] = $result_array['number'];
				} else {
					if ($_SESSION['settings']['manager'] == 1) {
						$_SESSION['additembarcode'] = $_POST['infoitemid'];
						$_SESSION['settings']['itemmgnt']['add_item'] = 1;
					} else {
						$_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'] = 0;
					}
				}
			}
		}

		if ($_POST['choose_result_page'] == 1) {
			$_SESSION['settings']['itemmgnt']['page'] = $_POST['result_page'];
		}

		if ($_POST['gotoadditem'] == 1) {
			$_SESSION['settings']['itemmgnt']['add_item'] = 1;
			unset($_SESSION['additembarcode']);
			unset($_SESSION['edit']['item']['new']);
		}

		if ($_POST['additem'] == 1) {
			$_SESSION['edit']['item']['new']['category_id'] = $_POST['new_category_id'];
			$_SESSION['edit']['item']['new']['supplier_id'] = $_POST['new_supplier_id'];
			$_SESSION['edit']['item']['new']['brand_id'] = $_POST['new_brand_id'];
			$_SESSION['edit']['item']['new']['department_id'] = $_POST['new_department_id'];
			$_SESSION['edit']['item']['new']['taxgroup_id'] = $_POST['new_taxgroup_id'];
			$_SESSION['edit']['item']['new']['name'] = $_POST['new_name'];
			$_SESSION['edit']['item']['new']['number'] = $_POST['new_number'];
			$_SESSION['edit']['item']['new']['style'] = $_POST['new_style'];
			$_SESSION['edit']['item']['new']['attribute1'] = $_POST['new_attribute1'];
			$_SESSION['edit']['item']['new']['attribute2'] = $_POST['new_attribute2'];
			$_SESSION['edit']['item']['new']['price'] = $_POST['new_price'];
			$_SESSION['edit']['item']['new']['buy_price'] = $_POST['new_buy_price'];
			$_SESSION['edit']['item']['new']['discount'] = $_POST['new_discount'];
			$_SESSION['edit']['item']['new']['location'] = $_POST['new_location'];
			$_SESSION['edit']['item']['new']['quantity'] = $_POST['new_quantity'];
			$_SESSION['edit']['item']['new']['barcode'] = empty($_POST['new_barcode']) ? date('dymsHi') : $_POST['new_barcode'];
			$_SESSION['edit']['item']['new']['reorder_limit1'] = empty($_POST['new_reorder_limit1']) ? $_SESSION['preferences']['default_reorder_limit1'] : $_POST['new_reorder_limit1'];;
			$_SESSION['edit']['item']['new']['reorder_limit2'] = empty($_POST['new_reorder_limit2']) ? $_SESSION['preferences']['default_reorder_limit2'] : $_POST['new_reorder_limit2'];;
			$_SESSION['edit']['item']['new']['archived'] = $_POST['new_archived'];

			if ($_SESSION['edit']['item']['new']['category_id'] < 1) {
				$_SESSION['bad']['category'] = 1;
			}
			if ($_SESSION['edit']['item']['new']['supplier_id'] < 1) {
				$_SESSION['bad']['supplier'] = 1;
			}
			if ($_SESSION['edit']['item']['new']['brand_id'] < 1) {
				$_SESSION['bad']['brand'] = 1;
			}
			if ($_SESSION['edit']['item']['new']['department_id'] < 1) {
				$_SESSION['bad']['department'] = 1;
			}
			if ($_SESSION['edit']['item']['new']['taxgroup_id'] < 1) {
				$_SESSION['bad']['taxgroup'] = 1;
			}
			if (trim($_SESSION['edit']['item']['new']['name']) == '') {
				$_SESSION['bad']['name'] = 1;
			}
			if (trim($_SESSION['edit']['item']['new']['number']) == '') {
				$_SESSION['bad']['number'] = 1;
			}

			if (isset($_SESSION['bad'])) {
				$_SESSION['message'] = 'ERROR: Item could not be added since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.';
			} else {
				if (strlen($_SESSION['edit']['item']['new']['barcode']) % 2 > 0) {
					$_SESSION['edit']['item']['new']['barcode'] = "0" . $_SESSION['edit']['item']['new']['barcode'];
				}

				insert_db('item', array('category_id' => $_SESSION['edit']['item']['new']['category_id'], 'supplier_id' => $_SESSION['edit']['item']['new']['supplier_id'], 'brand_id' => $_SESSION['edit']['item']['new']['brand_id'], 'department_id' => $_SESSION['edit']['item']['new']['department_id'], 'tax_group_id' => $_SESSION['edit']['item']['new']['taxgroup_id'], 'name' => $_SESSION['edit']['item']['new']['name'], 'number' => $_SESSION['edit']['item']['new']['number'], 'style' => $_SESSION['edit']['item']['new']['style'], 'attribute1' => $_SESSION['edit']['item']['new']['attribute1'], 'attribute2' => $_SESSION['edit']['item']['new']['attribute2'], 'price' => $_SESSION['edit']['item']['new']['price'], 'buy_price' => $_SESSION['edit']['item']['new']['buy_price'], 'discount' => $_SESSION['edit']['item']['new']['discount'], 'location' => $_SESSION['edit']['item']['new']['location'], 'quantity' => $_SESSION['edit']['item']['new']['quantity'], 'barcode' => $_SESSION['edit']['item']['new']['barcode'], 'reorder_limit1' => $_SESSION['edit']['item']['new']['reorder_limit1'], 'reorder_limit2' => $_SESSION['edit']['item']['new']['reorder_limit2'], 'archived' => $_SESSION['edit']['item']['new']['archived'], 'added' => 0));

				$_SESSION['message'] = "Item was added successfully.";
				$_SESSION['added'][mysql_insert_id()] = 1;

				foreach (array_keys($_SESSION['edit']['item']['new']) as $key) {
					$_SESSION['edit']['item']['old'][$key] = $_SESSION['edit']['item']['new'][$key];
				}

				unset($_SESSION['additembarcode']);
				unset($_SESSION['edit']['item']['new']);
				$_SESSION['settings']['ka'] = 1;
			}
		}

		if ($_POST['updateitem'] == 1) {
			$_SESSION['edit']['item']['new']['category_id'] = $_POST['new_category_id'];
			$_SESSION['edit']['item']['new']['supplier_id'] = $_POST['new_supplier_id'];
			$_SESSION['edit']['item']['new']['brand_id'] = $_POST['new_brand_id'];
			$_SESSION['edit']['item']['new']['department_id'] = $_POST['new_department_id'];
			$_SESSION['edit']['item']['new']['taxgroup_id'] = $_POST['new_taxgroup_id'];
			$_SESSION['edit']['item']['new']['name'] = $_POST['new_name'];
			$_SESSION['edit']['item']['new']['number'] = $_POST['new_number'];
			$_SESSION['edit']['item']['new']['style'] = $_POST['new_style'];
			$_SESSION['edit']['item']['new']['attribute1'] = $_POST['new_attribute1'];
			$_SESSION['edit']['item']['new']['attribute2'] = $_POST['new_attribute2'];
			$_SESSION['edit']['item']['new']['price'] = $_POST['new_price'];
			$_SESSION['edit']['item']['new']['buy_price'] = $_POST['new_buy_price'];
			$_SESSION['edit']['item']['new']['discount'] = $_POST['new_discount'];
			$_SESSION['edit']['item']['new']['location'] = $_POST['new_location'];
			$_SESSION['edit']['item']['new']['quantity'] = $_POST['new_quantity'];
			$_SESSION['edit']['item']['new']['barcode'] = empty($_POST['new_barcode']) ? date('dymsHi') : $_POST['new_barcode'];
			$_SESSION['edit']['item']['new']['reorder_limit1'] = empty($_POST['new_reorder_limit1']) ? $_SESSION['preferences']['default_reorder_limit1'] : $_POST['new_reorder_limit1'];;
			$_SESSION['edit']['item']['new']['reorder_limit2'] = empty($_POST['new_reorder_limit2']) ? $_SESSION['preferences']['default_reorder_limit2'] : $_POST['new_reorder_limit2'];;
			$_SESSION['edit']['item']['new']['archived'] = $_POST['new_archived'];

			if ($_SESSION['edit']['item']['new']['category_id'] < 1) {
				$_SESSION['bad']['category'] = 1;
			}
			if ($_SESSION['edit']['item']['new']['supplier_id'] < 1) {
				$_SESSION['bad']['supplier'] = 1;
			}
			if ($_SESSION['edit']['item']['new']['brand_id'] < 1) {
				$_SESSION['bad']['brand'] = 1;
			}
			if ($_SESSION['edit']['item']['new']['department_id'] < 1) {
				$_SESSION['bad']['department'] = 1;
			}
			if (trim($_SESSION['edit']['item']['new']['name']) == '') {
				$_SESSION['bad']['name'] = 1;
			}
			if (trim($_SESSION['edit']['item']['new']['number']) == '') {
				$_SESSION['bad']['number'] = 1;
			}

			if (isset($_SESSION['bad'])) {
				$_SESSION['message'] = 'ERROR: Item could not be updated since at least one mandatory detail is missing or wrong. Whatever is missing or wrong is marked in red.';
			} else {
				if (strlen($_SESSION['edit']['item']['new']['barcode']) % 2 > 0) {
					$_SESSION['edit']['item']['new']['barcode'] = "0" . $_SESSION['edit']['item']['new']['barcode'];
				}

				$result = select_db('i.id', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and c.type = %s and i.id = %s and coalesce(i.deleted, c.deleted) is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['update_item_id'])));
				if ($result_array = mysql_fetch_array($result)) {
					update_db('items', array('deleted' => '[utc_timestamp()]'), sprintf('id = %s', quote_smart($result_array['id'])));

                                	$_SESSION['edit']['item']['new']['id'] = insert_db('item', array('old_id' => $result_array['id'], 'category_id' => $_SESSION['edit']['item']['new']['category_id'], 'supplier_id' => $_SESSION['edit']['item']['new']['supplier_id'], 'brand_id' => $_SESSION['edit']['item']['new']['brand_id'], 'department_id' => $_SESSION['edit']['item']['new']['department_id'], 'tax_group_id' => $_SESSION['edit']['item']['new']['taxgroup_id'], 'name' => $_SESSION['edit']['item']['new']['name'], 'number' => $_SESSION['edit']['item']['new']['number'], 'style' => $_SESSION['edit']['item']['new']['style'], 'attribute1' => $_SESSION['edit']['item']['new']['attribute1'], 'attribute2' => $_SESSION['edit']['item']['new']['attribute2'], 'price' => $_SESSION['edit']['item']['new']['price'], 'buy_price' => $_SESSION['edit']['item']['new']['buy_price'], 'discount' => $_SESSION['edit']['item']['new']['discount'], 'location' => $_SESSION['edit']['item']['new']['location'], 'quantity' => $_SESSION['edit']['item']['new']['quantity'], 'quantity' => $_SESSION['edit']['item']['new']['quantity'], 'barcode' => $_SESSION['edit']['item']['new']['barcode'], 'reorder_limit1' => $_SESSION['edit']['item']['new']['reorder_limit1'], 'reorder_limit2' => $_SESSION['edit']['item']['new']['reorder_limit2'], 'archived' => $_SESSION['edit']['item']['new']['archived']));

                                	if ($_SESSION['edit']['item']['new']['id'] > 0) {
                                        	update_db('item_image_mappings', array('item_id' => $_SESSION['edit']['item']['new']['id']), sprintf('item_id = %s', quote_smart($result_array['id'])));

						foreach (array_keys($_SESSION['edit']['item']['new']) as $key) {
							$_SESSION['edit']['item']['old'][$key] = $_SESSION['edit']['item']['new'][$key];
						}

						if ($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'] == $result_array['id']) {
							$_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'] = $_SESSION['edit']['item']['old']['id'];
							$forward = 1;
						}
                                	}

					$_SESSION['message'] = "Item was updated successfully.";
				}
			}
		}

		if ($_POST['deleteitem'] == 1) {
			$result = select_db('i.id', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and c.type = %s and i.id = %s and i.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['delete_item_id'])));
			if ($result_array = mysql_fetch_array($result)) {
				update_db('item', array('deleted' => '[utc_timestamp()]'), sprintf('id = %s', quote_smart($result_array['id'])));
				$_SESSION['message'] = "Item was deleted successfully.";
			}
		}

                if ($_POST['item_management'] == 1) {
			if (isset($_POST['show_type'])) {
				$_SESSION['settings']['itemmgnt']['type'] = $_POST['show_type'];
			}

			foreach (array_keys($_POST) as $key) {
				if (substr($key, 0, 7) == 'choose_' && $_POST[$key] == 1) {
					$_SESSION['settings']['itemmgnt']['search_option_zero'] = ($_POST['search_option_zero'] == 1 ? '1' : '0');
					$_SESSION['settings']['itemmgnt']['search_option_archived'] = ($_POST['search_option_archived'] == 1 ? '1' : '0');
					$name = substr($key, 7);
					$_SESSION['settings']['itemmgnt'][$name . '_id'] = $_POST[$name . '_id'];
					foreach (array('category', 'department', 'brand', 'number') as $name2) {
						if (!isset($_SESSION['settings']['itemmgnt'][$name2 . '_id'])) { $_SESSION['settings']['itemmgnt'][$name2 . '_id'] = '0'; }
					}
					unset($_SESSION['settings']['itemmgnt']['page']);
					unset($_SESSION['settings']['itemmgnt']['style_edit_item_id']);
				}
			}

                        if ($_POST['showsaledetails_rid'] > 0) {
				if (mysql_num_rows(select_db('id', 'sales', sprintf('company_id = %s and receipt_id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_POST['showsaledetails_rid'])))) > 0) {
                                	$_SESSION['sale']['receipt_id'] = $_POST['showsaledetails_rid'];
				} else {
					$_SESSION['message'] = 'ERROR: No sale found with matching receipt barcode!';
				}
                        }

			if ($_POST['search_options_update'] == 1) {
				$_SESSION['settings']['itemmgnt']['search_option_zero'] = ($_POST['search_option_zero'] == 1 ? '1' : '0');
				$_SESSION['settings']['itemmgnt']['search_option_archived'] = ($_POST['search_option_archived'] == 1 ? '1' : '0');
			}
                }

		if ($_POST['report_management'] == 1) {
			if (isset($_POST['show_type'])) {
				$_SESSION['settings']['reports']['type'] = $_POST['show_type'];
			}

			if ($_POST['date_range_report'] == 1) {
				if ($_POST['from_date'] != '' && $_POST['till_date'] != '') {
					$_SESSION['settings']['reports']['frame'] = 'r';
					$_SESSION['settings']['reports']['from_date'] = $_POST['from_date'];
					$_SESSION['settings']['reports']['till_date'] = $_POST['till_date'];
				}
			}

			if ($_POST['clerk_select'] == 1) {
				if ($_POST['sales_person_id'] > 0) {
					$_SESSION['settings']['reports']['sales_person_id'] = $_POST['sales_person_id'];
				} else {
					unset($_SESSION['settings']['reports']['sales_person_id']);
				}
			}
		}

		if ($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'] > 0 && mysql_num_rows(select_db('i.id', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and c.type = %s and i.id = %s and i.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'])))) == 0) {
			unset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id']);
			$forward = 1;
		}

		if ($_POST['edititem'] == 1 || $_POST['showiteminfo'] == 1) {
			if ($_POST['showiteminfo'] == 1) {
				$_POST['itemid'] = $_SESSION['settings']['itemmgnt']['highlight_item_id'];
			}
			if (isset($_SESSION['settings']['itemmgnt']['highlight_item_id']) && $_SESSION['settings']['itemmgnt']['highlight_item_id'] > 0) {
				$_SESSION['settings']['itemmgnt']['highlight_item_id'] = $_POST['itemid'];
				$_SESSION['settings']['itemmgnt']['open_highlight'] = 1;
			}
			if ($result_array = mysql_fetch_array(select_db('i.category_id, i.supplier_id, i.brand_id, i.department_id, i.tax_group_id, i.name, i.number, i.style, i.attribute1, i.attribute2, i.price, i.buy_price, i.discount, i.location, i.quantity, i.barcode, i.reorder_limit1, i.reorder_limit2, i.archived', 'items as i join categories as c on i.category_id = c.id', sprintf('i.id = %s and c.company_id = %s and coalesce(i.deleted, c.deleted) is null', quote_smart($_POST['itemid']), quote_smart($_SESSION['settings']['company_id']))))) {
				$_SESSION['edit']['item']['old']['id'] = $_POST['itemid'];
				$_SESSION['edit']['item']['old']['category_id'] = $result_array['category_id'];
				$_SESSION['edit']['item']['old']['supplier_id'] = $result_array['supplier_id'];
				$_SESSION['edit']['item']['old']['brand_id'] = $result_array['brand_id'];
				$_SESSION['edit']['item']['old']['department_id'] = $result_array['department_id'];
				$_SESSION['edit']['item']['old']['taxgroup_id'] = $result_array['tax_group_id'];
				$_SESSION['edit']['item']['old']['name'] = $result_array['name'];
				$_SESSION['edit']['item']['old']['number'] = $result_array['number'];
				$_SESSION['edit']['item']['old']['style'] = $result_array['style'];
				$_SESSION['edit']['item']['old']['attribute1'] = $result_array['attribute1'];
				$_SESSION['edit']['item']['old']['attribute2'] = $result_array['attribute2'];
				$_SESSION['edit']['item']['old']['price'] = $result_array['price'];
				$_SESSION['edit']['item']['old']['buy_price'] = $result_array['buy_price'];
				$_SESSION['edit']['item']['old']['discount'] = $result_array['discount'];
				$_SESSION['edit']['item']['old']['location'] = $result_array['location'];
				$_SESSION['edit']['item']['old']['quantity'] = $result_array['quantity'];
				$_SESSION['edit']['item']['old']['barcode'] = $result_array['barcode'];
				$_SESSION['edit']['item']['old']['reorder_limit1'] = $result_array['reorder_limit1'];
				$_SESSION['edit']['item']['old']['reorder_limit2'] = $result_array['reorder_limit2'];
				$_SESSION['edit']['item']['old']['archived'] = $result_array['archived'];

				foreach (array_keys($_SESSION['edit']['item']['old']) as $key) {
					$_SESSION['edit']['item']['new'][$key] = $_SESSION['edit']['item']['old'][$key];
				}

				if ($_POST['edititem'] == 1 && !(isset($_SESSION['settings']['itemmgnt']['highlight_item_id']) && $_SESSION['settings']['itemmgnt']['highlight_item_id'] > 0)) {
					if (isset($_SESSION['sale3']) || isset($_SESSION['return3'])) {
						$_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id2'] = $_POST['itemid'];
					} else {
						$_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'] = $_POST['itemid'];
					}
				}
			} else {
				die($_POST['itemid']);
			}
		}

		if ($_POST['newitempicture'] == 1) {
			if ($_POST['item_id'] > 0 && isset($_FILES['new_picture']['tmp_name']) && file_exists($_FILES['new_picture']['tmp_name'])) {
				$info = getimagesize($_FILES['new_picture']['tmp_name']);
				unset($image);
				switch ($info['mime']) {
					case 'image/gif' :
						if (!isset($image)) { $image = imagecreatefromgif($_FILES['new_picture']['tmp_name']); }
					case 'image/jpeg' :
						if (!isset($image)) { $image = imagecreatefromjpeg($_FILES['new_picture']['tmp_name']); }
					case 'image/png' :
						if (!isset($image)) { $image = imagecreatefrompng($_FILES['new_picture']['tmp_name']); }
						$m = $info[0] > $info[1] ? 600 / $info[0] : 600 / $info[1];
						$x = floor($info[0] * $m); $y = floor($info[1] * $m);
						$image2 = imagecreatetruecolor($x, $y);
						imagecopyresized($image2, $image, 0, 0, 0, 0, $x, $y, $info[0], $info[1]);
						imagedestroy($image);
						$fname = '/tmp/shopimport.png';
						imagepng($image2, $fname);
						imagedestroy($image2);
						$f = fopen($fname, 'r');
						$c = fread($f, filesize($fname));
						fclose($f);
						$t = chunk_split(base64_encode($c));
						$id = insert_db('image', array('image' => $t, 'width' => $x, 'height' => $y, 'added' => 0));
						insert_db('item_image_mapping', array('item_id' => $_POST['item_id'], 'image_id' => $id, 'added' => 0));
						unlink($fname);
						$_SESSION['message'] = 'Picture was uploaded successfully.';
						break;
					default:
						$_SESSION['message'] = sprintf('ERROR: Unsupported image type: %s', $info['mime']);
				}
			}
		}

		if ($_POST['defaultitempicture'] == 1) {
			if ($_POST['item_id'] > 0 && $_POST['image_id'] > 0) {
				if ($_POST['defaultgrouppicture'] == 1) {
					$var = 'iim.default_group_image';
					update_db('item_image_mappings as iim join items as i on i.id = iim.id join categories as c on c.id = i.category_id join items as i2 on i2.number = i.number and i2.category_id = i.category_id', array($var => 0), sprintf('i2.id = %s and c.company_id = %s and coalesce(iim.deleted, i.deleted, c.deleted, i2.deleted) is null', quote_smart($_POST['item_id']), quote_smart($_SESSION['settings']['company_id'])));
				} else {
					$var = 'iim.default_item_image';
					update_db('item_image_mappings as iim join items as i on i.id = iim.id join categories as c on c.id = i.category_id', array($var => 0), sprintf('i.id = %s and c.company_id = %s and coalesce(iim.deleted, i.deleted, c.deleted) is null', quote_smart($_POST['item_id']), quote_smart($_SESSION['settings']['company_id'])));
				}
				$num = update_db('item_image_mappings as iim join items as i on i.id = iim.id join categories as c on c.id = i.category_id', array($var => 1), sprintf('i.id = %s and iim.image_id = %s and c.company_id = %s and coalesce(iim.deleted, i.deleted, c.deleted) is null', quote_smart($_POST['item_id']), quote_smart($_POST['image_id']), quote_smart($_SESSION['settings']['company_id'])));
				if ($num == 1) {
					$_SESSION['message'] = 'The selected picture is the default ' . ($var == 'default_item_image' ? 'item' : 'group') . ' picture now.';
				}
			}
		}

		if ($_POST['deleteitempicture'] == 1) {
			if ($_POST['item_id'] > 0 && $_POST['image_id'] > 0) {
				$num = update_db('item_image_mapping', array('deleted' => '[utc_timestamp]'), sprintf('item_id = %s and image_id = %s and deleted is null', quote_smart($_POST['item_id']), quote_smart($_POST['image_id'])));
				if ($num == 1) {
					$_SESSION['message'] = 'The selected picture was deleted.';
				}
			}
		}

		if ($_POST['noitemdetails'] == 1) {
			if (isset($_SESSION['settings']['itemmgnt']['highlight_item_id'])) {
				unset($_SESSION['settings']['itemmgnt']['highlight_item_id']);
				unset($_SESSION['settings']['itemmgnt']['highlight_item_number']);
				unset($_SESSION['settings']['itemmgnt']['category_id']);
			} else if (isset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id2'])) {
				unset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id2']);
			} else if (isset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'])) {
				unset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id']);
			} else {
				unset($_SESSION['settings']['itemmgnt']['add_item']);
				unset($_SESSION['settings']['ka']);
				unset($_SESSION['additembarcode']);
			}
		}

		if ($_POST['modifycategory'] == 1 && $_SESSION['manage'] == 1) {
			if (!empty($_POST['new_groupname'])) {
				$pcid = 0;
				if ($_SESSION['settings']['itemmgnt']['category_id'] > 0) {
					$pcid = $_SESSION['settings']['itemmgnt']['category_id'];
				}

				if ($pcid > 0 && mysql_num_rows(select_db('id', 'categories', sprintf('company_id = %s and type = %s and name like %s and parent_id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['new_groupname']), quote_smart($pcid)))) == 0) {
					insert_db('category', array('name' => $_POST['new_groupname'], 'parent_id' => $pcid, 'type' => $_SESSION['settings']['pagetype'], 'added' => 0));
				}
			}
			if (!empty($_POST['deletecategoryid'])) {
				update_db('categories as c1 left join categories as c2 on c2.parent_id = c1.id and c2.deleted is null left join products as p on p.category_id = c1.id and p.deleted is null', array('c1.deleted', '[utc_timestamp()]'), sprintf('c2.id is null and p.id is null and c1.company_id = %s and c1.type = %s and c1.id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['deletecategoryid'])));
			}
			if (!empty($_POST['modifycategoryid'])) {
				update_db('category', array('name' => $_POST['modifycategoryname'], 'subtitle' => $_POST['modifycategorysubtitle'], 'description' => $_POST['modifycategorydescription'], 'active' => $_POST['modifycategoryactive']), sprintf('where company_id = %s and type = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_POST['modifycategoryid'])));
			}
		}

		if ($_POST['modifyproduct'] == 1 && $_SESSION['manage'] == 1) {
                        if (!empty($_POST['new_productname']) && $_SESSION['settings']['itemmgnt']['category_id'] > 0) {
                                $result = select_db('id', 'products', sprintf('name like %s and deleted is null', quote_smart($_POST['new_productname'])));
                                if (mysql_num_rows($result) == 0) {
                                        insert_db('product', array('name' => $_POST['new_productname'], 'category_id' => $_POST['new_productname'], 'added' => 0));
                                }
                        }
			if (!empty($_POST['deleteproductid'])) {
				update_db('product', array('deleted' => '[utc_timestamp()]', sprintf('id = %s and deleted is null', quote_smart($_POST['deleteproductid']))));
			}
			if (!empty($_POST['modifyproductid'])) {
				update_db('product', array('name' => $_POST['modifyproductname'], 'barcode' => $_POST['modifyproductbarcode'], 'price' => $_POST['modifyproductprice'], 'discount' => $_POST['modifyproductdiscount'], 'sizes' => $_POST['modifyproductsizes'], 'active' => $_POST['modifyproductactive'], 'description' => $_POST['modifyproductdescription']), sprintf('id = %s and deleted is null', quote_smart($_POST['modifyproductid'])));
			}
		}

		if ($_POST['no_sale_details'] == 1) {
			if ($_SESSION['settings']['site'] == 'sales') {
				unset($_SESSION['sale']['receipt_id']);
			}

			if ($_SESSION['settings']['site'] == 'returns' || $_SESSION['settings']['site'] == 'itemmgnt') {
				unset($_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id']);
			}

			if ($_SESSION['settings']['site'] == 'exchanges') {
				if (isset($_SESSION['settings']['exchanges']['sale_receipt_id'])) {
					unset($_SESSION['settings']['exchanges']['sale_receipt_id']);
				} else {
					unset($_SESSION['settings']['exchanges']['manage_type']);
				}
			}

			if ($_SESSION['settings']['site'] == 'reports') {
				if (isset($_SESSION['return3']) || isset($_SESSION['sale3'])) {
					unset($_SESSION['return3']);
					unset($_SESSION['sale3']);
					unset($_SESSION['settings']['reports']['show_return_details2']);
					unset($_SESSION['settings']['reports']['sale_receipt_id2']);
				} else {
					unset($_SESSION['return2']);
					unset($_SESSION['sale2']);
					unset($_SESSION['settings']['reports']['show_return_details']);
					unset($_SESSION['settings']['reports']['sale_receipt_id']);
				}
			}
		}

		if ($_POST['reportdatechange'] == 1) {
			if ($_POST['reportyear'] > 0) { $_SESSION['dateinfo']['year'] = $_POST['reportyear']; }
			if ($_POST['reportyear'] < 0) { unset($_SESSION['dateinfo']['year']); }
			if ($_POST['reportmonth'] > 0) { $_SESSION['dateinfo']['month'] = $_POST['reportmonth']; }
			if ($_POST['reportmonth'] < 0) { unset($_SESSION['dateinfo']['month']); }
			if ($_POST['reportday'] > 0) { $_SESSION['dateinfo']['day'] = $_POST['reportday']; }
			if ($_POST['reportday'] < 0) { unset($_SESSION['dateinfo']['day']); }
			$_SESSION['dateinfo']['string'] = $_POST['reportstring'];
		}

		if ($_POST['item_group_management'] == 1) {
			if ($_POST['style_edit'] == 1) {
				$result = select_db('i.id, i.number, i.name, i.brand_id, i.department_id, i.quantity, i.style, i.category_id', 'items as i join categories as c on c.id = i.category_id left join brands as b on b.id = i.brand_id', sprintf('i.id = %s and c.company_id = %s and coalesce(i.deleted, c.deleted, b.deleted) is null order by c.name asc, c.id asc, i.number asc, b.name asc, i.name asc, i.style asc, i.attribute1 asc, i.attribute2 asc limit 1', quote_smart($_POST['item_id']), quote_smart($_SESSION['settings']['company_id'])));
				if ($result_array = mysql_fetch_array($result)) {
					$_SESSION['settings']['itemmgnt']['style_edit_item_id'] = $result_array['id'];
					$_SESSION['edit']['style']['old']['category_id'] = $result_array['category_id'];
					$_SESSION['edit']['style']['old']['number'] = $result_array['number'];
					$_SESSION['edit']['style']['old']['name'] = $result_array['name'];
					$_SESSION['edit']['style']['old']['brand_id'] = $result_array['brand_id'];
					$_SESSION['edit']['style']['old']['department_id'] = $result_array['department_id'];
					$_SESSION['edit']['style']['old']['style'] = $result_array['style'];

					foreach (array_keys($_SESSION['edit']['style']['old']) as $key) {
						$_SESSION['edit']['style']['new'][$key] = $_SESSION['edit']['style']['old'][$key];
					}
				}
			}

			if ($_POST['update_style'] == 1) {
				if (!($_POST['new_category_id'] > 0)) { $_SESSION['bad']['category'] = 1; }
				if (trim($_POST['new_number']) == '') { $_SESSION['bad']['number'] = 1; }
				if (trim($_POST['new_name']) == '') { $_SESSION['bad']['name'] = 1; }
				if (!($_POST['new_brand_id'] > 0)) { $_SESSION['bad']['brand'] = 1; }
				if (!($_POST['new_department_id'] > 0)) { $_SESSION['bad']['department'] = 1; }

				if (!isset($_SESSION['bad'])) {
					if (is_array($_SESSION['edit']['style']['ids'])) {
						foreach (array_keys($_SESSION['edit']['style']['ids']) as $item_id) {
							update_db('items', array('deleted' => '[utc_timestamp()]'), sprintf('id = %s', $item_id));
							$new_id = insert_db('item', array('old_id' => $item_id, 'category_id' => $_POST['new_category_id'], 'number' => $_POST['new_number'], 'name' => $_POST['new_name'], 'brand_id' => $_POST['new_brand_id'], 'department_id' => $_POST['new_department_id'], 'style' => $_POST['new_style']));
							if ($new_id > 0) {
								update_db('item_image_mappings', array('item_id' => $new_id), sprintf('item_id = %s', quote_smart($item_id)));
							}
							$_SESSION['settings']['itemmgnt']['style_edit_item_id'] = $new_id;
						}

						if ($_SESSION['settings']['itemmgnt']['category_id'] > 0 && $_SESSION['settings']['itemmgnt']['category_id'] != $_POST['new_category_id']) {
							$_SESSION['settings']['itemmgnt']['category_id'] = $_POST['new_category_id'];
						}

						$_SESSION['message'] = 'All items that were shown before were updated. After the update you might see additional items that have the same common details now.';
						unset($_SESSION['edit']['style']['old']);
					}
				} else {
					$_SESSION['message'] = 'ERROR: Nothing could be updated since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.';
				}

				$_SESSION['edit']['style']['new']['category_id'] = $_POST['new_category_id'];
				$_SESSION['edit']['style']['new']['number'] = $_POST['new_number'];
				$_SESSION['edit']['style']['new']['name'] = $_POST['new_name'];
				$_SESSION['edit']['style']['new']['brand_id'] = $_POST['new_brand_id'];
				$_SESSION['edit']['style']['new']['department_id'] = $_POST['new_department_id'];
				$_SESSION['edit']['style']['new']['style'] = $_POST['new_style'];
			}

			if ($_POST['exit_style_edit'] == 1) {
				unset($_SESSION['settings']['itemmgnt']['style_edit_item_id']);
				unset($_SESSION['style']);
			}
		}

		if ($_SESSION['settings']['itemmgnt']['category_id'] > 0) {
			$result = select_db('parent_id, deleted', 'categories', sprintf('company_id = %s and type = %s and id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($_SESSION['settings']['itemmgnt']['category_id'])));
			if ($result_array = mysql_fetch_array($result)) {
				if ($result_array['deleted'] != "") {
					if ($result_array['parent_id'] > 0) {
						$_SESSION['settings']['itemmgnt']['category_id'] = $result_array['parent_id'];
						$forward = 1;
					} else {
						unset($_SESSION['settings']['itemmgnt']['category_id']);
						$_SESSION['settings']['site'] = "main";
						$forward = 1;
					}
				}
			} else {
				unset($_SESSION['settings']['itemmgnt']['category_id']);
				$_SESSION['settings']['site'] = "main";
				$forward = 1;
			}
		}

		if ($_SESSION['settings']['site'] == "") {
			$_SESSION['settings']['site'] = "main";
			$forward = 1;
		}

		$num = 0;

		foreach(array_keys($_POST) as $key) {
			$_SESSION["POST_$key"] = $_POST[$key];
			$num++;
		}

		if ($num > 0) {
			$forward = 1;
		} else {
			foreach(array_keys($_SESSION) as $key) {
				if (substr($key, 0, 5) == "POST_") {
					$_POST[substr($key, 5)] = $_SESSION[$key];
					unset($_SESSION[$key]);
				}
			}
		}
	}
	if ($forward == 2) {
		$_SESSION['keep_debug'] = 1;
		header("Location: $GLOBALS[weburl]");
		exit();
	}
	# Messages
	if ($_SESSION['settings']['site'] == 'sales') {
		if (isset($_SESSION['sale']['receipt_id']) && isset($_SESSION['exchange']['receipt_id']) && $_SESSION['sale']['receipt_id'] == $_SESSION['exchange']['receipt_id']) {
			$_SESSION['message'] .= "The shown sale is part of an encashed exchange. Click on 'START NEW SALE'.<br/>";
		}
	}
	if ($_SESSION['settings']['site'] == 'returns') {
		if (isset($_SESSION['return']['receipt_id']) && isset($_SESSION['exchange']['receipt_id']) && $_SESSION['return']['receipt_id'] == $_SESSION['exchange']['receipt_id']) {
			$_SESSION['message'] .= "The shown return is part of an encashed exchange. Click on 'START NEW RETURN'.<br/>";
		}
	}
	if ($_SESSION['settings']['site'] == 'exchanges') {
		if (isset($_SESSION['sale']['receipt_id']) && (!isset($_SESSION['exchange']['receipt_id']) || $_SESSION['sale']['receipt_id'] != $_SESSION['exchange']['receipt_id'])) {
			$_SESSION['message'] .= "The shown sale items are part of an encashed sale. Go to 'MANAGE SALE ITEMS' and click 'START NEW SALE'.<br/>";
		}

		if (isset($_SESSION['return']['receipt_id']) && (!isset($_SESSION['exchange']['receipt_id']) || $_SESSION['return']['receipt_id'] != $_SESSION['exchange']['receipt_id'])) {
			$_SESSION['message'] .= "The shown return items are part of an encashed return. Go to 'MANAGE RETURN ITEMS' and click 'START NEW RETURN'.<br/>";
		}
	}
}
?>