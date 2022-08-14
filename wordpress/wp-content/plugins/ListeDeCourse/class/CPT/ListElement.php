<?php

namespace Liste_de_course\CPT;

class ListElement
{
	const SLUG = 'list_element';

	static public function register()
	{
		register_post_type(
			self::SLUG, [
			'label'           => "Element",
			'description'     => "un élément à ajouter dans la liste",
			'menu_icon'       => 'dashicons-welcome-add-page',
			//'capability_type' => self::SLUG,
			//'show_ui'         => true,
			//'show_in_menu'    => true,
			'capabilities'    => [
				'create_posts'    => true
				],
			//'map_meta_cap'    => true,
			'supports'        => [
					"title",
					"author",
			],
			'show_in_rest' => true,
			'public'       => true,
		]);
	}
	static public function unregister()
	{
			unregister_post_type( self::SLUG );
	}
}