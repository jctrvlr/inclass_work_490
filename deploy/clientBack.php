#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

function runScript($v, $n, $target) {
    $fname = $n."-".$v;
    list($type, $location) = split('[/.-]', $target);
    switch($type) {
        case("fe"): {
            exec("sudo cp -r /tmp/".$fname."/ /var/git/");
            exec("sudo ln -sf /var/git/".$fname." /var/www/html/");
            echo "Successfully deployed front-end files.";
        }
        case("be"): {
            exec("sudo cp -r /tmp/".$fname."/ /var/git/");
            exec("sudo nohup php /var/git/".$fname."/rabbitMQServer.php");
            exec("sudo nohup php /var/git/".$fname."/rabbitMQServerData.php");
            exec("sudo nohup php /var/git/".$fname."/rabbitMQServerReview.php");
            exec("sudo mysql -u root -pIreland2018 it490 < it490.sql");
            echo "Successfully deployed back-end files.";
        }
        case("dmz"): {
            exec("sudo cp -r /tmp/".$fname."/ /var/git/");
            exec("sudo nohup php /var/git/".$fname."/php_backend/rabbitMQServerBackend.php");
            echo "Successfully deployed dmz files.";
        }
        default: {
            echo "Error invalid type";
        }
    }
    // run script. depnding on target type
    // if backend, frontend, dmz
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "run_script":
        return runScript($request['ver'], $request['name'], $request['target']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("deployClient.ini","testServer");

$server->process_requests('requestProcessor');
exit();

?>