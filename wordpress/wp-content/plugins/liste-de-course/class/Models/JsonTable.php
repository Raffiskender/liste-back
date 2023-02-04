<?php

namespace Liste_de_course\Models;

use WP_Error;

class JsonTable extends CoreModel
{
  public const TABLE_NAME = 'json_table';

  public function createTable()
  {
    $charset_collate = $this->wpdb->get_charset_collate();
    $sql = 'CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME ."` (
      `user_id` BIGINT NOT NULL UNIQUE,
      `json` TEXT ) " . $charset_collate;
		$this->wpdb->query( $sql );
	}
	
	public function deleteTable()
	{
		$sql = 'DROP TABLE `' . self::TABLE_NAME . '`';
		$this->wpdb->query($sql);
	}
	
	public function findByUser($params)
  {
		$userIdFromRequest = $params['userId'];
		$tokenValidation = $this->validate_token();
		
		if (!is_wp_error($tokenValidation))
		{
    	$userIdFromToken = $tokenValidation['data']['user'];

   		if ($userIdFromRequest == $userIdFromToken) {
        $sql = "SELECT `json` FROM `" . self::TABLE_NAME . "` WHERE `user_id` = " . $userIdFromToken;
        $result = $this->wpdb->get_results($sql);
        $result[0]->json = self::decrypt( $result[0]->json, $userIdFromToken );
        $json = $result[0]->json;
        return $json;
				
    	} else {
        return new WP_Error(
          'jwt_invalid_user',
          'Authorization header doesn\'t matches user.',
          array(
            'status' => 403,
            'message' => "vous n'avez pas le droit de voir la liste de cet utilisateur."
          )
        );
  	  }
		}
		return $tokenValidation;
	}
	
	//* This Function is deprecated for the json table line of our user is insert in the email validation step
	//DEPRECATED - KEPT FOR TEST PURSPOSES ONLY
	public function initialize($userId)
	{
    $data = [
      'user_id' => $userId,
      'json'    => self::encrypt("[\"vide\"]", $userId),
    ];
  
	return $this->wpdb->insert(self::TABLE_NAME, $data);
	}
	
  public function deleteUserJsonData($id){
    $sql = 'DELETE FROM `'. self::TABLE_NAME . "` WHERE `user_id` = " . $id;
    return ($this->wpdb->query( $sql ));
  }
  
	public function updateJson($params)
	{
		$userIdFromRequest = $params['userId'];
		$tokenValidation = $this->validate_token();
		
		if (!is_wp_error($tokenValidation))
		{
    	$userIdFromToken = $tokenValidation['data']['user'];

   		if ($userIdFromRequest == $userIdFromToken) {
      // 1- import the actual json data (by a findAll)
      // 2- add to this json object the new entry
      // 3- Add all this to DB.
      
      //1-
      //$jsonData = self::findByUser($userIdFromRequest);
      //2-
			//Let Encryprt the json :
			$json = $params['json'];
			$json = self::encrypt($params['json'], $userIdFromToken);
			//return $json;	
			$data = [
		 	'json' => $json
			];
      
			return $this->wpdb->update(self::TABLE_NAME, $data, ["user_id" => $userIdFromToken]);
			
    	} else {
        return new WP_Error(
          'jwt_invalid_user',
          'Authorization header doesn\'t matches user.',
          array(
            'status' => 403,
            'message' => "vous n'avez pas le droit de modifier la liste d'un autre."
          )
        );
  	  }
		}
		return $tokenValidation;
  }
	
	public function addNewCorrespondance($request)
	{
		$params = $request->get_params();
		$data = [
			'list_element_id'=>$params['listElementId'],
			'rubrique_id'    =>$params['rubriqueId']
		];
	  
		return $this->wpdb->update();
	}
	
	
	//* https://www.geeksforgeeks.org/how-to-encrypt-and-decrypt-a-php-string/
	
	private static function encrypt($json, $id)
	{
		// Store the cipher method
		$ciphering = "AES-128-CTR";
			
		// Use OpenSSl Encryption method
		//$iv_length = openssl_cipher_iv_length($ciphering);
		$options = 0;
			
		// Non-NULL Initialization Vector for encryption
		$encryption_iv = '1234567891011121';
			
		// Store the encryption key and make it unique
		$encryption_key = $id . JSON_ENCRYPT_KEY . $id;
			
		// Use openssl_encrypt() function to encrypt the data
		$encryption = openssl_encrypt($json, $ciphering,
    $encryption_key, $options, $encryption_iv);
		
		return $encryption;
	}
	
	private static function decrypt($json, $id)
	{
		// Store the cipher method
		$ciphering = "AES-128-CTR";
			
		// Use OpenSSl Encryption method
		//$iv_length = openssl_cipher_iv_length($ciphering);
		$options = 0;
			
		// Non-NULL Initialization Vector for encryption
		$encryption_iv = '1234567891011121';
			
		// Store the encryption key and make it unique
		$encryption_key = $id . JSON_ENCRYPT_KEY . $id;
			
		// Use openssl_encrypt() function to encrypt the data
		$encryption = openssl_decrypt($json, $ciphering,
    $encryption_key, $options, $encryption_iv);
		
		return $encryption;
	}
}