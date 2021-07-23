#!/usr/bin/php
#password = "test";

//remote server configuration tool
//steve wallis 16/07/21

<?php

$web_password = "test";

$output = null;
$retval = null;
$i = 0;
$count = 0;
$env = "";
$cmd = "";
$line = "";
$line1 = "";
$lines = "";
$log = "";
$now = date("Y-m-d H:i:s");

$server = $argv[1];
$user = $argv[2];
$pass = $argv[3];

shell_exec('> ./rsct.log');

$str = "Process started - IP ". $server . " User ";
$str .= $user . " " . $now . "\n\n";

file_put_contents("./rsct.log", $str, FILE_APPEND);

//basic script validation. 

$lines = explode("\n", file_get_contents("./script.txt"));
$lines = array_filter($lines);

while ($i < count($lines)) 
{
	$line = $lines[$i];
	$cmd = substr($line, 0, strpos($line, " ")); 
	$data = substr($line, strpos($line, " ") + 1); 

	if (!in_array($cmd, array("ENV","COPY","EXPOSE","RUN"))) 
	{
		echo 'INVALID SCRIPT FORMAT ON LINE ' .$i ;
		exit(1);
	}
	$i +=1;
}

//add new server ssh key to avoid prompt.

$cmd = "ssh-keyscan -H " . $server . " >> ~/.ssh/known_hosts";
exec($cmd, $output, $retval);	


if ($retval < 0) //TODO fix retval
{
	echo 'CONNECTION FAILED. CHECK SERVER IP, USER AND PASSWORD';
	exit(1);
}

//copy upload files to temp dir on targrt

file_put_contents("rsct.log", "File Transfer Started \n\n", FILE_APPEND);

$line = "rm -fr tmp/rsct_tmp";
//shell_cmd($line,2,$env,$server,$user,$pass);

$line = "mkdir -p tmp/rsct_tmp ";
shell_cmd($line,1,$env,$server,$user,$pass);


$line = 'sshpass -p ' . $pass . ' rsync -avz ./uploads/ ' ;
$line .= $user . '@' . $server . ':tmp/rsct_tmp/'; 

exec($line, $output, $retval);	

file_put_contents("rsct.log", "File Transfer Completed \n\n", FILE_APPEND);

			
//process script.txt

for ($i = 0; $i < count($lines); $i++) 
{
	$line = $lines[$i];
	$cmd = substr($line, 0, strpos($line, " ")); 
	$data = substr($line, strpos($line, " ") + 1); 
	
	switch ($cmd) 
	{
 		case "ENV":
  			$env = procENV($data,$env);
  			break;
 		case "RUN":
  			procRUN($data,$env,$server,$user,$pass);
  			break;
 		case "EXPOSE":
  			procEXPOSE($data);
  			break;
 		case "COPY":
  			procCOPY($data,$env,$server,$user,$pass);
  			break;
	}
}

$now = date("Y-m-d H:i:s");
$str = "Process ended - IP ". $server . " User ";
$str .= $user . " " . $now . "\n\n";
file_put_contents("./rsct.log", $str, FILE_APPEND);

function procRUN($line,$env,$server,$user,$pass)
{
	shell_cmd($line,2,$env,$server,$user,$pass);
}

function procCOPY($line,$env,$server,$user,$pass)
{
	$from = substr($line, 0, strpos($line, " ")); 
	$to = substr($line, strpos($line, " ") + 1); 
	$path = dirname($to);

	$line = "cp -fr ~/tmp/rsct_tmp/$from " . $to ; 
	shell_cmd($line,2,$env,$server,$user,$pass);
}

function procENV($line,$env)
{
	$envVar = trim(substr($line, 0, strpos($line, "="))); 
	$envVal = trim(substr($line, strpos($line, "=") + 1)); 
	$env = $env . 'export ' . $envVar . '="' . $envVal . '";';		

	return $env;
}

function procEXPOSE($line)
{
}

function shell_cmd($line,$mode,$env,$server,$user,$pass)
{
	if ($mode === 1)// no sudO
	{
		$cmd = $env . 'sshpass -p ' . $pass . ' ssh -t ' . $user . '@' . $server; 
		$cmd .= ' ' . $line . ' 2>&1';
	}
	else if ($mode === 2) //sudu cmd
	{
		$cmd = $env . 'sshpass -p ' . $pass . ' ssh -t ' . $user . '@' . $server; 
		$cmd .= ' "echo ' . $pass . '|sudo -S ' . $line . ' 2>&1"';
	}

echo $cmd . "\n\n";

	exec($cmd, $output, $retval);	

	$out = implode($output);
	$out = preg_replace('~\r\n?~', "\n",$out);
	$log = $line . "\n\n";
	$log .= $out . "\n\n";

	file_put_contents("rsct.log", $log, FILE_APPEND);
}

?>
