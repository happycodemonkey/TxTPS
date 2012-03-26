<?php

echo "<h3>Testing PETSC Loading</h3>";
echo "<p><b>Command: <b>/opt/modules/bin/modulecmd csh list</b></p>";
echo "<p>". passthru("/opt/modules/bin/modulecmd csh list 2>&1")."</p>";

echo "<p><b>Command: <b>/opt/modules/bin/modulecmd bash list</b></p>";
echo "<p>". passthru("/opt/modules/bin/modulecmd bash list 2>&1")."</p>";

echo passthru('whoami');
echo "<br />";
echo passthru('env');
?>
