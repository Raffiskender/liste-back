<?php

namespace Liste_de_course\Taxonomy;

use Liste_de_course\CPT\ListElement;

class Urgence
{
	const SLUG = "urgence";

	static public function register()
	{
		register_taxonomy(
			self::SLUG,
			[ListElement::SLUG],
			[
				"label"        => "Urgence",
				"hierarchical" => false,
				"public"       => true,
				"show_in_rest" => true,
			]
		);
	}
	
	public static function unregister(){
		unregister_taxonomy(self::SLUG);
	}

}