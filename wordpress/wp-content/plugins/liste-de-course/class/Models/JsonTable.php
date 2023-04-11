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
      'json'    => self::encrypt("[{\"id\":1,\"tabName\":\"liste\",\"selected\":true,\"content\":[\"vide\"]}]", $userId),
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
        $jsonArray = json_decode($json);
        
        //* Let do some verifications :
        
        //* 1st - Are we trying to delete tab Nb 1 ?
        if ($jsonArray[0]->id == 1){
          $selected = 0;
          foreach($jsonArray as $index){
            //* 2nd Is a line of tab > to 25 caracteres
            if (strlen($index -> tabName) > 25){
              return  new WP_Error(
                'Invalid JSON',
                'Tab name too long...',
                array(
                  'status' => 403,
                  'message' => "Le nom de l'onglet ne doit pas dépasser 25 charactères"
                )
              );
            }
            //*3rd is there more than 1 tab selected
            if($index->selected == true && $selected == 1){
              $index->selected = false;
            }
            
            //* 4th is there a line with more than 75 caracteres...
            foreach($index -> content as $content){
              if (strlen($content -> content) > 75){
                return  new WP_Error(
                  'Invalid JSON',
                  'Line too long...',
                  array(
                    'status' => 403,
                    'message' => "une ligne ne doit pas dépasser 75 charactères"
                  )
                );
              }
            }
            $selected = $index -> selected == true ? 1 : $selected;
          }
          //* 5th- Is there more than 15 tabs...
        if (count($jsonArray) > 15){
          return new WP_Error(
            'Invalid JSON',
            'Too much tabs...',
            array(
              'status' => 403,
              'message' => "Vous ne pouvez pas créer plus de 15 onglets"
            )
          );
        }
         
         //return $jsonArray;
          
          $json = self::encrypt(json_encode($jsonArray), $userIdFromToken);
          //return $json;	
          $data = [
          'json' => $json
          ];
          return $this->wpdb->update(self::TABLE_NAME, $data, ["user_id" => $userIdFromToken]);
        }
        else{
          return new WP_Error(
            'Invalid JSON',
            'Id 1 can\'t be delete',
            array(
              'status' => 403,
              'message' => "vous ne pouvez pas supprimer l'onglet n°1 ! !"
            )
          );       
        }
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