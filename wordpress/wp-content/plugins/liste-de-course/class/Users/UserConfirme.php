<?php

namespace Liste_de_course\Users ;

class UserConfirme
{
	
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

			$user = get_userdata($userId);
			$mail = $user->get('user_email');
			$headers = [
				'Content-Type: text/html; charset=UTF-8',
				'From: La Liste de course <contact@raffiskender.com>'
				
			];
		//*2- Send a mail
			
			$message = ' 
			<html>
				<body>
				<p>Bienvenue sur la liste de course. Pour pouvoir utiliser le site, vous devez confirmer votre mail en cliquant sur le boutton ci-dessous : </p>
				<p>
					<a href="https://liste.raffiskender.com/confirmation?user=' . urlencode($user->user_login) . '&key=' . $key . '">
						confirmer...
					</a>
				</p>
				</body>
			</html>
			';
			
			wp_mail($mail, 'Merci de confirmer votre e-mail', $message, $headers);
	}
	
	public function confirmeUser($params){
		$user = $params['user'];
		$key = $params['key'];

		$currentUser = get_user_by('login', $user);

		$inDBKey = get_user_meta($currentUser->data->ID, 'key');
				
		if ($inDBKey[0] == $key) {
			update_user_meta($currentUser->data->ID, 'confirmed', '1');
			return '1';
		}
		
		return '0';
	}
}