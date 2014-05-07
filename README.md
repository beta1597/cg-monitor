### What is CG-monitor?

CG-Monitor is a web based application that allows you to remotely monitor and manage your CGMiner/SGMiner mining rigs all in one easy to use graphical interface.<br>
Use any web browser, smart phone or tablet to check stats or remotely control one or more mining rigs. <br>
CG-Monitor does not require any port forwarding from your router/firewall. <br>
All information transmitted to and from your mining rigs are encrypted and Salted on private keys to maximize security. <br>
CG-Monitor is an Open Source application. <br>
<br>
If CG-Monitor has been beneficial to you, please consider donating a little something to the developer:<br>
BTC: 1JcBmddyvWUMoBM7exCq2NwLtmF8vyprAn<br>
LTC: LhWGLVWquMnvbNErjZSmmfc4LEw9wF1aop<br>

### Features

<ul>
  <li>Intuitive, easy to read GUI using any web browser on a PC or Mac and also on iOS, Android and Windows smart phones and tablets.
  <li>Support for Windows 7/8 and Linux mining rigs using CGMiner/SGMiner. ASICs are also supported.
  <li>SGMiner multi-algorithm support. (SGMiner 4.0.1 and above)
  <li>Real time detailed statistical information for each rig in your farm. Historical stats of up to 48 hours using detailed charts.
  <li>Easy to use Pool Manager to quickly direct your rigs to the most profitable pools, remotely.
  <li>Automatically change Intensity based on GPU temp and fan speed.
  <li>Email alerts for dead/sick GPU’s, temperature and low WU.
  <li>Integrated Task Scheduler to automatically switch pools, reboot rigs and reset stats.
  <li>Automatically reboot rigs when a dead/sick GPU is detected. Rebooted rigs will automatically switch back to the pool it was      previously mining.
  <li>Secure login system using JavaScript SHA Hashing. All commands are encrypted in the database.
  <li>This software is Open Source and is free of Malware or any form of donation mining.
</ul>

### Requirements:

- cgminer / sgminer properly configured with API write access allowed
- cg-monitor needs to be the only automatic sync application (cg-remote and cg-monitor can interfere with each other)

### How does it work?

You need a small client (php page) that will run every 2 min.  <br>
Every 2 min the client will send an encrypted private key to the server.  <br>
The server will response with all the actions that the client need to do.<br>
<br>
After executing all the actions, the client will sent the api data from cgminer to the server.<br>
The server will look for pool, temp, etc changes and will decide if there are other actions to take (sync pools, change intensity)<br>
<br>
After this actions the server will reset the private key to nothing for security reasons.<br>

### What are the security measures we have taken?

- write api key of the miner limited to 127.0.0.1
- you never need open a port on your router
- encrypted login
- salted and encrypted password
- all actions are encrypted in the database
- all transactions from and to your miner are encrypted

### How can you contact me?

- irc channel #cg-monitor on irc.freenode.net (my username is beta1597)
- e-mail beta1597@outlook.com

---
### Installation webhost:

Download http://cg-monitor.com/download/cg-monitor.zip<br>
Extract all files to your own computer<br>
Change the _config.php file with your database settings<br>
Make sure you change the secret_passphrase (password and encryption is based on this key)<br>
<br>
Upload all the extracted files to your own webhosting (don’t extract install.zip).<br>
Browse to <your-url>/install.php<br>
Choose a username and a password (longer than 6 char)<br>

---
### Installation rig:
<br>
You need to install a little php client on every rig to sync to your webhost.<br>
Why on every rig? Because we need to send an exec command to reboot your rig or change algorithm.<br>
Installation procedure is explained in the help menu on the webhost.<br>
<br>
Installation files for linux (all linux version's) and windows available.<br>
<br>
You need to change all the files in the “com-linux” or “com-windows” directory to your own cgminer or reboot commands.  Because otherwise the commands will not work.<br>

---
### Upgrading:

Upgrading is easy.<br>
<br>
Just push go to the update menu and push the update button, you can also choose to auto update on login.<br>
<br>
or<br>
<br>
Download http://cg-monitor.com/update/update.zip and unzip to your webhosting.<br>
<br>

<a name="faq"></a>

---

### FAQ:

**Q. Can i trust the source of cg-monitor?**

**A.** Yes you can. We have made it with the greatest security measures and total open source, you can check the source if you don't trust us.

---

**Q. Do i need to change something to my config file?**

**A.** Your mining utility (cgminer / sgminer etc.) requires that API write access is allowed. Here's an example:

    ...
    "api-allow" : "W:127.0.0.1",
    "api-listen" : true,
    "api-port" : "4028",
    ...

---

**Q. How can i disable sync on my rig?**

**A.** You can always stop the sync by comment the line out in the cron "/etc/crontab" or disable the task in "Windows Task Scheduler"

---

**Q. Why are my pools not synced from my miner to cg-monitor?**

**A.** You need to add your pools to cg-monitor self.  The application needs to know the pools algorithm, the password, etc...  When you switch to a pool in cg-monitor, the application will push the pool to the miners/rigs.

---

**Q. Why do i need to setup a backup pool**

**A.** When your first pool is dead cg-monitor will automatic switch to your backup pool.  So you need to add a backup pool for every algorithm.

---

### Our Donation Addresses:

**Bitcoin**<br>
<small>1JcBmddyvWUMoBM7exCq2NwLtmF8vyprAn</small>

**Litecoin**<br>
<small>LhWGLVWquMnvbNErjZSmmfc4LEw9wF1aop</small>

---

