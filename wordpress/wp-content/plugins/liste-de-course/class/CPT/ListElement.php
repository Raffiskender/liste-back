<?php

namespace Liste_de_course\CPT;

class ListElement
{
	public const SLUG = 'list_element';

	public const CAPABILITIES = [
			'edit_posts'             => 'edit_list_elements',
			'edit_post'              => 'edit_list_element',
			'publish_posts'          => 'publish_list_elements',
			'publish_post'           => 'publish_list_element',
			'edit_published_posts'   => 'edit_published_list_elements',
			'edit_published_post'    => 'edit_published_list_elements',
			//'read_posts'             => 'read_list_elements',
			'read_post'              => 'read_list_element',
			'delete_post'            => 'delete_list_element',
			'delete_posts'           => 'delete_list_elements',
			//'edit_others_posts'      => 'edit_others_list_elements',
			//'edit_others_post'       => 'edit_others_list_element',
			//'delete_others_posts'    => 'delete_others_list_elements',
			//'delete_others_post'     => 'delete_others_list_element',
			'delete_published_posts' => 'delete_published_list_elements',
	];

	public static function register()
	{
		register_post_type(
				self::SLUG,
				[
						'label'        => "Element",
						'description'  => "Un élément de la liste",
						'menu_icon'    => 'dashicons-welcome-add-page',
						'map_meta_cap' => true,
						'capabilities' => self::CAPABILITIES,
						'supports'     => [
								"title",
								"author",
						],
						'show_in_rest' => true,
						'public'       => true,
				]
		);   
	}
	
	static public function unregister()
	{
			unregister_post_type( self::SLUG );
	}
}