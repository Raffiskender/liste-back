<?php

namespace Liste_de_course\Models;

use WP_Error;
use Liste_de_course\Models\JsonTable;

class User extends CoreModel
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
  
  public function deleteUser(){
    $tokenValidation = $this->validate_token();
    
    if (!is_wp_error($tokenValidation))
		{
      $userIdFromToken = $tokenValidation['data']['user'];
      require_once(ABSPATH . 'wp-admin/includes/user.php');
      wp_delete_user($userIdFromToken);
      return "1";
    }
    else return $tokenValidation;
    
  }
  public function changeLastName($params){
    $lastName = $params['lastName'];
    $tokenValidation = $this->validate_token();
    
    if (!is_wp_error($tokenValidation)){
      wp_update_user([
        'ID' => $tokenValidation['data']['user'],
        'display_name' => $lastName,
      ]);
      return '1';
    }
    else return $tokenValidation;
    
  }
}