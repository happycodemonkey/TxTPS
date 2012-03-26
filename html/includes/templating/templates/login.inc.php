		<?php
		require_once(dirname(__FILE__) . "/../util/security.inc.php");
		if(!$auth->is_logged_in()){
		?>
			<form action="login.php" method="post">
				<ul>
					<li><input size="15" type="text" value="E-Mail Address"  name="email" onClick="this.value='';" style="border:none;background-color:#EEE;color:#0CF;"></li>
					<li><input size="15" type="password" value="password" name="password" onClick="this.value='';" style="border:none; background-color:#EEE;color:#0CF;"></li>
					<li><input type="submit" value="Login" style=""></li>
				</ul>
			</form>
		<?php
		}else{
			echo "<p>".$auth->get_user_email()."</p>";
			echo "<p><a href=login.php?logout=true>Logout</a></p>";
		}//end login check
		?>
