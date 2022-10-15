<?php

namespace Liste_de_course\CPT;

use Liste_de_course\Taxonomy\Rubrique;
use Liste_de_course\Taxonomy\Urgence;

class ListElement
{
	public const SLUG = 'list_element';

	public const CAPABILITIES = [
		//'manage_' . Rubrique::CAPABILITIES[1],
		//'manage' . Urgence::CAPABILITIES[0],
		
    //'edit_terms',
		//'edit_rubrique',
    //'delete_terms',
    //'assign_terms',
	];

	public static function register()
	{
		register_post_type(
			self::SLUG,
			[
				'label'        => "Element",
				'description'  => "Un élément de la liste",
				'menu_icon'    => 'dashicons-welcome-add-page',
				'supports'     => [
						"title",
						"author",
				],
				'show_in_rest' => true,
				'public'       => false,
			]
		);   
	}
		public static function grantCapsToAuthor()
	{
			//* 1- We recover the author role
			$authorRole = get_role('author');
			//$authorRole->add_cap('manage_categories');
			//* 2- We add our roles to the author roles
			//var_dump(self::CAPABILITIES); die;
			//foreach (self::CAPABILITIES as $currentCustomCapability) {
				//	$authorRole -> add_cap($currentCustomCapability, true);
			//}
	}
	
	public static function removeCapsToAdmin()
	{
		//* 0- Table af capa we want to delete :
		// $capasToRemove=[
		// 	'read_posts'             => 'read_list_elements',
		// 	'edit_others_posts'      => 'edit_others_list_elements',
		// 	'edit_others_post'       => 'edit_others_list_element',
		// 	'delete_others_posts'    => 'delete_others_list_elements',
		// 	'delete_others_post'     => 'delete_others_list_element',
		// 	'read_others_posts'      => 'read_others_list_elements'    ,
		// 	'read_others_posts',
		// 	'edit_others_posts'   ,
		// 	'edit_others_post'    ,
		// 	'delete_others_posts' ,
		// 	'delete_others_post'  ,
			
		// ];
		
    // //* 1- We recover the author role
    // $adminRole = get_role('admin');
    // //* 2- We add our roles to the admin roles
    // foreach ($capasToRemove as $currentCustomCapability) {
    //     $adminRole -> add_cap($currentCustomCapability, false);
    // }
	}
	public static function removeCapsToAuthor()
	{
			//* 1- We recover the author role
			$authorRole = get_role('author');
			//* 2- We add our roles to the author roles
			foreach (self::CAPABILITIES as $currentCustomCapability) {
					$authorRole -> remove_cap($currentCustomCapability);
			}
	}

	static public function unregister()
	{
			unregister_post_type( self::SLUG );
	}
}