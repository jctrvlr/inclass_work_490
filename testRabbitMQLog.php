#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('logger.inc');
 
// Array will be programmatically filld when log is created
$samp_options = array();
$samp_options[0] = 'e';
<<<<<<< HEAD
$samp_options[1] = 'Khari';
$samp_options[2] = 'Test log message that would be generated by other things';
=======
$samp_options[1] = 'johnsComputer';
$samp_options[2] = 'Hello how are you?';
>>>>>>> origin/master

sendLogs($samp_options);

?>

