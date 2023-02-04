<?php

namespace Liste_de_course\Models;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use WP_Error;

class CoreModel
{
  protected $wpdb;
  public function __construct()
  {
    global $wpdb;
    $this->wpdb = $wpdb;
  }
		
  public function validate_token()
  {
  	$output = true;
		$auth = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : false;
		
    /* Double check for different auth header string (server dependent) */
    if (!$auth) {
      $auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : false;
    }
    if (!$auth) {
      return new WP_Error(
        'jwt_auth_no_auth_header',
        'Authorization header not found.',
        array(
          'status' => 403,
        )
      );
    }

    /*
    * The HTTP_AUTHORIZATION is present verify the format
    * if the format is wrong return the user.
    */
				 
    list($token) = sscanf($auth, 'Bearer %s');
    if (!$token) {
      return new WP_Error(
        'jwt_auth_bad_auth_header',
        'Authorization header malformed.',
        array(
          'status' => 403,
        )
      );
    }

    /** Get the Secret Key */
    $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;		
    if (!$secret_key) {
      return new WP_Error(
        'jwt_auth_bad_config',
        'JWT is not configurated properly, please contact the admin',
        array(
          'status' => 403,
        )
      );
    }
		//return "JWT::decode($token, $secret_key, 'HS256')" ;
    /** Try to decode the token */
    try {
      $token = JWT::decode($token, new Key($secret_key, 'HS256'));
      /** The Token is decoded now validate the iss */
      if ($token->iss != get_bloginfo('url')) {
        /** The iss do not match, return error */
        return new WP_Error(
          'jwt_auth_bad_iss',
          'The iss do not match with this server',
          array(
            'status' => 403,
          )
        );
      }
      /** So far so good, validate the user id in the token */
      if (!isset($token->data->user->id)) {
        /** No user id in the token, abort!! */
        return new WP_Error(
          'jwt_auth_bad_request',
          'User ID not found in the token',
          array(
            'status' => 403,
          )
        );
      }
      /** Everything looks good return the decoded token if the $output is false */
      if (!$output) {
        return $token;
      }
      /** If the output is true return an answer to the request to show it */
      return array(
        'code' => 'jwt_auth_valid_token',
        'data' => array(
        'status' => 200,
	    		'user' => $token->data->user->id,
        ),
      );
    } catch (Exception $e) {
      /** Something is wrong trying to decode the token, send back the error */
      return new WP_Error(
        'jwt_auth_invalid_token',
        $e->getMessage(),
        array(
          'status' => 403,
        )
      );
    }
	}
}