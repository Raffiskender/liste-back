<?php

namespace Liste_de_course;

use Liste_de_course\Login\Google\GoogleLogin;
use Liste_de_course\Models\JsonTable;
use Liste_de_course\Models\UserConfirme;
use Liste_de_course\Models\User;
use Liste_de_course\Models\UserResetPassword;

class RoutesAPI
{
  private const ROUTE_NAME = 'liste-de-course/v1';
  
	public static function create_API_routes(){
		//* Les routes en GET
		$jsonTable = new JsonTable;
		$userConfirm = new UserConfirme;
    $user = new User;
    $resetPassword = new UserResetPassword;
    $googleLogin = new GoogleLogin;
		//* ROUTES USERS
		
		//* ROUTE CRÉATION USER
		register_rest_route( self::ROUTE_NAME, 'create-user', [
			'methods' => 'POST',
			'callback' => [User::class, 'onCreate'],
			],
		);
    
    //* SUPRIMER UN UTILISATEUR
		register_rest_route( self::ROUTE_NAME, 'delete-user', [
			'methods' => 'DELETE',
			'callback' => [$user, 'deleteUser'],
			],
		);
    

		//* ROUTES VERIFICATION USERS
		//* POST
		register_rest_route( self::ROUTE_NAME, 'confirm', [
			'methods' => 'POST',
			'callback' => [$userConfirm, 'confirmeUser'],
			],
		);
    
		//* RECUPERATION DE MOT DE PASSE
    //* POST
		register_rest_route( self::ROUTE_NAME, 'askForPasswordReset', [
			'methods' => 'POST',
			'callback' => [$resetPassword, 'sendResetLink'],
			],
		);
    //* CHANGEMENT DU MOT DE PASSE
		register_rest_route( self::ROUTE_NAME, 'resetPassword', [
			'methods' => 'POST',
			'callback' => [$resetPassword, 'setNewPassword'],
			],
		);
		register_rest_route( self::ROUTE_NAME, 'resetPasswordFromProfil', [
			'methods' => 'POST',
			'callback' => [$resetPassword, 'setNewPasswordFromProfilPage'],
			],
		);
    
		//* MODIFICATION DES NOMS / PRENOMS
    
		register_rest_route( self::ROUTE_NAME, 'changeLastName', [
			'methods' => 'POST',
			'callback' => [$user, 'changeLastName'],
			],
		);
		// //* ROUTES GOOGLE
		register_rest_route( self::ROUTE_NAME, 'googleLogin', [
			'methods' => 'POST',
			'callback' => [$googleLogin, 'loginWithGoogle' ],
		]);
		
		 //* ROUTES JSON
		register_rest_route( self::ROUTE_NAME, 'data/(?P<userId>\d+)', [
			'methods' => 'GET',
		 	'callback' => [$jsonTable, 'findByUser'],
		 	],
		);
		
		register_rest_route( self::ROUTE_NAME, 'data/(?P<userId>\d+)', [
			'methods' => 'PUT',
			'callback' => [$jsonTable, 'updateJson'],
			],
		);
		
		register_rest_route( self::ROUTE_NAME, 'data', [
			'methods' => 'POST',
			'callback' => [$jsonTable, 'createJson'],
			],
		);
		
		//TODO Les routes en PATCH
	
	}
}