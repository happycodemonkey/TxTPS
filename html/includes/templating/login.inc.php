		<?php
		require_once(dirname(__FILE__) . "/../classes/Security.class.php");
		$security = Security::getInstance();
		if(!$security->isLoggedIn()){
		?>
			<form action="https://tps.tacc.utexas.edu/login.php" method="post">
				<ul>
		    <li><label for="login_email">Email</label></li><li><input id="login_email" size="30" type="text" value="username@example.com"  name="email" onClick="this.value='';" style="border:none;"></li>
					<li><label for="login_password">Password</label></li><li><input id="login_password" size="15" type="password" value="password" name="password" onClick="this.value='';" style="border:none;"></li>
					<li><input type="submit" value="Login" style="display:inline;"></li>
					<li><a class="loginlink" href="/profile/reset.php">Lost it?</a></li>
				</ul>
			</form>
		<?php
		}else{
			echo "<ul>";
		        echo "<li><strong>".  $security->getUser()->getFirstName();
			echo "&nbsp;&nbsp;(" . $security->getUser()->getEmail() .")</strong></li>";
			
			if($security->getClassName() == "Admin"){
			  echo '<li><a class="loginlink" href="/admin/">Admin Controls</a></li>';
			}

			echo "<li><a class=\"loginlink\" href=\"/logout.php\">Logout</a></li>";
                        echo "</ul>";
		}//end login check
		?>
