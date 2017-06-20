<?php
	require_once(dirname(__FILE__) . '/../../config/config.inc.php');


	function get_best_path_for_image($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }
        return $path;
    }

	function copyImage($id_entity, $id_image = null, $url = '', $entity = 'products', $regenerate = true)
{
	$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
	$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

	switch ($entity) {
		default:
		case 'products':
			$image_obj = new Image($id_image);
			$path = $image_obj->getPathForCreation();
			break;
		case 'categories':
			$path = _PS_CAT_IMG_DIR_.(int)$id_entity;
			break;
		case 'manufacturers':
			$path = _PS_MANU_IMG_DIR_.(int)$id_entity;
			break;
		case 'suppliers':
			$path = _PS_SUPP_IMG_DIR_.(int)$id_entity;
			break;
		case 'stores':
			$path = _PS_STORE_IMG_DIR_.(int)$id_entity;
			break;
	}

	$url = urldecode(trim($url));
	$parced_url = parse_url($url);

	if (isset($parced_url['path'])) {
		$uri = ltrim($parced_url['path'], '/');
		$parts = explode('/', $uri);
		foreach ($parts as &$part) {
			$part = rawurlencode($part);
		}
		unset($part);
		$parced_url['path'] = '/'.implode('/', $parts);
	}

	if (isset($parced_url['query'])) {
		$query_parts = array();
		parse_str($parced_url['query'], $query_parts);
		$parced_url['query'] = http_build_query($query_parts);
	}

	if (!function_exists('http_build_url')) {
		require_once(_PS_TOOL_DIR_.'http_build_url/http_build_url.php');
	}

	$url = http_build_url('', $parced_url);

	$orig_tmpfile = $tmpfile;
	if (Tools::copy($url, $tmpfile)) {
		// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
		if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
			@unlink($tmpfile);
			return false;
		}

		$tgt_width = $tgt_height = 0;
		$src_width = $src_height = 0;
		$error = 0;
		ImageManager::resize($tmpfile, $path.'.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);
		$images_types = ImageType::getImagesTypes($entity, true);

		if ($regenerate) {
			$previous_path = null;
			$path_infos = array();
			$path_infos[] = array($tgt_width, $tgt_height, $path.'.jpg');
			foreach ($images_types as $image_type) {
				$tmpfile = get_best_path_for_image($image_type['width'], $image_type['height'], $path_infos);

				if (ImageManager::resize(
							$tmpfile,
							$path.'-'.stripslashes($image_type['name']).'.jpg',
							$image_type['width'],
							$image_type['height'],
							'jpg',
							false,
							$error,
							$tgt_width,
							$tgt_height,
							5,
							$src_width,
							$src_height
							)) {
					// the last image should not be added in the candidate list if it's bigger than the original image
					if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
						$path_infos[] = array($tgt_width, $tgt_height, $path.'-'.stripslashes($image_type['name']).'.jpg');
					}
					if ($entity == 'products') {
						if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg')) {
							unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg');
						}
						if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg')) {
							unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg');
						}
					}
				}
				if (in_array($image_type['id_image_type'], $watermark_types)) {
					Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
				}
			}
		}
	} else {
		@unlink($orig_tmpfile);
		return false;
	}
	unlink($orig_tmpfile);
	return true;
}



class ImageAdd
{

	public function insertImageInPrestashop($id_product, $url, $name_photo)
	{
		$shops = Shop::getShops(true, null, true);
		$image = new ImageCore();
		$image->id_product = $id_product;
		$image->position = Image::getHighestPosition($id_product) + 1;
		$image->cover = true;
		$tmp = explode(".", $name_photo);
		$name_photo_product = "";
		$name_for_legend = "";
		if (count($tmp) == 1) {
			$name_photo_product = trim($url) . $name_photo . ".jpg";
			$name_for_legend = $name_photo . ".jpg";
		} else {
			$name_photo_product = trim($url) . $name_photo;
			$name_for_legend = $name_photo;
		}
		$image->legend = array('1' => trim($name_for_legend));
		if ($image->validateFields(false, true) === true && $image->validateFieldsLang(false, true) === true && $image->add()) {
			$image->associateTo($shops);
			if (!copyImage($id_product, $image->id, $name_photo_product, 'products')) {
				$image->delete();
			}
		}
		return $image->id;
	}

}


	$data = $_POST['post_data'];

	$arr = json_decode($data, true);


	if(fetchAffiliateProductId($arr['asin']) != 0){
		return;
	}


	$product_id = addProduct($arr['name'], $arr['prestashop_category'], $arr['price'], $arr['short_description']);

	if($product_id != 0){
								try{
                                                                updateProduct("affiliate_product_id", $arr['asin'], $product_id);
                                                                updateProduct("affiliate_website", $arr['website'], $product_id);
								}catch(Exception $e){
                                                                }

								try{

									$imageAdd = new ImageAdd();

									$image_id = $imageAdd->insertImageInPrestashop($product_id, $arr['images'], $arr['name']);

									if($image_id == 0){
										deleteProduct($product_id);
									}
								}catch(Exception $e){
                                                                                deleteProduct($product_id);
                                                                }

                           }

	function updateProduct($column, $val, $product_id){
                $sql = "update "._DB_PREFIX_."product set  {$column} = '{$val}' WHERE id_product = '{$product_id}'";
                Db::getInstance()->execute($sql);
        }

	function deleteProduct($product_id){
                $product = new ProductCore($productId, false);
                return $product->delete();
        }

	function addProduct($name, $category_id, $price, $short_description){
                $name = stripslashes(strip_tags(trim($name)));

		if(empty($name) || empty($category_id) || empty($price)){
                        return 0;
                }

                $id_product = 0;

                $product = new Product();
                $product->ean13 = 0;
                $product->name = array((int)Configuration::get('PS_LANG_DEFAULT') =>  $name);
                $product->id_category = $category_id;
                $product->id_category_default = $category_id;
                $product->redirect_type = '404';
                $product->price = $price;
                $product->quantity = 1;
                $product->minimal_quantity = 1;
                $product->show_price = 1;
                $product->on_sale = 0;
                $product->online_only = 1;
                $product->is_virtual=0;
                $product->available_for_order = 0;
                //$product->description = $short_description;
                $product->description_short = $short_description;
                $product->available_now = 0;

                try{
                        $product->add();
                        $product->addToCategories(array($category_id));

                        $id_product = $product->id;
                }catch(Exception $e){
                        $id_product = 0;
                }

                return $id_product;
        }

	function maxProductId()
        {
                $sql = new DbQuery();
                $sql->from('product');
                $sql->select('max(id_product) as product_id');

                return Db::getInstance()->executeS($sql)[0]['product_id'];
        }

	function fetchAffiliateProductId($asin){
                $sql = new DbQuery();
                $sql->from('product');
                $sql->select('count(*) as count');
                $sql->where("affiliate_product_id = '".$asin."'");

                return Db::getInstance()->executeS($sql)[0]['count'];
        }


?>

