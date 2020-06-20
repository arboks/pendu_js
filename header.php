<?php session_start (); 

if (isset($_SESSION['userip']) === false)
{
    $_SESSION['userip'] = $_SERVER['REMOTE_ADDR'];
}

if ($_SESSION['userip'] !== $_SERVER['REMOTE_ADDR'])
{
    session_unset();
    session_destroy(); 
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="CSS/style.css" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
	</head>

    <body>
    	<?php @ $db = new mysqli("localhost","root","","bdd_javascript"); ?>
    	<header>
            <div class="enseigne">	
                <a href="homepage.php"><img src="images/enseigne.png" alt="enseigne" /></a>	
            </div>  
            <nav>
	            <ul>
				</ul>

				<ul class="signup">
					<?php
					if(isset($_SESSION['username']) && !empty($_SESSION['username']))
					{
						?>
						<p class="emaildisplay"><span class="color"> Connecté en tant que : </span><?php echo $_SESSION['username']; ?></p>
						<form method="POST" action="">
							<input class="signbuttons" class="logout" type="submit" name="logout" value="Logout" />
						</form>
						<?php
					}
					else
					{
						?>
						<button class="signbuttons" onclick="document.getElementById('id01').style.display='block'"><img class="img_nav" src="images/plus.png" alt="regles" /></button>
						<div id="id01" class="modal">
							<span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close">&times;</span>

							<form method="POST" class="modalform" action="">
								<div class="container">
									<label>Email <br/></label>
									<input type="text" placeholder="Enter Email" name="email" required>
									<br/>

									<label>Mot de passe <br/></label>
									<input type="password" placeholder="Enter Password" name="password" required>
									<br/>

									<label>Répeter le mot de passe <br/></label>
									<input type="password" placeholder="Repeat Password" name="password_repeat" required>
									<br/>
									<input type="submit" value="Submit" />
								</div>
							</form>
						</div>

						<button class="signbuttons" onclick="document.getElementById('id02').style.display='block'"><img class="img_nav" src="images/sign-in.png" alt="regles" /></button>

						<div id="id02" class="modal">
							<span onclick="document.getElementById('id02').style.display='none'" class="close" title="Close">&times;</span>

						  	<form method="POST" class="modalformlog animate" action="">
						    	<div class="container">
						      		<label>Nom d'utilisateur</label>
						      		<input type="text" placeholder="Enter Username" name="username" required>

						      		<label>Mot de passe</label>
						      		<input type="password" placeholder="Enter Password" name="password" required>

						      		<input type="submit" value="Submit" />
						    	</div>
						  	</form>
						</div>
						<?php
					}
					?>
				</ul>
			</nav>

			<?php

			if (isset($_POST['email'], $_POST['password'],$_POST['password_repeat'])) 
			{
			    $getusername = $_POST['email'];
			    $psw = sha1($_POST['password']);
			    $psw_repeat = sha1($_POST['password_repeat']);

			    if ($psw == $psw_repeat)
			    {
			    	$sql = $db->prepare('SELECT username FROM user WHERE username = \''.$getusername.'\';');
					$sql->execute();
					$res = $sql->fetch();

					if ($res)
					{
						?>
						<div class="headeralert">
							<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
							Cette email est déjà utilisé !
							<?php
							header("location:homepage.php");
							?>
						</div>	
						<?php	    
					}
					else
					{
					    $req = $db->prepare("INSERT INTO user (username,password) VALUES (?,?)");
					    $req->bind_param("ss", $getusername,$psw);      
					    $req->execute();
					    $req->close(); 
					    ?>
					    <div class="headeralert">
							<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
							Votre compte a été créé ! 
							<?php
							header("location:homepage.php");
							?>
						</div>
						<?php
					}
			    }
			    else
			    {
			    	?>
					<div class="headeralert">
						<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
						Les mots de passe ne correspondent pas !
						<?php 
						header("location:homepage.php");
						?>
					</div>	
					<?php
			    }
			}

			if (isset($_POST['username'], $_POST['password'])) 
			{
			    $getusername = $_POST['username'];
			    $psw = sha1($_POST['password']);

			    $sql = $db->prepare("SELECT username, password FROM user WHERE username = '".$getusername."' and password = '".$psw."'");
				$sql->execute();

				$res = $sql->fetch();

				if ($res)
				{
					header("location:homepage.php");
					$_SESSION['username'] = $getusername;
				}
				else
				{
					?>
					<div class="headeralert">
						<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
						Le nom d'utilisateur ou le mot de passe est incorrect ! 
					</div>	
					<?php
				}
			}

			if(!empty($_POST['logout'])) 
			{
			   session_destroy();
			   ?>
			   <div class="headeralert">
					<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
					Logout
					<?php
					header("location:homepage.php");
					?>
				</div>	
				<?php
			}

			?>
    	

	    	<script>
	    		function myFunction() {
	  				document.getElementById("myDropdown").classList.toggle("show");
				}

				window.onclick = function(event) {
					if (!event.target.matches('.dropbtn')) {
						var dropdowns = document.getElementsByClassName("dropdown-content");
						var i;
						for (i = 0; i < dropdowns.length; i++) {
							var openDropdown = dropdowns[i];
							if (openDropdown.classList.contains('show')) {
								openDropdown.classList.remove('show');
							}
						}
					}
				}
			</script>
		</header>