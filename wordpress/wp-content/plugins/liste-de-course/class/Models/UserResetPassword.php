<?php

namespace Liste_de_course\Models;

use WP_Error;

class UserResetPassword extends CoreModel
{
	public static function sendResetLink($params)
	{
    $email = $params['email'];

    //* Do we have a proper email ?
    if (!is_email($email)) {
        return new WP_Error(
            'Invalid email',
            __('Sorry, this email is not valid !'),
            array( 'data' => null )
        );
    }
    //* Yes

    //* Is this mail on our DataBase ?
    $user = get_user_by('email', $email);

    if ($user) {
      //* Yep, let send a mail to this lovely user :
      //* First create a token
      $key = bin2hex(random_bytes(32));
      $validity = strval(time() + 7200);
      $token = $validity . '.' . $key;
      update_user_meta($user->ID, 'key', $token);
      self::SendMail($user, $token);
    }
    //* Else we return nothing
}
  
	private static function SendMail($user, $key){
			$headers = [
				'Content-Type: text/html; charset=UTF-8',
				'From: La Liste de course <contact@raffiskender.com>'
				
			];
		//*- Send the mail
			
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
          p{
            text-align:center;
          }
				</style>
				<body>
				<p>Bonjour, vous avez demandé la réinitialisation de votre mot de passe. Pour ce faire rendez-vous sur la page en cliquant sur le lien ci-dessous</p>
				<p>
					<a href="https://liste-v2.raffiskender.com/passwordChange?user=' . $user->ID . '&key=' . $key . '">
						Réinitialiser
					</a>
				</p>
				</body>
			</html>
			';
			
			wp_mail($user->user_email, 'Réinitialisation de mot de passe', $message, $headers);
	}
  
  public static function setNewPassword($params){
    $newPassword = $params['password'];
    $userId      = $params['userId'];
    $key         = $params['key'];    
    
    if ($newPassword && $userId && $key) {
      $currentUser = get_user_by('ID', $userId);
      $inDBKey = get_user_meta($currentUser->data->ID, 'key');
      if ($inDBKey[0] == $key) {
      //* Check token validity
      //*1- retrieve expiration date
      $breaktoken = explode(".",$key);
      $validation = $breaktoken[0];
      //*2- See if is ok
      if ($validation - time() > 0) {
          wp_set_password( $newPassword, $userId );
          update_user_meta($currentUser->ID, 'key', '');
          return '1';
        }
        else return 'Clé expiré. Redemandez la réinitialisation de votre mot de passe.';
      }
      else return 'Clé invalide. Redemandez la réinitialisation de votre mot de passe.';
    }
		return '0';
  }
  
  public function setNewPasswordFromProfilPage($params){
    $currentPassword   = $params['currentPassword'];
    $newPassword       = $params['newPassword'];  
    $userIdFromRequest = $params['userId'];  
    
    
    $tokenValidation = $this->validate_token();
    if (!is_wp_error($tokenValidation))
		{
      if ($userIdFromRequest == $tokenValidation['data']['user']) {   
        $currentUser = get_user_by('ID', $userIdFromRequest);
        
        if ($currentPassword && $newPassword){
          //return ($currentPassword->_value);
          if (wp_check_password($currentPassword, $currentUser->user_pass, $userIdFromRequest)) {
            //*Ajouter des vérifications sur le mot de passe...
            wp_set_password( $newPassword, $userIdFromRequest );
            return '1';
          }
          else return new WP_Error(
            'invalid_password',
            'Password is wrong',
            array(
              'status' => 403,
              'message' => "Vous n'avez pas entré le bon mot de passe"
            )
          );;
        }
        else return 'Requête incomplête';
        return '0';
      }
      else return new WP_Error(
        'jwt_invalid_user',
        'Authorization header doesn\'t matches user.',
        array(
          'status' => 403,
          'message' => "Vous ne pouvez pas modifier cet utilisateur"
        )
      );
    }
    return $tokenValidation;
  }
  
}
