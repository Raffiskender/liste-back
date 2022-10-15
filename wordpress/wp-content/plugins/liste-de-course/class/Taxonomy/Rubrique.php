<?php

namespace Liste_de_course\Taxonomy;

use Liste_de_course\CPT\ListElement;

class Rubrique
{
	const SLUG = "rubrique";
	const PLURIAL_SLUG = "rubriques";
	const CAPABILITIES = [
	 	'manage_categories' => 'manage_' . self::PLURIAL_SLUG,
    //'edit_terms' => 'edit_' . self::SLUG,
  //   'delete_terms' => 'delete_' . self::SLUG,
  //   'assign_terms' => 'assign_' . self::SLUG,
	 ];
	
	static public function register()
	{
		register_taxonomy(
			self::SLUG,
			[ListElement::SLUG],
			[
				"label"        => "Rubrique",
				"hierarchical" => true,
				"public"       => false,
				"show_in_rest" => true,
				'capabilities'  => self::CAPABILITIES,
			]
		);
	}
	
	public static function unregister(){
		unregister_taxonomy(self::SLUG);
	}
}