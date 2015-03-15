<?

	session_start();

	if (isset($_SESSION['settings']['login_id'])) {
		include_once('../../includes/shop.php');
		dbconnect();

?>
<update>
<?

		if ($_GET['update_delivery_items'] == 1) {

?>
  <option>
    <append>
<?

			if (!empty($_GET['number_item_id'])) {
                                $result = select_db('i.number, i.id, concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info', 'items as i join items as i2 on i2.number = i.number join suppliers as s on s.id = i.supplier_id join brands as b on i.brand_id = b.id', sprintf('s.id = %s and s.company_id = b.company_id and s.company_id = %s and coalesce(s.deleted, b.deleted, i.deleted) is null and i2.id = %s order by i.number asc, b.name asc, i.name asc, i.attribute1 asc, i.attribute2 asc', quote_smart($_SESSION['delivery']['supplier_id']), quote_smart($_SESSION['settings']['company_id']), quote_smart($_GET['number_item_id'])));
			} else {
                                $result = select_db('i.id, concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info', 'items as i join suppliers as s on s.id = i.supplier_id join brands as b on i.brand_id = b.id', sprintf('s.id = %s and s.company_id = b.company_id and s.company_id = %s and coalesce(s.deleted, b.deleted, i.deleted) is null order by i.number asc, b.name asc, i.name asc, i.attribute1 asc, i.attribute2 asc', quote_smart($_SESSION['delivery']['supplier_id']), quote_smart($_SESSION['settings']['company_id'])));
			}

			while ($result_array = mysql_fetch_array($result)) {
				if (!isset($_SESSION['delivery']['items'][$result_array['id']])) {
					if (isset($result_array['number'])) {
						$_SESSION['delivery']['item_number'] = $result_array['number'];
					} else {
						unset($_SESSION['delivery']['item_number']);
					}

?>
      <ajax_delivery_item_select><?=$result_array['id']?>,<?=$result_array['info']?></ajax_delivery_item_select>
<?

				}
			}

?>
    </append>
  </option>
<?

		} else {

?>
  <text>
    <replace>
<?

			if ($_GET['item_id'] > 0 && $_GET['mode'] == 'delivery') {
				$result = select_db('i.quantity, i.buy_price, i.price as sell_price', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and i.id = %s and coalesce(i.deleted, c.deleted) is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_GET['item_id'])));
			}

			if ($_GET['category_id'] > 0 && $_GET['mode'] == 'add_item') {
				$result = select_db('upper(c.attribute1) as attribute1, upper(c.attribute2) as attribute2', 'categories as c', sprintf('c.company_id = %s and c.id = %s and c.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_GET['category_id'])));
			}

			if ($result_array = isset($result) ? mysql_fetch_array($result) : null) {
				foreach (array_keys($result_array) as $key) {
					if (!is_numeric($key)) {

?>
      <ajax_<?=$key?>><?=$result_array[$key]?></ajax_<?=$key?>>
<?

					}
				}
			}

?>
    </replace>
  </text>
<?

		}

?>
</update>
<?

	}

?>
