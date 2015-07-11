<?php
die("die");
define('DB_HOST','77.77.141.220');
define('DB_USER','systemsmartsales');
define('DB_PASS','sLxCw4j5VJNvdexa');
define('DB_NAME','systemsmartsales');
define('DEFAULT_CHARSET','UTF8');

mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("cant connect");
mysql_select_db(DB_NAME) or die("cant select");
mysql_query("SET NAMES UTF8");

Header("Content-Type: text/html; charset=UTF-8");

$mongo = new Mongo( '127.0.0.1' );

$site_ids = array('12');

foreach($site_ids as $site_id)
{
	$mongo_products = $mongo->test->products;
	
	// PRICES
	$prices_q = mysql_query("SELECT product_id, value_dds FROM prices_by_source WHERE object = 'Yello' AND type = '100g'");
	while($v = mysql_fetch_row($prices_q))
	{
		$product_prices[$v['0']] = $v['1'];
	}
	
	global $desc_types;
	
	$descriptors = mysql_query("SELECT descriptor_id FROM site_descriptors WHERE site_id = '{$site_id}' AND show_in_filter = 'y'");
	
	while($v = mysql_fetch_row($descriptors))
	{
		$desc_ids[] = $v['0'];
		$q = mysql_query("SELECT col_type FROM product_type_desc WHERE id = '{$v['0']}'");
		$r = mysql_fetch_row($q);
		$desc_types[$v['0']] = $r['0'];
	}
	$desc_ids_string = @implode(",", $desc_ids);
	
	$mongo_products->remove();
	
	$products = mysql_query("SELECT id, type_id, brand_id, title, part_no, category_id_2, series_value_id, model_id, serie_name FROM `products_{$site_id}` WHERE active = 'y'");
	while($v=mysql_fetch_row($products))
	{
		$product = array();
		$product['product_id'] = (int)$v['0'];
		$product['type_id'] = (int)$v['1'];
		$product['site_id'] = (int)$site_id;
		$product['brand_id'] = (int)$v['2'];
		
		$product['s_q'] = $v['3'].' '.$v['4'].' '.$v['8'];
		$product['category_id_2'] = (int)$v['5'];
		$product['series_value_id'] = (int)str_replace(';', '', $v['6']);
		$product['model_id'] = (int)$v['7'];
		
		// price
		$product['d_511492'] = (int)$product_prices[$v['0']];
		
		$pr_desc = mysql_query("SELECT descriptor_id, value FROM product_desc_data WHERE descriptor_id IN ({$desc_ids_string}) AND product_id = '{$v['0']}' AND `value` NOT IN('', '-') GROUP by descriptor_id");
		
		while($d = mysql_fetch_row($pr_desc))
		{
			$product['d_'.$d['0']] = proccessDesc( $d['0'], $d['1'] );
		}
		
		$mongo_products->insert($product);
		print $i++;
	}
}

function proccessDesc( $desc_id, $val )
{
	global $desc_types;
	$type = $desc_types[$desc_id];
	
	if($type == 'n')
		return (float)$val;
	if($type == 'c')
	{
		$a = explode(";", $val);
		
		foreach($a as $v)
		{
			if($v)
			{
				$newval[] = $v;
			}
		}
		
		return $newval;
	}
	else
		return $val;
}
?>