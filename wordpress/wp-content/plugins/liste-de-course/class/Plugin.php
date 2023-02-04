<?php

namespace Liste_de_course;

use Liste_de_course\Models\JsonTable;
use Liste_de_course\Users\UserConfirme;

class Plugin{
	
	private $jsonTable;
	
	// Construction du plugin
	function __construct()
	{
		$this->jsonTable = new JsonTable;
		register_activation_hook( LISTE_DE_COURSE_ENTRY_FILE, [$this, "onActivation"] );
		register_deactivation_hook( LISTE_DE_COURSE_ENTRY_FILE, [$this, "onDeactivation"] );
		add_action('user_register', [UserConfirme::class, 'CreateNonConfirmedUser'] );
    add_action('delete_user', [$this->jsonTable, 'deleteUserJsonData']);
		add_action('init', [$this, 'onInit']);
		add_action('rest_api_init', [$this, 'onApiInit']);
	}
		
	public function onApiInit()
	{
		RoutesAPI::create_API_routes();		
	}
	
	public function onInit()
  {
    header("Access-Control-Allow-Origin: *");
	}
	
	public function onActivation()
	{	
		$this->jsonTable->createTable();
	}
	
	public function onDeactivation()
	{
		//* if you want to throw up all datas when deactivate the plugin, then uncomment this line
		$this->jsonTable->deleteTable();
	}
}