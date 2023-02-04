<?php

namespace Liste_de_course\Login\Google;
use \Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Liste_de_course\Models\JsonTable;
use WP_Error;

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
      $params = $request->get_params();
      $code = $params['code'];

      
      return ($code);
      
  }
  
  public function retrieveGoogleUserDataFromGoogleCode($code)
  {
      //retrieve the code

      // request the user token
      $client = new Client();
      try {
        $response = $client->request(
            'POST',
            'https://oauth2.googleapis.com/token',
            [
              'form_params' => [
                'code' => $code,
                'client_id' => GOOGLE_ID,
                'client_secret' => GOOGLE_SECRET,
                'redirect_uri' => 'https://liste-v2.raffiskender.com/googleLogin/',
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
  }