<?php 
include_once("../includes/db/generators.inc.php");
$collection_list = generator_collection_list();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="stylesheet" type="text/css" href="/css/main.css">
<style type="text/css">
th,td{
	padding:0 1em 0 1em;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Test Problem Server</title>
</head><body>
<div id="wrapper">
	<div id="titlebar">
		<?php include("../includes/templating/title.inc.php"); ?>
	</div>
	   <div id="login">
              <?php require("../includes/templating/login.inc.php"); ?>
	   </div>

	<div id="menu">
		<?php include("./menu.inc.php"); ?>
	</div>
	<div id="sidemenu">
		<?php include("./submenu.inc.php"); ?>
	</div>
	<div id="content">
<h1>Comparison of Selected Problem Generators</h1>  



<table border="1">
<tr>
<th>Property</th>
<th><a href="https://tps.tacc.utexas.edu/matrices/build.php?#collection,17">HP3D</a></th>
<th><a href="https://tps.tacc.utexas.edu/matrices/build.php?#collection,14">libMesh</a></th>
<th><a href="https://tps.tacc.utexas.edu/matrices/build.php?#collection,23">Defiant</a></th>
</tr>



<tbody>
<tr>
<td><em>Increasing Discretization</em></td>
<td>Adaptive</td>
<td>Uniformly</td>
<td>Uniformly</td>
</tr>

<tr>
<td><em>Problem Coeffcients</em></td>
<td>Constant Anisotropy</td>
<td>Constant</td>
<td>Constant with Random Noise</td>
</tr>

<tr>
<td><em>Finite Elements</em></td>
	<td>Vertex, Edge, Face, Interior</td>
<td>Lagrange and Heirarchical</td>
	<td>&nbsp;</td>
</tr>

<tr>
<td><em>Domain Choices</em></td>
<td>Re-enterant corner</td>
<td>Brick with holes</td>
<td>Brick with patches removed</td>
</tr>

<tr>
<td><em>Systems from Newton Iterators</em></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>

<tr>
<td><em>Systems from time-stepping</em></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
<td>X</td>
</tr>

<tr>
<td><em>Element Matrix Format</em></td>
<td>X</td>
<td>Coming Soon</td>
	<td>&nbsp;</td>
</tr>

</tbody>

</table>
<br>
<h2>Or <a href="build.php">see entire set</a> of generator collections</h2>

	</div>
	<div id="footer">
	</div>
</div>
</body></html>
