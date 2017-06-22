<?php
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(__DIR__."/imageHelper.php");
require_once(__DIR__."/productHelper.php");

$data = $_POST['post_data'];
$arr = json_decode($data, true);

if(strlen($arr['name']) > 120){
	$arr['name'] = substr($arr['name'], 0, 120);
}


$arr['name'] = preg_replace('/[^a-zA-Z0-9 \']/', '', $arr['name']);
$arr['name'] = str_replace("'", '', $arr['name']);

$product = new ProductHelper();

if($product->fetchAffiliateProductId($arr['asin']) != 0){
	echo "Product already exits ";
	return;
}

$product_id = $product->addProduct($arr['name'], $arr['prestashop_category'], $arr['price'], $arr['short_description']);

if($product_id != 0)
{
	try
	{
		$product->updateProduct("affiliate_product_id", $arr['asin'], $product_id);
		$product->updateProduct("affiliate_website", $arr['website'], $product_id);
	}catch(Exception $e)
	{
	}

	try
	{
		$imageAdd = new ImageAdd();
			$img_name = str_replace(' ', '-',  $arr['name']);
		$image_id = $imageAdd->insertImageInPrestashop($product_id, $arr['images'], $img_name);
		if($image_id == 0){
			//$product->deleteProduct($product_id)
			$message = "Unable to add image";
		}else
		{
			$message =  "Product added successfully";
		}
	}catch(Exception $e)
	{
		//$product->deleteProduct($product_id);
		$message = "Unable to add image";
	}

}else
{
	$message = "Unable to add producct";
}

echo $message;
return $message;

?>
