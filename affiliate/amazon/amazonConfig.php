<?php

class AmazonConfig 
{

	private $this_module = false;


	public function __construct($mod)
        {	
		$this->this_module = $mod;
         
	}

	public function getAmazonContent()
	{

		$output = null;

		if (Tools::isSubmit('amazonconfigsubmit'.$this->this_module->name))
		{

			$access_key = strval(Tools::getValue('TurboeCom_amazon_access_key'));
			$secret_key = strval(Tools::getValue('TurboeCom_amazon_secret_key'));
			$affiliate_id = strval(Tools::getValue('TurboeCom_amazon_affiliate_id'));

			if (!empty($access_key))
			{
				Configuration::updateValue('TurboeCom_amazon_access_key', $access_key);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Access key can not be empty'));
			}

			if (!empty($secret_key))
			{
				Configuration::updateValue('TurboeCom_amazon_secret_key', $secret_key);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Secret key can not be empty'));
			}

			if (!empty($affiliate_id))
			{
				Configuration::updateValue('TurboeCom_amazon_affiliate_id', $affiliate_id);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Affiliate key can not be empty'));
			}
		}

		if (Tools::isSubmit('amazonsearchsubmit'.$this->this_module->name))
		{
			$access_key = NULL;
			$secret_key = NULL;
			$affiliate_id = NULL;
			$is_valid = true;
			$amazon_category = strval(Tools::getValue('TurboeCom_amazon_category'));
			$search_keyword = strval(Tools::getValue('TurboeCom_amazon_keyword'));
			$amazon_page = strval(Tools::getValue('TurboeCom_amazon_fetch_count'));
			$prestashop_category = strval(Tools::getValue('TurboeCom_amazon_prestashop_category'));

			if(empty($amazon_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Amazon Category'));
				$is_valid = false;
			}

			if(empty($search_keyword)){
				$output .= $this->this_module->displayError($this->this_module->l('Search keyword can not be empty'));
				$is_valid = false;
			}

			if(empty($amazon_page)){
				$output .= $this->this_module->displayError($this->this_module->l('Please enter the page number'));
				$is_valid = false;
			}else if(!is_numeric($amazon_page)){
				$output .= $this->this_module->displayError($this->this_module->l('Page number to be fetched is not numeric'));
				$is_valid = false;
			}

			if(empty($prestashop_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Prestashop Category'));
				$is_valid = false;
			}

			if($is_valid){
				$access_key = Configuration::get('TurboeCom_amazon_access_key');
				$secret_key = Configuration::get('TurboeCom_amazon_secret_key');
				$affiliate_id = Configuration::get('TurboeCom_amazon_affiliate_id');


				if(empty($access_key) || empty($secret_key) || empty($affiliate_id)){
					$output .= $this->this_module->displayError($this->this_module->l('Missing configuration. Please set Amazon setting'));
					$is_valid = false;
				}
			}

			if($is_valid){
				$amazon = new amazonAPI($access_key, $secret_key, $affiliate_id);

				$arr = array();

					$arr = $amazon->searchProductHelper($search_keyword, $amazon_category, $amazon_page);
					$displayContent = null;

                                        $displayContent .= "<div id='content' class='bootstrap'><br><br><br><div><button type='button' class='btn btn-success btn-lg' onclick='nextAmazon(this)'>Next Page</button></div><br><table id='save-product' class='table table-bordered table-hover'><thead><tr><th>Image</th><th>Name</th><th>Description</th><th>Action</th><th class='hidden'>Content</th></tr></thead><tbody>";

					$count = 0;
					foreach($arr as $p){
						$count++;
						$short_description = "<ul>";

							$des_count = 0;
                                                        foreach($p['description'] as $desc){
								$des_count++;
                                                                $desc = trim($desc);
                                                                $short_description .= "<li>{$desc}</li>";
                                                        		
								if($des_count==3){
									break;
								}
							}

                                                        $short_description .="</ul><br><br>";
                                                        $short_description .= "<p><a href={$p['link']} target='_blank' class='btn btn-default'>BUY NOW</a></p>";

							$array_send = array();
							$array_send['name'] = str_replace('"', '-inch', $p['name']);

							$array_send['short_description'] = $short_description;
							$array_send['images'] = $p['images'];
							$array_send['prestashop_category'] = $prestashop_category;
							$array_send['price'] = $p['price'];
							$array_send['asin'] = $p['asin'];
							$array_send['website'] = 'amazon.com';

							$json_send = json_encode($array_send, true);

                                                        $displayContent .= "<tr id='{$count}'><td><a class = 'thumbnail' target='_blank' href='{$p['images']}'><img src='{$p['images']}' style='height:100px;' class='img-thumbnail' alt='NA' ></a></td><td>{$p['name']}</td><td>{$short_description}</td><td><button type='button' class='btn btn-danger delete-row' onclick='deleteRow(this)'>Remove</button>  <button type='button' class='btn btn-primary' data-loading-text=\"<i class='icon-spinner icon-spin icon-large'></i>\" id='{$count}_add_button' onclick='addRow({$count}, this)'>Add</button></td><td class='hidden' id='data_{$count}'>{$json_send}</td></tr>";
					}
					$displayContent .= "</tbody><div></table><div><button type='button' class='btn btn-success btn-lg' onclick='nextAmazon(this)'>Next Page</button></div></div>";


				Configuration::updateValue('amazon_page_number', $amazon_page+1);
				Configuration::updateValue('amazon_keyword', $search_keyword);
                 		Configuration::updateValue('amazon_prestashop_category', $prestashop_category);
                 		Configuration::updateValue('amazon_category', $amazon_category);


                        echo $displayContent;
				
			}
		}
		return $output;
	}

	public function checkConfiguration()
	{
		$config = Configuration::getMultiple(array(
					'TurboeCom_amazon_access_key',
					'TurboeCom_amazon_secret_key',
					'TurboeCom_amazon_affiliate_id'
					));

		if (array_key_exists('TurboeCom_amazon_access_key', $config)
				&& array_key_exists('TurboeCom_amazon_secret_key', $config)
				&& array_key_exists('TurboeCom_amazon_affiliate_id', $config)
		   )
		{
			if (empty($config['TurboeCom_amazon_access_key']))
				$this->this_module->warning = $this->this_module->l('Please provide Amazon Access Key');

			if (empty($config['TurboeCom_amazon_secret_key']))
				$this->this_module->warning = $this->this_module->l('Please provide Amazon Secret Key');

			if (empty($config['TurboeCom_amazon_affiliate_id']))
				$this->this_module->warning = $this->this_module->l('Please provide Affiliate Id');


		}else{
			$this->this_module->warning = $this->this_module->l('Missing configuration. Please configure module ' . $this->this_module->class_module_name);
		}
	}

	public function amazonForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$presta_category = array();
		$fetched_categ = $this->this_module->fetchPrestashopCategory();

		foreach ($fetched_categ as $categ)
		{
			$presta_category[] = array( 
					"id_option" => (int)$categ['id_category'],
					"name" => $categ['name']
					);
		}

		$amazon_category = array();
		$fetched_categ = $this->this_module->fetchAffiliateCategory('amazon.com');

		foreach ($fetched_categ as $categ)
		{
			$amazon_category[] = array( 
					"id_option" => $categ['category_name'],
					"name" => $categ['category_name']
					);
		}

		$fields_form[0]['form'] = array(
				'legend' => array( 
					'title' => $this->this_module->l('Amazon Fetch Product'),
					),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->this_module->l('Search in Category'),
						'desc' => $this->this_module->l('Select Amazon Category'),
						'name' => 'TurboeCom_amazon_category',
						'required' => true,
						'options' => array(
							'query' => $amazon_category,
							'id' => 'id_option',
							'name' => 'name'
							)
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Search keyword for Amazon'),
						'name' => 'TurboeCom_amazon_keyword',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Start page for pagination'),
						'desc' => $this->this_module->l('Do not change if you are not clear'),
						'name' => 'TurboeCom_amazon_fetch_count',
						'required' => true
					     ),
					array(
							'type' => 'select',
							'label' => $this->this_module->l('Save in Category'),
							'desc' => $this->this_module->l('Choose your store category'),
							'name' => 'TurboeCom_amazon_prestashop_category',
							'required' => true,
							'options' => array(
								'query' => $presta_category,
								'id' => 'id_option',
								'name' => 'name'
								)
					     )
						),
					'submit' => array(
							'title' => $this->this_module->l('Save'),
							'class' => 'btn btn-default pull-right',
							'id' => 'amazonProductSaveMaster'
							)
						);


		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this->this_module;
		$helper->name_controller = $this->this_module->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->this_module->name;
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->this_module->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'amazonsearchsubmit'.$this->this_module->name;

		$helper->toolbar_btn = array(
				'save' =>
				array(
					'desc' => $this->this_module->l('Fetch'),
					'href' => AdminController::$currentIndex.'&configure='.$this->this_module->name.'&save'.$this->this_module->name.
					'&token='.Tools::getAdminTokenLite('AdminModules'),
				     ),
				'back' => array(
					'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'desc' => $this->this_module->l('Back to list')
					)
				);

		$helper->fields_value['TurboeCom_amazon_fetch_count'] = Configuration::get('amazon_page_number');
		$helper->fields_value['TurboeCom_amazon_keyword'] = Configuration::get('amazon_keyword');
		 $helper->fields_value['TurboeCom_amazon_prestashop_category'] = Configuration::get('amazon_prestashop_category');
		 $helper->fields_value['TurboeCom_amazon_category'] = Configuration::get('amazon_category');

		return $helper->generateForm($fields_form);
	}


	public function amazonConfig()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->this_module->l('Amazon Setting'),
					),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Amazon Access Key Id (for amazon.com)'),
						'name' => 'TurboeCom_amazon_access_key',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Amazon Secret Key Id (for amazon.com)'),
						'name' => 'TurboeCom_amazon_secret_key',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Amazon Affiliate Id (for amazon.com)'),
						'name' => 'TurboeCom_amazon_affiliate_id',
						'required' => true
					     )
					),
				'submit' => array(
						'title' => $this->this_module->l('Save'),
						'class' => 'btn btn-default pull-right'
						)
					);


		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this->this_module;
		$helper->name_controller = $this->this_module->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->this_module->name;
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->this_module->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'amazonconfigsubmit'.$this->this_module->name;
		$helper->toolbar_btn = array(
				'save' =>
				array(
					'desc' => $this->this_module->l('Save'),
					'href' => AdminController::$currentIndex.'&configure='.$this->this_module->name.'&save'.$this->this_module->name.
					'&token='.Tools::getAdminTokenLite('AdminModules'),
				     ),
				'back' => array(
					'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'desc' => $this->this_module->l('Back to list')
					)
				);

		// Load current value
		$helper->fields_value['TurboeCom_amazon_access_key'] = Configuration::get('TurboeCom_amazon_access_key');
		$helper->fields_value['TurboeCom_amazon_secret_key'] = Configuration::get('TurboeCom_amazon_secret_key');
		$helper->fields_value['TurboeCom_amazon_affiliate_id'] = Configuration::get('TurboeCom_amazon_affiliate_id');


		return $helper->generateForm($fields_form);
	}


}

?>
