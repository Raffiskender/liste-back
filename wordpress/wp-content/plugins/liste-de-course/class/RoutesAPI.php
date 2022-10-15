<?php

namespace Liste_de_course;

use Liste_de_course\Models\Correspondance;

class RoutesAPI
{
	public static function create_API_routes(){
		//* Les routes en GET
		$correspondance = new Correspondance;
		

		register_rest_route( 'liste-de-course/v1', 'correspondance',[
			'methods' => 'GET',
			'callback' => [$correspondance, 'findAll'],
			],
		);
		
		register_rest_route( 'liste-de-course/v1', 'correspondance/list-element/(?P<listElementId>\d+)', [
			'methods' => 'GET',
			'callback' => [$correspondance, 'findByListElement'],
			],
		);
		
		register_rest_route( 'liste-de-course/v1', 'correspondance/rubrique/(?P<rubriqueId>\d+)', [
			'methods' => 'GET',
			'callback' => [$correspondance, 'findByRubrique'],
			],
		);
		
		//* Les routes en POST
		register_rest_route( 'liste-de-course/v1', 'correspondance', [
			'methods' => 'POST',
			'callback' => [$correspondance, 'addNewCorrespondance'],
			],
		);
		
		//* Les routes en DELETE
		register_rest_route( 'liste-de-course/v1', 'correspondance/list-element/(?P<listElementId>\d+)', [
			'methods' => 'DELETE',
			'callback' => [$correspondance, 'deleteOneListElement'],
			],
		);
		
		register_rest_route( 'liste-de-course/v1', 'correspondance/rubrique/(?P<rubriqueId>\d+)', [
			'methods' => 'DELETE',
			'callback' => [$correspondance, 'deleteOneRubrique'],
			],
		);
		
		//TODO Les routes en PATCH
	
	}
}