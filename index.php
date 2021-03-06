<?php 

session_start();
require_once 'vendor/autoload.php';
$domain = $_SERVER['HTTP_HOST'];

if(isset($_GET['logout'])){
	$_SESSION = array();
	unset($_SESSION);
	session_destroy();
	header('location: index.php');
	exit();
}

$fb = new Facebook\Facebook([
  'app_id' => 'APP_ID_HERE',
  'app_secret' => 'APP_SECRET_HERE',
  'default_graph_version' => 'v2.7'
  ]);

$helper = $fb->getRedirectLoginHelper();
$url= "http://".$domain.'/fblogin/fb-callback.php';
$permissions = ['email'];
$login_url = $helper->getLoginUrl($url,$permissions);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Facebook Login</title>
	<link rel="shortcut icon" href="favicon.ico" />
		<style>
		*{
			font-family: Arial;
			font-variant: small-caps;
			text-align: center;
		}
	</style>

</head>
<body>
<?php if(!(isset($_SESSION['akpk_session']['__fb_login']) AND $_SESSION['akpk_session']['__fb_login'] == "ok")): ?>
	<div align="center">
		<a href="<?php echo htmlspecialchars($login_url); ?>">
			<img src="img/lwfb.png" alt="Login With Facebook" width="300px">
		</a>
	</div>
	<?php else: $user = $_SESSION['akpk_session']; ?>
		<div id="detailsBox">
			<img src="<?php echo $user['__profile_pic']; ?>" alt="User Profile Picture" />
			<img src="<?php echo $user['__cover']; ?>" alt="User Cover Photo" />
			<h3>ID: <?php echo $user['__id']; ?></h3>
			<h3>FULL NAME: <?php echo $user['__name']; ?></h3>
			<h3>First Name: <?php echo $user['__first_name']; ?></h3>
			<h3>Last Name: <?php echo $user['__last_name']; ?></h3>
			<h3>Locale: <?php echo $user['__locale']; ?></h3>
			<h3>Link: <a href="<?php echo $user['__link']; ?>" target="_blank"><?php echo $user['__link']; ?></a></h3>
			<h3>Email: <?php echo $user['__email']; ?></h3>
			<h3>Age Above: <?php echo $user['__age']; ?></h3>
			<h3>Gender: <?php echo $user['__gender']; ?></h3>
		</div >
		<a href="?logout=1">Logout</a>
	<?php endif; ?>

</body>
</html>
