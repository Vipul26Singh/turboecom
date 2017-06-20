<?php
if (!defined('_PS_VERSION_'))
exit;

require_once(__DIR__."/imageHelper.php");

if(file_exists(__DIR__."/affiliate/amazon/amazonAPI.php")){
	require_once(__DIR__."/affiliate/amazon/amazonAPI.php");
	require_once(__DIR__."/affiliate/amazon/amazonConfig.php");
}

if(file_exists(__DIR__."/affiliate/ebay/ebayAPI.php")){
	require_once(__DIR__."/affiliate/ebay/ebayAPI.php");
	require_once(__DIR__."/affiliate/ebay/ebayConfig.php");
}

if(file_exists(__DIR__."/affiliate/aliexpress/aliexpressAPI.php")){
        require_once(__DIR__."/affiliate/aliexpress/aliexpressAPI.php");
        require_once(__DIR__."/affiliate/aliexpress/aliexpressConfig.php");
}

class TurboeCom extends Module
{
	public $amazon_allowed = false;
	public $ebay_allowed = false;
	public $aliexpress_allowed = false;
	public $class_module_name = 'turboecom'; 
	public $amazonConfig;
	public $aliexpressConfig;
	public $ebayConfig;

	public function __construct()
	{
		$this->name = $this->class_module_name;
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'TurboeCom';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		if(file_exists(__DIR__."/affiliate/amazon/amazonAPI.php")){
			$this->amazon_allowed = true;
		}

                if(file_exists(__DIR__."/affiliate/ebay/ebayAPI.php")){
                        $this->ebay_allowed = true;
                } 

		if(file_exists(__DIR__."/affiliate/aliexpress/aliexpressAPI.php")){
                        $this->aliexpress_allowed = true;
                }

		$this->displayName = $this->l('TurboeCom');
		$this->description = $this->l('Affilite programs wirh e-commerce');

		$this->confirmUninstall = $this->l('Are you sure... you will not be able to add affiliaate program');


		if($this->amazon_allowed == true){
			$this->amazonConfig = new AmazonConfig($this);
			$this->amazonConfig->checkConfiguration();
		}


		if($this->ebay_allowed == true){
			$this->ebayConfig = new EbayConfig($this);
			$this->ebayConfig->checkConfiguration();
                }

		if($this->aliexpress_allowed == true){
                        $this->aliexpressConfig = new AliexpressConfig($this);
                        $this->aliexpressConfig->checkConfiguration();
                }


	}

	public function hookDisplayBackOfficeHeader(){
                $this->context->controller->addCSS($this->_path.'/views/css/back.css', 'all');
		$this->context->controller->addJS($this->_path.'/views/js/back.js');
		$this->context->controller->addJS($this->_path.'/views/js/back_add.js');
		$this->context->controller->addJS($this->_path.'/views/js/amazon.js');
        }

	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		$result = parent::install() && $this->registerHook('displayBackOfficeHeader') && $this->installTables() && $this->installData();
		Configuration::updateValue('TurboeCom_amazon_access_key', '');
		Configuration::updateValue('TurboeCom_amazon_secret_key', '');
		Configuration::updateValue('TurboeCom_amazon_affiliate_id', '');
		Configuration::updateValue('TurboeCom_ebay_app_id', '');
		Configuration::updateValue('TurboeCom_ebay_compaign_id', '');
		Configuration::updateValue('TurboeCom_aliexpress_app_secret', '');
		Configuration::updateValue('TurboeCom_aliexpress_tracking_id', '');
		Configuration::updateValue('amazon_page_number', '1');
		Configuration::updateValue('ebay_page_number', '1');
		Configuration::updateValue('aliexpress_page_number', '1');
		Configuration::updateValue('amazon_keyword', '');
		Configuration::updateValue('amazon_prestashop_category', '');
		Configuration::updateValue('amazon_category', '');


		return $result;
	}


	private function installData(){
		try{
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('All'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Wine'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Wireless'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('ArtsAndCrafts'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Miscellaneous'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Electronics'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Jewelry'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MobileApps'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Photo'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Shoes'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('KindleStore'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Automotive'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Vehicles'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Pantry'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MusicalInstruments'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('DigitalMusic'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('GiftCards'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionBaby'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionGirls'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('GourmetFood'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('HomeGarden'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MusicTracks'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('UnboxVideo'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionWomen'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('VideoGames'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionMen'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Kitchen'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Video'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Software'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Beauty'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Grocery'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionBoys'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Industrial'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('PetSupplies'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('OfficeProducts'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Magazines'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Watches'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Luggage'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('OutdoorLiving'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Toys'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('SportingGoods'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('PCHardware'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Movies'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Books'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Collectibles'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Handmade'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('VHS'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MP3Downloads'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Fashion'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Tools'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Baby'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Apparel'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Marketplace'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('DVD'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Appliances'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Music'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('LawnAndGarden'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('WirelessAccessories'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Blended'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('HealthPersonalCare'), 'site_name'  => 'amazon.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Classical'), 'site_name'  => 'amazon.com'));


			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('-1'), 'category_name' => pSQL('All'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('20081'), 'category_name' => pSQL('Antiques'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('550'), 'category_name' => pSQL('Art'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('2984'), 'category_name' => pSQL('Baby'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('267'), 'category_name' => pSQL('Books'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('12576'), 'category_name' => pSQL('Business & Industrial'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('625'), 'category_name' => pSQL('Cameras & Photo'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('15032'), 'category_name' => pSQL('Cell Phones & Accessories'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11450'), 'category_name' => pSQL('Clothing, Shoes & Accessories'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11116'), 'category_name' => pSQL('Coins & Paper Money'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1'), 'category_name' => pSQL('Collectibles'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('58058'), 'category_name' => pSQL('Computers/Tablets & Networking'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('293'), 'category_name' => pSQL('Consumer Electronics'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('14339'), 'category_name' => pSQL('Crafts'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('237'), 'category_name' => pSQL('Dolls & Bears'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11232'), 'category_name' => pSQL('DVDs & Movies'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('45100'), 'category_name' => pSQL('Entertainment Memorabilia'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('172008'), 'category_name' => pSQL('Gift Cards & Coupons'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('26395'), 'category_name' => pSQL('Health & Beauty'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11700'), 'category_name' => pSQL('Home & Garden'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('281'), 'category_name' => pSQL('Jewelry & Watches'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11233'), 'category_name' => pSQL('Music'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('619'), 'category_name' => pSQL('Musical Instruments & Gear'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1281'), 'category_name' => pSQL('Pet Supplies'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('870'), 'category_name' => pSQL('Pottery & Glass'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('10542'), 'category_name' => pSQL('Real Estate'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('316'), 'category_name' => pSQL('Specialty Services'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('888'), 'category_name' => pSQL('Sporting Goods'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('64482'), 'category_name' => pSQL('Sports Mem, Cards & Fan Shop'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('260'), 'category_name' => pSQL('Stamps'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1305'), 'category_name' => pSQL('Tickets & Experiences'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('220'), 'category_name' => pSQL('Toys & Hobbies'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('3252'), 'category_name' => pSQL('Travel'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1249'), 'category_name' => pSQL('Video Games & Consoles'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('99'), 'category_name' => pSQL('Everything Else'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Automobiles and Motorcycles'), 'category_id' => pSQL('34'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Beauty and Health'), 'category_id' => pSQL('66'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Computer and Office'), 'category_id' => pSQL('7'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Construction and Real Estate'), 'category_id' => pSQL('13'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Consumer Electronics'), 'category_id' => pSQL('44'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Electrical Equipment and Supplies'), 'category_id' => pSQL('5'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Electronic Components and Supplies'), 'category_id' => pSQL('502'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Food'), 'category_id' => pSQL('2'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Furniture'), 'category_id' => pSQL('1503'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Hair and Accessories'), 'category_id' => pSQL('200003655'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Hardware'), 'category_id' => pSQL('42'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Home and Garden'), 'category_id' => pSQL('15'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Home Appliances'), 'category_id' => pSQL('6'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Industry and Business'), 'category_id' => pSQL('200001996'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Jewelry and Accessories'), 'category_id' => pSQL('36'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Lights and Lighting'), 'category_id' => pSQL('39'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Luggage and Bags'), 'category_id' => pSQL('1524'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Mother and Kids'), 'category_id' => pSQL('1501'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Office and School Supplies'), 'category_id' => pSQL('21'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Phones and Telecommunications'), 'category_id' => pSQL('509'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Security and Protection'), 'category_id' => pSQL('30'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Shoes'), 'category_id' => pSQL('322'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Special Category'), 'category_id' => pSQL('200001075'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Sports and Entertainment'), 'category_id' => pSQL('18'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Tools'), 'category_id' => pSQL('1420'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Toys and Hobbies'), 'category_id' => pSQL('26'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Travel and Coupon Services'), 'category_id' => pSQL('200003498'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Watches'), 'category_id' => pSQL('1511'), 'site_name'  => 'aliexpress.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Weddings and Events'), 'category_id' => pSQL('320'), 'site_name'  => 'aliexpress.com'));
		}catch(Exception $e){

		}
		return true;
	}


	private function installTables(){

		try{
			Db::getInstance()->execute('DROP table '._DB_PREFIX_.'affiliate_category');
		}catch(Exception $e){
		}
		$result = Db::getInstance()->execute('
				CREATE TABLE if not exists `'._DB_PREFIX_.'affiliate_category`( 
					`category_name` VARCHAR(100) NOT NULL,
					`site_name` VARCHAR(100) NOT NULL,
					`category_id` VARCHAR(100) NULL,
					PRIMARY KEY (`site_name`, `category_name`)
					) DEFAULT CHARSET=utf8;');


		try{
			$sql_add_asin = 'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `affiliate_product_id` VARCHAR(100) NULL';
			Db::getInstance()->query($sql_add_asin);
		}catch(Exception $e){

		}

		try{
			$sql_add_site =   'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `affiliate_website` VARCHAR(100) NULL';
			Db::getInstance()->query($sql_add_site);
		}catch(Exception $e){

                }

		try{
			$sql_add_index = 'ALTER TABLE `'._DB_PREFIX_.'product` ADD INDEX `turboecom_affiliate_product_id` (`affiliate_product_id`)';
			Db::getInstance()->query($sql_add_index);
		}catch(Exception $e){

                }

		return true;
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	private function initList()
	{
		$this->fields_list = array(
				'id_category' => array(
					'title' => $this->l('Id'),
					'width' => 140,
					'type' => 'text',
					),
				'name' => array(
					'title' => $this->l('Name'),
					'width' => 140,
					'type' => 'text',
					),
				);
		$helper = new HelperList();

		$helper->shopLinkType = '';

		$helper->simple_header = false;

		// Actions to be displayed in the "Actions" column
		$helper->actions = array('edit', 'delete', 'view');

		$helper->identifier = 'id_category';
		$helper->show_toolbar = true;
		$helper->title = 'HelperList';
		$helper->table = $this->name.'_categories';
		$helper->actions = array('edit', 'delete', 'view');

		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		return $helper;
	}


	public function getContent()
	{
		$output = null;

		if($this->amazon_allowed == true){ 
			$output.=$this->amazonConfig->getAmazonContent().$this->amazonConfig->amazonConfig().$this->amazonConfig->amazonForm();
		}

		if($this->ebay_allowed == true){
                        $output.=$this->ebayConfig->getEbayContent().$this->ebayConfig->ebayConfig().$this->ebayConfig->ebayForm();
                }

		if($this->aliexpress_allowed == true){
                        $output.=$this->aliexpressConfig->getAliexpressContent().$this->aliexpressConfig->aliexpressConfig().$this->aliexpressConfig->aliexpressForm();
                }

		return $output;
	}


	public function addProduct($name, $category_id, $price, $short_description){
		if(empty($name) || empty($category_id) || empty($price)){
			return 0;
		}

		$id_product = 0;

		$product = new Product();
		$product->ean13 = 0;
		$product->name = array((int)Configuration::get('PS_LANG_DEFAULT') =>  $name);;
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

			$id_product = $this->maxProductId();	
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

        public function updateProduct($column, $val, $product_id){
                $sql = "update "._DB_PREFIX_."product set  {$column} = '{$val}' WHERE id_product = '{$product_id}'";
                Db::getInstance()->execute($sql);
        }

        public function fetchPrestashopCategory()
        {
                $sql = new DbQuery();
                $sql->from('category_lang');
                $sql->select('distinct id_category, name');
                $sql->orderBy('name');

                return Db::getInstance()->executeS($sql);
        }

        public function fetchAffiliateCategory($site)
        {
                $sql = new DbQuery();
                $sql->select('category_name, category_id');
                $sql->from('affiliate_category', 'a');
                $sql->where("a.site_name = '". pSQL($site)."'");
                $sql->orderBy('category_name');

                return Db::getInstance()->executeS($sql);

        }

	public function maxProductId()
        {
                $sql = new DbQuery();
                $sql->from('product');
                $sql->select('max(id_product) as product_id');

                return Db::getInstance()->executeS($sql)[0]['product_id'];
        }

	public function deleteProduct($product_id){
		$product = new ProductCore($productId, false);
		return $product->delete();
	}


}

?>
