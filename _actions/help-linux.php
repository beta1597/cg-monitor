<?php
if(empty($_SESSION['MM_Username'.$secret_passphrase]) and $_SESSION['MM_Username'.$secret_passphrase] != "okuser"){
exit;
};	
?>
<h1>Linux install</h1>
<div style="clear:both"></div>

<h3>shell script:</h3>

wget http://www.cg-monitor.com/download/install.sh<br/>		
chmod +x install.sh<br/>
./install.sh<br/>
<br/>

<h3>commands in install.sh:</h3>

apt-get update<br/>
apt-get install -y php5-cli php5-mcrypt php5-curl wget<br/>
mkdir -p /var/www/cg-monitor/<br/>
cd /var/www/cg-monitor/<br/>
wget http://www.cg-monitor.com/download/cg-monitor-linux.zip<br/>
unzip cg-monitor-linux.zip<br/>
rm cg-monitor-linux.zip<br/>
chmod +x /var/www/cg-monitor/com-linux/reboot.sh<br/>
chmod +x /var/www/cg-monitor/com-linux/algo-1.sh<br/>
chmod +x /var/www/cg-monitor/com-linux/algo-1-start.sh<br/>
chmod +x /var/www/cg-monitor/com-linux/algo-2.sh<br/>
chmod +x /var/www/cg-monitor/com-linux/algo-2-start.sh<br/>
chmod +x /var/www/cg-monitor/com-linux/algo-3.sh<br/>
chmod +x /var/www/cg-monitor/com-linux/algo-3-start.sh<br/>
echo "*/2 * * * * root /usr/bin/php5 /var/www/cg-monitor/sync.php" >> /etc/crontab<br/>
nano /var/www/cg-monitor/_config.php<br/><br/>
<br/><br/>