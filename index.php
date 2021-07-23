<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- META FOR IOS & HANDHELD -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="HandheldFriendly" content="true" />
<meta name="apple-mobile-web-app-capable" content="YES" />
<!-- //META FOR IOS & HANDHELD -->
<meta content="en-gb" http-equiv="Content-Language" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Remote Server Configuration</title>
<style type="text/css">
.auto-style1 {
	text-align: center;
}
.auto-style2 {
	font-size: large;
	font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
}
.auto-style3 {
	font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
}
.auto-style5 {
	font-size: medium;
	font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
}
.auto-style7 {
	font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
}
.auto-style8 {
	font-size: small;
}
.auto-style9 {
	font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
	font-size: large;
}
.auto-style10 {
	font-size: large;
}
.auto-style12 {
	font-family: "Franklin Gothic Medium", "Arial Narrow", Arial, sans-serif;
	font-size: small;
}
</style>
</head>

<body>

<script> 
function open_file()
{
	document.getElementById('File1').click();
} 
function open_file1()
{
	document.getElementById('File2').click();
} 
</script>

<form action="index.php" method="post" enctype="multipart/form-data">
<div class="auto-style1">
<br>
<span class="auto-style9"><a href="about.txt" target="_blank">Remote Server Configuration Tool</a></span>
<br>
<br>
<p class="auto-style1"><span class="auto-style3">&nbsp; Admin Password&nbsp;&nbsp;&nbsp;&nbsp;
</span>
<input name="AdmPass" style="width: 138px" type="text" class="auto-style3" />
<br>
<br>
<input name="File1" opt="UploadFiles" id="File1" type="file" hidden/>
<input name="File2[]" opt="UploadFiles" id="File2" type="file" multiple hidden/>
<input class="auto-style12" name="btnSubmit" opt="DownloadScript" type="submit" formtarget="_blank" formaction="script.txt" value="View Script" /><span class="auto-style12">&nbsp;&nbsp;
</span>
<input class="auto-style12" name="btnSubmit" opt="UploadScript" type="button" value="Upload Script" onclick="open_file()"/><span class="auto-style12">&nbsp;&nbsp;
</span>
<input class="auto-style12" name="btnSubmit" opt="UploadFiles" type="button" value="Upload Files" onclick="open_file1()" />
<p class="auto-style1">
<p class="auto-style1"><span class="auto-style3">Server IP&nbsp;&nbsp;&nbsp;&nbsp;
</span>
<input name="ServerIP" style="width: 201px" type="text" class="auto-style3" /></p>
<p class="auto-style1"><span class="auto-style3">Username&nbsp;&nbsp; </span>
<input name="ServerUser" id="ServerUser" style="width: 201px" type="text" class="auto-style3" /></p>
<p class="auto-style1"><span class="auto-style3">Password&nbsp;&nbsp; </span>
<input name="ServerPass" style="width: 198px" type="text" class="auto-style3" /></p>
		<input name="btnSubmit" opt="Deploy" type="submit" value="Run Script" class="auto-style5" />
<br>
<br>
<textarea name="TextArea1" style="width: 285px; height: 119px"><?php echo $_SESSION["LogText"] ?></textarea> 
<br><br>
<input class="auto-style12" name="btnSubmit" type="submit" value="Show Log" />
<input class="auto-style12" name="btnSubmit" type="submit" value="Clear Log" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input class="auto-style12" name="btnSubmit" type="submit" value="Run Text" />

</div>
</form>

<?php

  $server=$_POST['ServerIP'];
  $user=$_POST['ServerUser'];
  $pass=$_POST['ServerPass'];
	$remotecmd=$_POST['TextArea1'];

	$count=0;

  if ($_POST['btnSubmit'] === "Run Script")
  {
		if (isset($_FILES["File1"]["name"]))
		{
			copy("./script.txt","./script.txt.bak");
			move_uploaded_file($_FILES["File1"]["tmp_name"], "./script.txt");
		}  
		
		while (isset($_FILES["File2"]["name"][$count]))
		{
			$from = $_FILES["File2"]["tmp_name"][$count];
			$to = "./uploads/" . $_FILES["File2"]["name"][$count];

			//move_uploaded_file($from,$to); //DISABLED IN TEST//

			$count += 1;
		}  

		$cmd = "./remote_conf.php " . $server . " " . $user . " " . $pass ;
		shell_exec($cmd . "> /dev/null &");
		
	}
	else if ($_POST['btnSubmit'] === "Show Log")
	{
		$_SESSION["LogText"] = file_get_contents('./rsct.log');
	}
	else if ($_POST['btnSubmit'] === "Clear Log")
	{
		$_SESSION["LogText"] = "";
		shell_exec('> ./rsct.log');
	}
	else if ($_POST['btnSubmit'] === "Run Text")
	{
		$cmd = './remote_conf.php ' . $server . ' ' . $user . ' ' . $pass ;
		$cmd .= ' "'. $remotecmd . '"';
		shell_exec($cmd . "> /dev/null &");
	}
?>
</body>
