<?php
namespace App\Validation;

class CustomRules{

  // Rule is to validate mobile number digits
  public function usernameValidation(string $str, string $fields, array $data){

    $user = model('\App\Models\UserModel')->where('username', $str)->orWhere('email', $str)->first();
    return isset($user->id);
  }

	public function validateRecaptcha($str, string $fields, array $data)
	{

    if(setting('google_recaptcha_enabled')!='1'){
      return true;
    }

    if(empty($str)){
      return false;
    }
		
    $userIp =  service('request')->getIPAddress();
    $secret = setting('google_recaptcha_secretkey');

    $url = "https://www.google.com/recaptcha/api/siteverify";
    $payload = [
        'secret'   => $secret,
        'response' => $str,
        'remoteip' => $userIp,
    ];

    $response = cnd_http_request('POST', $url, http_build_query($payload), [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $status = json_decode($response->body, true);

    if (isset($status['success']) && $status['success']) {
        return true;
    }
    return false;
	}

}