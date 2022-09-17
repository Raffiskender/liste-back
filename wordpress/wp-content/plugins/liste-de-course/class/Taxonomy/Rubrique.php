<?php

namespace Liste_de_course\Taxonomy;

use Liste_de_course\CPT\ListElement;

class Rubrique
{
	const SLUG = "rubrique";

	static public function register()
	{
		register_taxonomy(
			self::SLUG,
			[ListElement::SLUG],
			[
				"label"        => "Rubrique",
				"hierarchical" => false,
				"public"       => false,
				"show_in_rest" => true,
			]
		);
	}
	
	public static function unregister(){
		unregister_taxonomy(self::SLUG);
	}

}