		<ul>
			<li><a class="menulink" href="/">TxTPS</a></li>
			<li><a class="menulink" href="/matrices/">Test Problems</a></li>
          		<li class="selected"><a class="menulink" href="/search/">Search</a></li>
		        <?php
                        $sec = Security::getInstance();
                        if($sec->isLoggedIn()){
			  echo '<li><a class="menulink" href="/profile/">Profile</a></li>';
			}?>

<!--
		        <li><form action="/search/index.php" method="get"><input size="45" name="keyword" type="text" value="Tag or Keywords..." onClick="if(this.value == 'Tag or Keywords...'){this.value=''; document.getElementById('searchsubmit').disabled=false}" style="border:none;"></li><li><input disabled="true" id="searchsubmit" type="submit" value="Search" style=""></form></li>
	-->	       
		</ul>
		
		
