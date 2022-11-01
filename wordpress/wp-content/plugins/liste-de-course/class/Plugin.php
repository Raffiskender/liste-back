<?php

namespace Liste_de_course;

use Liste_de_course\CPT\ListElement;
//use Liste_de_course\Models\Correspondance;
//use Liste_de_course\Taxonomy\Rubrique;
//use Liste_de_course\Taxonomy\Urgence;
use Liste_de_course\Users\UserConfirme;

class Plugin{
	
	// Construction du plugin
	function __construct()
	{
		
		register_activation_hook( LISTE_DE_COURSE_ENTRY_FILE, [$this, "onActivation"] );
		register_deactivation_hook( LISTE_DE_COURSE_ENTRY_FILE, [$this, "onDeactivation"] );
		add_action( 'user_register', [UserConfirme::class, 'CreateNonConfirmedUser'] );
		add_action('init', [$this, 'onInit']);
		add_action('rest_api_init', [$this, 'onApiInit']);
	}
	
	
	public function onApiInit(){
		RoutesAPI::create_API_routes();
		//register_rest_field(ListElement::SLUG, 'readableTitle', "coucou");
	}
	
	public function onInit()
	{
		//Création des CPT au hook init
		ListElement::register();
		//Urgence::register();
	}
	
	public function onActivation()
	{
		//$this->correspondance->createTable();

		ListElement::grantCapsToAuthor();
	}
	
	public function onDeactivation()
	{
		//$this->correspondance->deleteTable();
		ListElement::unregister();
		ListElement::removeCapsToAuthor();
		//Rubrique::unregister();
		//Urgence::unregister();
	}
}