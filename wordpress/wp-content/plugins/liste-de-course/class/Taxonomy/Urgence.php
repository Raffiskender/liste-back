<?php

namespace Liste_de_course\Taxonomy;

use Liste_de_course\CPT\ListElement;

class Urgence
{
	const SLUG = "urgence";
	const PLURIAL_SLUG = "urgences";
	const CAPABILITIES = [
	 	'manage_categories' => 'manage_' . self::PLURIAL_SLUG,
	];
	
	static public function register()
	{
		register_taxonomy(
			self::SLUG,
			[ListElement::SLUG],
			[
				"label"        => "Urgence",
				"hierarchical" => true,
				"public"       => true,
				"show_in_rest" => true,
			]
		);
	}
	
	public static function unregister(){
		unregister_taxonomy(self::SLUG);
	}

}