<?php
session_start();

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('logger.inc');

// Hardcoded session variables that are set when log in.$_COOKIE
//$_SESSION["email"] = "jic6@njit.edu";
//$_SESSION["zipcode"] = "07103";

if (!isset($_POST))
{
	$msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
	echo json_encode($msg);
	exit(0);
}
$client = new rabbitMQClient("rabbitMQData.ini","testServer");
$request = $_POST;

switch ($request["type"])
{
	case "get_res": {
		$req=array();
		$req['type'] = "get_rest";
		$req['user'] = $_SESSION["email"];
		$req['zip'] = $_SESSION["zipcode"];
		$req['last'] = $_SESSION['last'];
		$response = $client->send_request($req);
		$json = json_decode($response, true);
		$_SESSION["last"] = $json['name'];
		break;
	}
	case "rest_response": {
		$req = array();
		$req['type'] = 'rest_response';
		$req['user']=$_SESSION['email'];
		$req['res_id']=$request['res_id'];
		$req['like']=$request['like'];
		$req['zip']=$_SESSION['zipcode'];
		$req['last']=$_SESSION['last'];
		$response = $client->send_request($req);
		$json = json_decode($response, true);
		$_SESSION["last"] = $json["name"];
		break;
	}
	case "get_favorites": {
		$req = array();
		$req['type'] = 'get_favorites';
		$req['user'] = $_SESSION['email'];
		$response = $client->send_request($req);	
		break;	
	}
	case "logout": {
		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
    		$params = session_get_cookie_params();
   			setcookie(session_name(), '', time() - 42000,
        		$params["path"], $params["domain"],
        		$params["secure"], $params["httponly"]
    		);
		}

		// Finally, destroy the session.
		session_destroy();
		exit(0);
	}
}

echo json_encode($response);
exit(0);

?>