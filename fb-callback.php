<?php 
session_start();
ob_start();
$domain = $_SERVER['HTTP_HOST'];
require_once 'vendor/autoload.php';
if(!function_exists('login_user')){
    function login_user($data){
      $_SESSION['akpk_session'] = array();
      foreach ($data as $key => $value) {
          $_SESSION['akpk_session'][$key] = $value;
      }
      $url= $domain.'/fblogin/';
      header("location: ".$url);
      exit();
    }
  }
$fb = new Facebook\Facebook([
  'app_id' => 'APP_ID_HERE',
  'app_secret' => 'APP_SECRET_HERE',
  'default_graph_version' => 'v2.7',
  ]);
  $helper = $fb->getRedirectLoginHelper();
try {
  $accessToken = $helper->getAccessToken();
  if(isset($accessToken)){
    $_SESSION['access_token'] = (string)$accessToken->getValue();
  }

} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

// Logged in


if($_SESSION['access_token']) {
    try {
      $fb->setDefaultAccessToken($_SESSION['access_token']);
      $res = $fb->get('/me?locale=en_US&fields=name,email,age_range,gender,cover,first_name,last_name,locale,link');
      $user = $res->getGraphUser();
      $name = $user->getField('name');
      $id  = $user->getId();
      $image = 'https://graph.facebook.com/'.$user->getId().'/picture?width=200&height=200';
      $age = $user->getField('age_range');
      $fullage = $user->getField('age_range');
      $age =  $age['min'];
      $user_friends = $user->getField('friends');

      $gender = $user->getField('gender');
      $cover = $user->getField('cover');
      $cover = $cover['source'];

      $email = $user->getField('email');

      $first_name = $user->getField('first_name');
      $last_name = $user->getField('last_name');
      $locale = $user->getField('locale');
      $link = $user->getField('link');
      $data = [
        '__fb_login' => 'ok',

        '__name' => $name,
        '__first_name' => $first_name,
        '__last_name' => $last_name,
        '__locale' => $locale,
        '__link' => $link,
        '__profile_pic' => $image,
        '__age' => $age,
        '__id' => $id,
        '__gender' => $gender,
        '__cover' => $cover,
        '__fullage' => $fullage,
        '__user_friends' => $user_friends,
        '__email' => $email
      ];
      echo "<pre>";
      //print_r($gender);
      print_r($data);
      echo "</pre>";
      //print_r($data);
      login_user($data);
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

?>