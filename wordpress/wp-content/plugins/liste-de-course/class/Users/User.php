<?php

namespace Liste_de_course\Users;

use WP_Error;
use Liste_de_course\Models\JsonTable;

class User
{
	public static function onCreate($params)
	{
    $username = $params['username'];
		$email = $params['email'];
		$password = $params['password'];
    
		if (!is_email($email)) {
      return new WP_Error(
        'Invalid email',
        __('Sorry, this email is not valid !'),
        array( 'data' => null )
      );
		}
		
		// create user and retrieve ID
		return wp_create_user( $username, $password, $email );
		
    //*DEPRECATED : Let the user be a suscriber...
		// $user = new WP_User($response);
		// //remove default role
		// $user->remove_role('subscriber');
		// // adding author role.
		// $user->add_role('author');
		
    //return $response;
	}
  public static function onDelete($id){
    $jsonTable = new JsonTable;
    $jsonTable->deleteUserJsonData($id);
  }
  
}