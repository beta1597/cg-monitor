<?php
	// make a new Token so send with the form
	$token = makeToken();
	
	// check if there is a new user and password request ( request is made by changing the value1 of row user in the settings table by your own secret_passphrase 
	$sql 	= "SELECT value1 FROM `cgmonitor__settings` where name = 'user'";
	$user 	= result($sql);
	
	if($user[0]["value1"] == $GLOBALS['secret_passphrase']){
		$resetsettings  = true;
		$title 			= "New account";
	}else{
		$resetsettings  = false;
		$title 			= "Login";	
	};
	
?>
    <style type="text/css">
      .container {
			position:fixed;
			left: 50%;
			width: 300px;
			margin-left: -150px; /*set to a negative number 1/2 of your width*/
			border: 3px solid #525B52;
			background-color: #f3f3f3;
		}

      /* The white background content wrapper */
      .container > .contentlogin {
        background-color: #fff;
        padding: 20px;
        margin: 0 -20px; 
        -webkit-border-radius: 10px 10px 10px 10px;
           -moz-border-radius: 10px 10px 10px 10px;
                border-radius: 10px 10px 10px 10px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
      }

    </style>
	<script src="./js/sha3.js"></script>
	<script src='./js/rijndael.js'></script>
	<script src='./js/mcrypt.js'></script>

</head>
<body>
  <div class="container">
    <div class="contentlogin">
      <div class="row">
        <div class="login-form">
			<h1><?php echo $title;?></h1>
			<div style="clear:both"></div>
			<form method="post" name="formlogin" id="formlogin">
				<input type="hidden" name="usernameEncrypt" id="usernameEncrypt" value="">
				<input type="hidden" name="passwordEncrypt" id="passwordEncrypt" value="">
				<input type="hidden" name="token" id="token" value="<?php echo md5($token);?>" />
				<input name="action" type="hidden" value="login">
<?php
if($resetsettings){
?>
                <input type="text" placeholder="New Username" class='form-control' name="username" id="username" ><br/>
                <input type="password" placeholder="New Password" class='form-control' name="password" id="password" ><br/>
                <input type="password" placeholder="Repeat Password" class='form-control' name="password2" id="password2" ><br/>
                <button class='btn btn-default'  type="submit" id="submit" name="submit" disabled >Change user</button>
<?php
}else{
?>
               <input type="text" placeholder="Username" class='form-control' name="username" id="username" ><br/>
               <input type="password" placeholder="Password" class='form-control' name="password" id="password" ><br/>
               <button class='btn btn-default'  type="submit" id="submit" name="submit" >Sign in</button>
<?php
};
?>
			</form>
        </div>
      </div>
    </div>
  </div> <!-- /container -->
</body>
</html>
<script>
<?php
if($resetsettings){

		// some javascript stuff to secure things 
		
		// look if the password are the same
		
		// crypt the username and password to sha1 before sending to the server ( basic security )

?>
		$( "#password" ).keyup(function() { checkpassword(); });
		$( "#password2" ).keyup(function() { checkpassword(); });

		function checkpassword(){
			if($('#password').val() == $('#password2').val() && $('#password').val().length >= 6 ){
				$('#submit').removeAttr("disabled");
			}else{
				$('#submit').attr("disabled", "disabled");
			};
		};

<?php
};
?>
		$('#formlogin').submit(function() {
				if($('#username').val() != ""){
					if($('#password').val() != ""){

						usernameEncrypt = CryptoJS.SHA3($('#username').val());
						passwordEncrypt = CryptoJS.SHA3($('#password').val());
						usernameEncrypt = usernameEncrypt.toString(CryptoJS.enc.Base64);
						passwordEncrypt = passwordEncrypt.toString(CryptoJS.enc.Base64);
						
						usernameEncrypt = toHex(mcrypt.Encrypt(usernameEncrypt, '', '<?php echo $token;?>', 'rijndael-256', 'ecb'));
						passwordEncrypt = toHex(mcrypt.Encrypt(passwordEncrypt, '', '<?php echo $token;?>', 'rijndael-256', 'ecb'));
											
						$('#usernameEncrypt').val(usernameEncrypt);
						$('#passwordEncrypt').val(passwordEncrypt);
						
						$('#username').val("");
						$('#password').val("");
						$('#password2').val("");
		
						return true;
					}else{
						$( "#errormessage" ).html("No password!!");
						$( "#dialog-error" ).dialog( "open" );
						event.preventDefault();				
						return false;
					};
				}else{
					$( "#errormessage" ).html("No username!!");
					$( "#dialog-error" ).dialog( "open" );
					event.preventDefault();				
					return false;
					
				};
			});
		
		  $(function() {
			$( "#dialog-error" ).dialog({
			  autoOpen: false,
			  modal: true,
			  buttons: {
				Ok: function() {
				  $( this ).dialog( "close" );
				}
			  }
			});
		  });
		  </script>
		<div id="dialog-error" title="Error">
		  <p>
			<span id="errormessage" name="errormessage"></span>
		  </p>
		</div>