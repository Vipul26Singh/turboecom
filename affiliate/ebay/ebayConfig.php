<?php

class EbayConfig
{

	private $this_module = false;


        public function __construct($mod)
        {
                $this->this_module = $mod;

        }


	public function checkConfiguration()
        {
                $config = Configuration::getMultiple(array(
                                                'TurboeCom_ebay_app_id',
                                                'TurboeCom_ebay_campaign_id'
                                                ));

                        if (array_key_exists('TurboeCom_ebay_app_id', $config)
                                        && array_key_exists('TurboeCom_ebay_campaign_id', $config)
                           )
                        {
                                if (empty($config['TurboeCom_ebay_app_id']))
                                        $this->this_module->warning = $this->this_module->l('Please provide Ebay App Id');

                                if (empty($config['TurboeCom_ebay_campaign_id']))
                                        $this->this_module->warning = $this->this_module->l('Please provide Ebay Campaign Id');


                        }else{
                                $this->this_module->warning = $this->this_module->l('Missing configuration. Please configure module ' . $this->this_module->class_module_name);
                        }

        }

	public function getEbayContent()
	{

		$output = null;

		if (Tools::isSubmit('ebayconfigsubmit'.$this->this_module->name))
		{

			$app_id = strval(Tools::getValue('TurboeCom_ebay_app_id'));
			$campaign_id = strval(Tools::getValue('TurboeCom_ebay_campaign_id'));

			if (!empty($app_id))
			{
				Configuration::updateValue('TurboeCom_ebay_app_id', $app_id);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('App Id can not be empty'));
			}

			if (!empty($campaign_id))
			{
				Configuration::updateValue('TurboeCom_ebay_campaign_id', $campaign_id);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Campaign id can not be empty'));
			}
		}

		if (Tools::isSubmit('ebaysearchsubmit'.$this->this_module->name))
		{
			$app_id = NULL;
			$campaign_id = NULL;
			$is_valid = true;
			$ebay_category = strval(Tools::getValue('TurboeCom_ebay_category'));
			$search_keyword = strval(Tools::getValue('TurboeCom_ebay_keyword'));
			$ebay_page = strval(Tools::getValue('TurboeCom_ebay_fetch_count'));
			$prestashop_category = strval(Tools::getValue('TurboeCom_ebay_prestashop_category'));

			if(empty($ebay_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Ebay Category'));
				$is_valid = false;
			}

			if(empty($search_keyword)){
				$output .= $this->this_module->displayError($this->this_module->l('Search keyword can not be empty'));
				$is_valid = false;
			}

			if(empty($ebay_page)){
				$output .= $this->this_module->displayError($this->this_module->l('Please enter page number'));
				$is_valid = false;
			}else if(!is_numeric($ebay_page)){
				$output .= $this->this_module->displayError($this->this_module->l('Page number is not numeric'));
				$is_valid = false;
			}

			if(empty($prestashop_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Prestashop Category'));
				$is_valid = false;
			}

			if($is_valid){
				$app_id = Configuration::get('TurboeCom_ebay_app_id');
				$campaign_id = Configuration::get('TurboeCom_ebay_campaign_id');

				if(empty($app_id) || empty($campaign_id)){
					$output .= $this->this_module->displayError($this->this_module->l('Missing configuration. Please set Ebay setting'));
					$is_valid = false;
				}
			}

			if($is_valid)
			{
				$ebay = new ebayAPI($app_id, $campaign_id);

				$arr = array();
				$arr = $ebay->searchProductHelper($search_keyword, $ebay_category, $ebay_page);
				$displayContent = null;

                                $displayContent .= "<div id='content' class='bootstrap'><br><br><br><div><button type='button' class='btn btn-success btn-lg' onclick='nextEbay(this)'>Next Page</button></div><br><table id='save-product' class='table table-bordered table-hover'><thead><tr><th>Image</th><th>Name</th><th>Description</th><th>Action</th><th class='hidden'>Content</th></tr></thead><tbody>";


				$count = 0;
                                        foreach($arr as $p){

                                                $count++;
                                                $short_description = "<ul>";

                                                        $des_count = 0;

							if(!empty($p['description'])){
								foreach($p['description'] as $desc){
									$des_count++;
									$desc = trim($desc);
									$short_description .= "<li>{$desc}</li>";

									if($des_count==3){
										break;
									}
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
                                                        $array_send['website'] = 'ebay.com';
							
                                                        $json_send = json_encode($array_send, true);

                                                        $displayContent .= "<tr id='{$count}'><td><a class = 'thumbnail' target='_blank' href='{$p['images']}'><img src='{$p['images']}' style='height:100px;' class='img-thumbnail' alt='NA' ></a></td><td>{$p['name']}</td><td>{$short_description}</td><td><button type='button' class='btn btn-danger delete-row' onclick='deleteRow(this)'>Remove</button>  <button type='button' class='btn btn-primary' data-loading-text=\"<i class='icon-spinner icon-spin icon-large'></i>\" id='{$count}_add_button' onclick='addRow({$count}, this)'>Add</button></td><td class='hidden' id='data_{$count}'>{$json_send}</td></tr>";
                                        }
                                        $displayContent .= "</tbody><div></table><div><button type='button' class='btn btn-success btn-lg' onclick='nextEbay(this)'>Next Page</button></div></div>";

					Configuration::updateValue('ebay_page_number', $ebay_page+1);
                                Configuration::updateValue('ebay_keyword', $search_keyword);
                                Configuration::updateValue('ebay_prestashop_category', $prestashop_category);
                                Configuration::updateValue('ebay_category', $ebay_category);
				echo $displayContent;
			}
		}
		return $output;
	}


	public function ebayForm()
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

		$ebay_category = array();
		$fetched_categ = $this->this_module->fetchAffiliateCategory('ebay.com');

		foreach ($fetched_categ as $categ)
		{
			$ebay_category[] = array( 
					"id_option" => $categ['category_id'],
					"name" => $categ['category_name']
					);
		}

		$fields_form[0]['form'] = array(
				'legend' => array( 
					'title' => $this->this_module->l('Ebay Fetch Product'),
					),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->this_module->l('Search in Category'),
						'desc' => $this->this_module->l('Select Ebay Category'),
						'name' => 'TurboeCom_ebay_category',
						'required' => true,
						'options' => array(
							'query' => $ebay_category,
							'id' => 'id_option',
							'name' => 'name'
							)
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Search keyword for Ebay'),
						'name' => 'TurboeCom_ebay_keyword',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Start page for pagination'),
                                                'desc' => $this->this_module->l('Do not change if you are not clear'),
						'name' => 'TurboeCom_ebay_fetch_count',
						'required' => true
					     ),
					array(
							'type' => 'select',
							'label' => $this->this_module->l('Save in Category'),
							'desc' => $this->this_module->l('Choose your store category'),
							'name' => 'TurboeCom_ebay_prestashop_category',
							'required' => true,
							'options' => array(
								'query' => $presta_category,
								'id' => 'id_option',
								'name' => 'name'
								)
					     )
						),
					'submit' => array(
							'id' => 'ebayProductSaveMaster',
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
		$helper->submit_action = 'ebaysearchsubmit'.$this->this_module->name;

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


		 $helper->fields_value['TurboeCom_ebay_fetch_count'] = Configuration::get('ebay_page_number');
                $helper->fields_value['TurboeCom_ebay_keyword'] = Configuration::get('ebay_keyword');
                 $helper->fields_value['TurboeCom_ebay_prestashop_category'] = Configuration::get('ebay_prestashop_category');
                 $helper->fields_value['TurboeCom_ebay_category'] = Configuration::get('ebay_category');

		return $helper->generateForm($fields_form);
	}

	public function ebayConfig()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->this_module->l('Ebay Setting'),
					),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Ebay App Id (for ebay.com)'),
						'name' => 'TurboeCom_ebay_app_id',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Ebay Campaign Id (for ebay.com)'),
						'name' => 'TurboeCom_ebay_campaign_id',
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
		$helper->submit_action = 'ebayconfigsubmit'.$this->this_module->name;
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
		$helper->fields_value['TurboeCom_ebay_app_id'] = Configuration::get('TurboeCom_ebay_app_id');
		$helper->fields_value['TurboeCom_ebay_campaign_id'] = Configuration::get('TurboeCom_ebay_campaign_id');


		return $helper->generateForm($fields_form);
	}

}

?>
