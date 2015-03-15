<?

	session_start();

?>
<update>
  <calendar>
    <replace>
<?

	if (isset($_SESSION['settings']['login_id'])) {
		include_once('../../includes/shop.php');
		dbconnect();

		if ($_GET['calendar_text'] != '') {
			$result = select_db('i.quantity, i.buy_price, i.price as sell_price', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and i.id = %s and coalesce(i.deleted, c.deleted) is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_GET['item_id'])));
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
	}

?>
    </replace>
  </text>
</update>
