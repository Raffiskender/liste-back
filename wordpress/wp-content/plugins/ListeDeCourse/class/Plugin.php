<?php

namespace Liste_de_course;

use Liste_de_course\CPT\ListElement;

class Plugin{
	// Construction du plugin
	function __construct()
	{
		
		register_activation_hook( LISTE_DE_COURSE_ENTRY_FILE, [$this, "onActivation"] );
		register_deactivation_hook( LISTE_DE_COURSE_ENTRY_FILE, [$this, "onDeactivation"] );
		add_action('init', [$this, 'onInit']);
	}
	
	public function onInit()
	{
		//Création des CPT au hook init
		ListElement::register();
	}
	
	public function onActivation()
	{
		//
	}
	
	public function onDeactivation()
	{
		ListElement::unregister();
	}
}