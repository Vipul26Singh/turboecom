<?php

class ProductHelper
{
	public function updateProduct($column, $val, $product_id){
                $sql = "update "._DB_PREFIX_."product set  {$column} = '{$val}' WHERE id_product = '{$product_id}'";
                Db::getInstance()->execute($sql);
        }

        public function deleteProduct($product_id){
                $product = new ProductCore($productId, false);
                return $product->delete();
        }

	public function addProduct($name, $category_id, $price, $short_description){
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

	public function fetchAffiliateProductId($asin){
                $sql = new DbQuery();
                $sql->from('product');
                $sql->select('count(*) as count');
                $sql->where("affiliate_product_id = '".$asin."'");

                return Db::getInstance()->executeS($sql)[0]['count'];
        }



}


?>
