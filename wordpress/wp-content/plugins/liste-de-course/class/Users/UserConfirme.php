<?php

namespace Liste_de_course\Users ;

use Liste_de_course\Models\JsonTable;

class UserConfirme
{
  protected $wpdb;
    
  public function __construct()
  {
    global $wpdb;
    $this->wpdb = $wpdb;
  }
		
	//*Cette classe permet de gérer l'envoie la réception la vérification et la confirmation du mail d'un nouveau soucris.
	//* elle se lance dès qu'un nouvl incrit... S'inscit.
	//TODO
	//* 1- Ajouter les user_meta au nouveau (une Key et un boolean 'confirmé' 0/1) / FAIT.
	//* 2- envoyer le mail avec un lien
	//* 3- la page du lien qui vérifie qu'on a bien connecté le bon user et le confirmer (à faire sur le front ?)
	//* 4- c tout (je pense.).
	
	
	public static function CreateNonConfirmedUser($userId){
		
		$key = bin2hex(random_bytes(32));
		
		update_user_meta($userId, 'confirmed', '0');
		update_user_meta($userId, 'key', $key);
		
		self::SendMailToNewUser($userId, $key);
		
	}
  
	private static function SendMailToNewUser($userId, $key){
		//*1- retrieve user's mail

			$user = get_user_by('ID', $userId);
			$mail = $user->get('user_email');
			$headers = [
				'Content-Type: text/html; charset=UTF-8',
				'From: La Liste de course <contact@raffiskender.com>'
				
			];
		//*2- Send a mail
			
			$message = ' 
			<html>
				<style>
					a{
						display: block;
						margin: 5px auto;
						text-align:center;
						width:10em;
						text-align:center;
						padding: 1em 1.5em;
						border-radius: 0.3em;
						color: white;
						background: blueviolet;
						font-weight: bold;
						border: none;
						box-sizing: border-box;
						transition: 0.2s;
						text-decoration:none
					}
					a:hover{
      			cursor: pointer;
						background: #af6eeb;
    			}
				</style>
				<body>
				<p>Bienvenue sur la liste de course. Pour pouvoir utiliser le site, vous devez confirmer votre mail en cliquant sur le boutton ci-dessous : </p>
				<p>
					<a href="https://liste-v2.raffiskender.com/confirmation?user=' . urlencode($user->ID) . '&key=' . $key . '">
						Confirmer...
					</a>
				</p>
				</body>
			</html>
			';
			
			wp_mail($mail, 'Merci de confirmer votre e-mail', $message, $headers);
	}
	
	/**
	 * confirme User Function
	 * Takes the parametters and compares it with the DB Params.
	 * @param [array] $params
	 * @return '1' on success, '0' if failed
	 */
	public function confirmeUser($params){
		$userId = $params['user'];
		$key  = $params['key'];
    $jsonTable = new JsonTable;
    
		if ($userId && $key) {
			$currentUser = get_user_by('ID', $userId);
			$inDBKey = get_user_meta($currentUser->ID, 'key');
      
			if ($inDBKey[0] == $key) {
				update_user_meta($currentUser->ID, 'confirmed', '1');
        update_user_meta($currentUser->ID, 'key', '', $key);
      	//* In bonus, We insert the json line of our user.
				$jsonTable = new JsonTable;
        if ($jsonTable->initialize($userId) == 1){
					return '2';
        }
  			return '1';
			}
		}
		return '0';
	}
}