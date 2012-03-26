{assign var=menus 
  value=['Create'
            =>[
                ['Collections', '/collections/'],
                ['Generators', '/generators/'],
                ['Finite Element', '#'],
                ['Matrix Market', '#'],
                ['Very Large', '#']
              ],
         'Research'
            =>[
                ['Stored Problems', '/problems/'],
                ['File Formats', '/about/formats.php'],
                ['Using TxTPS', '/about/how.php'],
                ['Software', '/about/software.php']
              ],
          'Explore'
            =>[
                ['About TxTPS', '/about/'],
                ['Contact US', '/about/contact.php'],
                ['News', '#'],
                ['FAQ', '/about/faq.php']
              ]
       ]}
<html>
<head>
{block name=head}
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.simplemodal.js"></script>
<script type="text/javascript" src="/js/jquery.lightbox-0.5.js"></script>
<script type="text/javascript" src="/js/MathJax/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script> 
<script type="text/javascript" src="/js/tps.js"></script>

<title>TxTPS - {block name=title}Texas Test Problem Server{/block}</title>
<link rel="stylesheet" type="text/css" href="/default.css">
<link rel="stylesheet" type="text/css" href="/js/css/jquery.lightbox-0.5.css" media="screen">
<style>
#profilebox, #adminbox{
   display:block;
  padding:10px 0;
}
</style>
<script>
var user = {$user|default:"null"};
</script>
<script>
{literal}



$(document).ready(function(){
  tps_ui_customize();
  tps_ui_headermenus();

});
{/literal}
</script>
{/block}
</head>
<body>
<div id="topbar_container">
  <div id="toptopbar"></div>
  <div id="topbar">
    <div id="topbar_left">
      <ul>
	<li id="topbar_tacclogo">
	  <a href="http://www.tacc.utexas.edu"><img src="/images/tacc_s.png" height="20px"></a>
	</li>
	<ul>
    </div>
    <div id="topbar_right">
      <ul>
	<li>
	  <div id="authbox_container">
	    <div id="authbox_leftedge"></div>
	    <div id="authbox">
	 
	    </div>
	    <div id="authbox_rightedge"></div>
	  </div>
	</li>
	<li>
	  <a href="/about/help.php" class="help_link">Help</a>
	</li>
	<li>
	  <a href="/about/contact.php" class="contact_link">Contact</a>
	</li>
	<li>
	  <a href="/about/" class="contact_link">About</a>
	</li>
      </ul>
    </div>
  </div>
</div>
<div id="outer_container">
  <div id="header_container">
    <div id="header">
      <header>
	<p>
	  <a href="/" title="TxTPS Homepage" class="logo"><img src="/images/TxTPS.png" height="75"/></a>
	</p>
	<ul>
	 {foreach $menus as $name=>$menu}
	  <li class="header_menu">
	    <div class="header_menu_name"><h2> <a  href="#">{$name}</a><span class="header_menu_arrow"></span></h2></div>
            <div class="header_menu_body" style="">
	      <ul>
	      {foreach $menu as $menu_item}
	        <li><a href="{$menu_item[1]}">{$menu_item[0]}</a></li>
	      {/foreach}
	      </ul>
	    </div>
          </li>
	  {/foreach}
	  <li id="header_search_container">
	    <input id="header_search" type="text"/>
	  </li>
	</ul>
      </header>
    </div>
  </div>
  <div id="content_container">
  {block name=content_container}
    <div id="content">
    {block name=content}{/block}
    </div>
    
    <div id="sidebar">
    {block name=sidebar}{/block}
    </div>
  {/block}
  </div>
  <div id="footer_container">
    <footer id="footer">
    {block name=footer}
      {foreach $menus as $name=>$menu}
      <section class="footer_menu_list">
	<h2>{$name}</h2>
	<ul>
	{foreach $menu as $menu_item}
	  <li><a href="{$menu_item[1]}">{$menu_item[0]}</a></li>
	{/foreach}
	</ul>
      </section>
      {/foreach}
      <section id="footer_quote">
	<span>
	  <p><abbr title="Texas Test Problem Server">TxTPS</abbr> is a project of <abbr title="Texas Advanced Computing Center">TACC</abbr>. </p>
	  <p>The goal of the project is to provide a centralized resource for generation and storage of relevant test problems for numerical algorithm research.</p></span>
      </section>
      <section id="footer_logos">
	<a href="http://tacc.utexas.edu"><img src="/images/tacc.png" style="height:40px;float:left;"></a>
	<a href="http://utexas.edu"><img src="/images/utexas.png" style="height:48px;float:right;"></a>
	<section id="footer_copyright">&copy; 2011 tps.tacc.utexas.edu</section>
      </section>
    {/block}
    </footer>
  </div>
<div style="display:none">
{block name=hidden}
  <div id="login_dialog" class="dialog">
    <h1>Login</h1>
    <form onsubmit="tps_auth_login();return false;">
    <label>Email</label>
    <input type="text" name="email"/>
    <label>Password</label>
    <input type="password" name="password"/>
    <p style="color:#ccc;">Need to <a  href="#" onclick="tps_ui_register()">register</a> first?</p>
    <p><input type="submit" value="Login"></input></p>
    </form>
  </div>  
  <div id="register_dialog" class="dialog">
    <h1>Register</h1>
    <label>Email *</label>
    <input type="text" />
    <label>Password</label>
    <input type="password" />
    <label>Confirm Password</label>
    <input type="password" />
    <p>* An email will be sent to the above address. You will need to click on the validation link contained in this email to complete registration.</p>
    <p>Already have an account? <a href="#" onclick="tps_ui_login()">Login here</a>.</p>    
    <p><input type="button" value="Register" onclick="tps_auth_register()"></input></p>
  </div>
  <div id="profilebox"  onmouseover="$('#profilebox_normal').hide();$('#profilebox_hover').show();" onmouseout="$('#profilebox_hover').hide();$('#profilebox_normal').show();">
    <div id="profilebox_normal" style="width:100%;text-align:center">
      <span class="user_email"></span>
    </div>
    <div id="profilebox_hover" style="display:none;width:100%;text-align:center;">
      <ul>
	<li style="float:left;margin-left:17px;"><a href="#" onclick="tps_auth_logout();">Logout</a></li>
	<li style="float:left;margin-left:17px;"><a href="#">Profile</a></li>
	<li style="float:left;margin-left:17px;"><a href="#">History</a></li>
      </ul>
    </div>
  </div>
  <div id="adminbox"  onmouseover="$('#adminbox_normal').hide();$('#adminbox_hover').show();" onmouseout="$('#adminbox_hover').hide();$('#adminbox_normal').show();">
    <div id="adminbox_normal" style="width:100%;text-align:center">
      <span style="color:#F00;" class="user_email"></span>
    </div>
    <div id="adminbox_hover" style="display:none;width:100%;text-align:center;">
      <ul>
	<li style="float:left;margin-left:17px;"><a href="#" onclick="tps_auth_logout();">Logout</a></li>
	<li style="float:left;margin-left:17px;"><a style="color:#F00;" href="/admin">Admin</a></li>
	<li style="float:left;margin-left:17px;"><a href="#">History</a></li>
      </ul>
    </div>
  </div>
  <div id="loginbox">
    <ul>
      <li style="float:left;margin-left:35px;"><a href="#" onclick="tps_ui_login();">Login</a></li>
      <li style="float:right;margin-right:35px;"><a href="#" onclick="tps_ui_register();">Register</a></li>
    </ul>
  </div>
<div id="build_sheet">

<h1>Create a Problem</h1>
<!-- 

<h2><span class="collection_name">Collection</span> / <span class="generator_name">Generator</span></h2>
--!>
<p><span>
<span id="build_sheet_step_label_1" class="build_sheet_step_label" style="font-weight:800;font-size:14px;">Step 1: Adjust Arguments </span> 
&gt; 
<span id="build_sheet_step_label_2" class="build_sheet_step_label">Step 2: Problem Notes</span> 
&gt; 
<span id="build_sheet_step_label_3" class="build_sheet_step_label">Step 3: Submit Problem</span> 
&gt;
<span id="build_sheet_step_label_4" class="build_sheet_step_label">Processing</span> 
</span></p>

<div id="build_sheet_step_1" class="build_sheet_step">
<h2>Step 1: Adjust Arguments <span id="build_page_label"></span></h2>
<div style="margin-bottom:10px;float:left">
<form id="build_form">
<div id="build_arguments" style="float:left;width:600px;height:300px;">
</div>
</form>
</div>
<input type="button" id="tps_build_table_previous_button" style="float:left"  onclick="tps_build_table_prevpage()" value="<< Previous Parameters">
<input type="button" id="tps_build_table_next_button" style="float:right" onclick="tps_build_table_nextpage()" value="Next Parameters >>">
<input type="button" value="Step 2: Problem Notes" onclick='tps_build_step(2)' style="position:absolute;bottom:10px;right:10px"></div>

<div id="build_sheet_step_2" class="build_sheet_step" style="display:none;">
<h2>Step 2: Problem Notes</h2>
<textarea style="font-size:12pt;" rows="10" cols="30"></textarea>
<br>
<input type="button" value="Step 1: Adjust Arguments" onclick='tps_build_step(1)' style="position:absolute;bottom:10px;left:10px">
<input type="button" value="Step 3: Submit Problem" onclick='tps_build_step(3)' style="position:absolute;bottom:10px;right:10px">

<p>Your paramaters have been validated and your problem is ready to be submitted.<p>

</div>
<div id="build_sheet_step_3" class="build_sheet_step" style="display:none;">
<h2>Step 3: Submit Problem</h2>
<input type="button" value="Step 2: Problem Notes" onclick='tps_build_step(2)' style="position:absolute;bottom:10px;left:10px">
<input type="button" value="Submit Problem" onclick='tps_build_submit()' style="position:absolute;bottom:10px;right:10px">
</div>
<div id="build_sheet_step_4" class="build_sheet_step" style="display:none;">
<h2>Problem #<span class="problem_identifier"></span> is Being Created</h2>
<h3>Status:   <span class="problem_status"></span><span class="problem_updating"><img src="/images/spin_666.gif"/></span></h3>

<p>Your problem has been queued for creation. This should only take a short time, but depends on the length of the queue and the complexity of your problem. You will recieve an email when this is complete. You may close your browser window or leave it open to see the current problem status. You will be redirected when the problem has been processed.</p>





</div>
{/block}
</div>
</body>
</html>
