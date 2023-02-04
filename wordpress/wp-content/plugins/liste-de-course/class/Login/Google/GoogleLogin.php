<?php

namespace Liste_de_course\Login\Google;
use \Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Liste_de_course\Models\JsonTable;
use WP_Error;
use WP_User;

class GoogleLogin
{
    //**** Action plan ****//
    //* X 1- send a GET request to https://oauth2.googleapis.com/token with the token as a header bearer
    //* X 2- retrieve name / surname / email and googleID

    //* X 3- search in DB if a user has this googleID
    //* X 4- if no -> create the user with google credentials
    //* X 5- Connect the user with a jwt token.
    //* X 6- return jwt token to the app


    //*****  1- use the composer element to retrieve datas ******/


    public function loginWithGoogle($request)
    {
      $userData = $this->retrieveGoogleUserDataFromGoogleCode($request);
      if ($userData) {
        if ($userData->email_verified) {
          $googleId = $userData->sub;
          $userEmail = $userData->email;
          $userName = $userData->name;
          $userLastName  = isset($userData->family_name) ? $userData->family_name : '';
          $userFirstName = isset($userData->given_name) ? $userData->given_name : $userName;
          
          $user = get_user_by('email', $userEmail);
          
          if (is_a($user, 'WP_User')) {
              return $this->create_token($user);
          }
          else {
                       
            $name    = str_replace(' ', '', $userName);
            
            $user_id = $this->create_user($name, $userEmail);
            if (is_wp_error($user_id)) {
              return $user_id;
            }

            $user = new WP_User($user_id);
            wp_update_user(array( 'ID'           => $user_id,
                                  'display_name' => $userName,
                                  'first_name'   => $userFirstName,
                                  'last_name'    => $userLastName
            ));
            
            update_user_meta($user->ID, 'confirmed', '1');
            update_user_meta($user->ID, 'key', '');
            update_user_meta($user->ID, 'google_id', $googleId);
            return ($this->create_token($user));
          }
        }
        else {
          return ('email n\'a pas été vérifié');
        }
      }
    }
    
    public function retrieveGoogleUserDataFromGoogleCode($request)
    {
        //retrieve the code
        $params = $request->get_params();
        $code = $params['code'];

        // request the user token
        $client = new Client();

        try {
          $response = $client->request(
              'POST',
              'https://oauth2.googleapis.com/token',
              [
                'headers' => [
                  'Content-Type'=>'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                  'code' => $code,
                  'client_id' => GOOGLE_ID,
                  'client_secret' => GOOGLE_SECRET,
                  'redirect_uri' => 'https://liste-v2.raffiskender.com/googleLogin',
                  'grant_type' => 'authorization_code',
                  ]
                ]
                );
        } catch (\GuzzleHttp\Exception\ClientException $exeption) {
          return ('ERREUR !' . var_dump($exeption->getMessage()));
        }
        $response = json_decode($response->getBody()->getContents());
        $googleToken = $response->access_token;

        try {
            $response = $client->request(
                'GET',
                'https://openidconnect.googleapis.com/v1/userinfo',
                [
                  'headers' => [
                    'Authorization' => 'Bearer ' . $googleToken
                  ]
                ]
            );
        } catch (\GuzzleHttp\Exception\ClientException $exeption) {
            return ('ERREUR !' . var_dump($exeption->getMessage()));
        }

        return json_decode($response->getBody()->getContents());
    }


    /**
     * Create the user.
     *
     * @param string $username
     * @param string $email
     *
     * @return integer User ID.
     */
      protected function create_user($username, $email, $password = '')
      {
          if (username_exists($username)) {
              $username = $username . date('YmdHis');
          }

          if (! $password) {
              $password = wp_generate_password();
          }

          $user_id = wp_create_user($username, $password, $email);
          $jsonTable = new JsonTable();
          $jsonTable->initialize($user_id);

          return $user_id;
      }


    /**
       * Create the token for the User.
       *
       * @param \WP_User $user User object.
       *
       * @return mixed|void|\WP_Error
       */
      protected function create_token($user)
      {
          $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

          /** First thing, check the secret key if not exist return a error*/
          if (!$secret_key) {
              return new \WP_Error(
                  'jwt_auth_bad_config',
                  __('JWT is not configurated properly, please contact the admin', 'wp-api-jwt-auth'),
                  array(
                      'status' => 403,
                  )
              );
          }

          /** Valid credentials, the user exists create the according Token */
          $issuedAt = time();
          $notBefore = apply_filters('jwt_auth_not_before', $issuedAt, $issuedAt);
          $expire = apply_filters('jwt_auth_expire', $issuedAt + (DAY_IN_SECONDS * 7), $issuedAt);

          $token = array(
              'iss' => get_bloginfo('url'),
              'iat' => $issuedAt,
              'nbf' => $notBefore,
              'exp' => $expire,
              'data' => array(
                  'user' => array(
                      'id' => $user->ID,
                  ),
              ),
          );

          /** Let the user modify the token data before the sign. */
          $algorithm = $this->get_algorithm();

          if ($algorithm === false) {
              return new WP_Error(
                  'jwt_auth_unsupported_algorithm',
                  __('Algorithm not supported, see https://www.rfc-editor.org/rfc/rfc7518#section-3', 'wp-api-jwt-auth'),
                  [
                      'status' => 403,
                  ]
              );
          }

          $token = JWT::encode(
              apply_filters('jwt_auth_token_before_sign', $token, $user),
              $secret_key,
              $algorithm
          );

          /** The token is signed, now create the object with no sensible user data to the client*/
          $data = [
              'token'             => $token,
              'user_email'        => $user->data->user_email,
              'user_nicename'     => $user->data->user_nicename,
              'user_display_name' => $user->data->display_name,
              'user_id'           => $user->data->ID,
              'user_confirmed'    => get_user_meta($user->ID, "confirmed")
    ];
          /** Let the user modify the data before send it back */
          return apply_filters('jwt_auth_token_before_dispatch', $data, $user);
      }

      /**
       * Get the algorithm used to sign the token via the filter jwt_auth_algorithm.
       * and validate that the algorithm is in the supported list.
       *
       * @return false|mixed|null
       */
      private function get_algorithm()
      {
          $algorithm = apply_filters('jwt_auth_algorithm', 'HS256');
          if (! in_array($algorithm, $this->supported_algorithms)) {
              return false;
          }

          return $algorithm;
      }
    /**
       * Supported algorithms to sign the token.
       *
       * @var array|string[]
       * @since 1.3.1
       * @see https://www.rfc-editor.org/rfc/rfc7518#section-3
       */
      private array $supported_algorithms = [ 'HS256', 'HS384', 'HS512', 'RS256', 'RS384', 'RS512', 'ES256', 'ES384', 'ES512', 'PS256', 'PS384', 'PS512' ];
}
