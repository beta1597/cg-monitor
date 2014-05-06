<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};	
?>
<h1>Windows install</h1>
<div style="clear:both"></div><br/>

Download <a href='http://www.cg-monitor.com/download/cg-monitor-windows.zip'>http://www.cg-monitor.com/download/cg-monitor-windows.zip</a><br/>
unzip the cg-monitor zip to wherever (example: c:/cg-monitor/<br/>
change _config.php with the keys from your website.<br/>
run "install" as users, then run "createTask.bat" as admin it will make a cronjob (task) to sync every 2 min with the server<br/>
If it don't work you can try to add your directory in the task settings under "start in".<br/>
<br/>
If you ever want do disable all stuff, you only need to del the task.<br/>
<br/>
You can test the sync by run "sync.bat"  
 