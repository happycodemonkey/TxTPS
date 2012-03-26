<?
include_once("../includes/db/users.inc.php");
include_once("../includes/db/generators.inc.php");
include_once("../includes/db/data.inc.php");
require_once("../includes/classes/Security.class.php");

$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("/login.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/tpsadmin.js"></script>

<script type="text/javascript">

var generator_id = <?php echo $_GET['id']; ?>;
var generator_name;
var collection_name;

var changed = false;
var loaded  = false;

function start(){
  (function(){ 
  old = "";
  setInterval(function(){
	  if(old !=window.location.hash){ //changed
	     hash_handler(window.location.hash);
	     old = window.location.hash;
	   }
	  },100);
  }());
  
  foo();

  $("#overview_tab_label").click(function(){
    set_tab("overview");
  });

  $("#arguments_tab_label").click(function(){
    set_tab("arguments");
  });

  $("#images_tab_label").click(function(){
    set_tab("images");
  });

  $("#test_tab_label").click(function(){
    set_tab("test");
  });

  $("#validation_tab_label").click(function(){
    set_tab("validation");
  });

  $("#processing_tab_label").click(function(){
    set_tab("processing");
  });

  $("#delete_tab_label").click(function(){
    set_tab("delete");
  });

  load(generator_id);
  hash_handler(window.location.hash);

}

  function set_tab(tab){
    //check if changes have occured
    if(changed){
      ignore_changes = confirm("You have made changes without saving, do you wish to continue and lose these?");
      if(!ignore_changes){
        return;
      }
      changed = false;
    }


    window.location.hash= "" + tab;
  }

  function hash_handler(hash){
    var tab;
   
    if(hash.length < 3){ //no valid hash
      tab = "overview";
    }else{ // valid hash
      tab = hash.substr(1);  //remove #
    }

    $("div.tab").hide();
    $("div.#" + tab).show();

    $(".tab_button").removeClass("selected");
    $("#" + tab + "_tab_label").addClass("selected");
  }

  function load(id){
    generator_id  = id;    

    if(loaded){
      return;
    }

    //load generator
    gen_url = "/tacc/api.php?path=/generators/" + generator_id;
    $.get(gen_url,function(data){
      generator = JSON.parse(data);
      $("#generator_name").val(generator["name"]);    
      $(".generator_name").text(generator["name"]);    
      $("#generator_description").val(generator["description"]);    
      $("#generator_script").val(generator["script"]);
      								  
      collection_id = generator['collection_id'];      
       
      //load collection list
      gen_url = "/tacc/api.php?path=/collections/";
      $.get(gen_url,function(data){

        collections = JSON.parse(data);      
      	collection_select = $("#generator_collection");
      	for (i in collections){
          c = collections[i];
          opt = $("<option>");
      	  opt.val(c.id);
	  opt.text(c.name);
	  if(c.id == collection_id){
	    opt.attr("selected",true);
	  }
	  opt.appendTo(collection_select);
        }

      });
    });


    //add overview change events
    var overview_save_function = (function(){
    	//show spinner and status
	$("#overview").find(".section_status").text("Saving...");
	$("#overview").find(".section_save").css("display","block");
	//save
	save_overview();
	//show status
	$("#overview").find(".section_status").text("Saved!");
	setTimeout(function() { $("#overview").find(".section_save").fadeOut(); },1000);
    });
    $("#overview").find("input,select,textarea").change(overview_save_function);



    //load arguments
    $(".argument").remove();
    arg_url = "/tacc/api.php?path=/arguments/?generator_id=" + generator_id;
    jQuery.ajax({type: "GET", url: arg_url, success: function(arg_json){

      args = JSON.parse(arg_json);
      for (i in args){
        insert_argument(args[i]);
      }      


    },error: function(){
      alert("Could not load Arguments for Generator " + generator_id);
    }});


    //load scripts
    $(".script").remove();
    script_url = "/tacc/api.php?path=/scripts/?generator_id=" + generator_id;
    $.ajax({type:"GET",url:script_url, success: function(script_json){
      
      scripts = JSON.parse(script_json);
      for (i in scripts){
        insert_script(scripts[i]);
      }
    },error: function(){
      alert("Could not load Scripts for Generator " + generator_id);
    }});
    
  }


  function new_rule(){
    rule = Object;
    insert_rule(rule);
  }

  function insert_rule(rule){
    rule_div = $("<div>");
    rule_div.css("border-top","1px solid black");
    rule_div.css("margin-top","3em");
    rule_div.addClass(rule);
    rule_div.appendTo($("#validation"));
    
    //add rule header
    rule_div.append("<h2>Rule (ID#: "+rule.id+" )</h2>");
  
    //rule form
    rule_form = $("<form>");
    rule_form.attr("id","ruleform_" + rule.id);
    rule_form.appendTo(rule_div);

    //name
    rule_nlabel = $("<label>");
    rule_nlabel.text("Name (Optional)");
    rule_nlabel.appendTo(rule_form);    

    rule_form.append("<br>");

    rule_name = $("<input>");
    rule_name.attr("type","text");
    rule_name.attr("name","name");
    rule_name.appendTo(rule_form);
    rule_form.append("<br>");

    //error message
    rule_elabel = $("<label>");
    rule_elabel.text("Error Message");
    rule_elabel.appendTo(rule_form); 
    rule_form.append("<br>");   
    rule_error = $("<input>");
    rule_error.attr("type","text");
    rule_error.attr("name","error");
    rule_error.appendTo(rule_form);    
        rule_form.append("<br>");
    //rule code
    rule_clabel = $("<label>");
    rule_clabel.text("Validation Code");
    rule_clabel.appendTo(rule_form);
    rule_form.append("<br>");    
    rule_code = $("<textarea>");
    rule_code.attr("name","code");
    rule_code.appendTo(rule_form);
    


    
  }


  function delete_rule(id){

  }

  function save_rule(id){


  }

  function new_script(){

    apiurl = "/tacc/api.php?path=/scripts/";
    script = new Object;
    script["generator_id"] = generator_id;
    script_json = JSON.stringify(script);
    jQuery.ajax({type: "POST", data:script_json, url: apiurl, contentType:"application/json", success: function(result){
      arg = JSON.parse(result);
      alert("Success");
    
    },error: function(){
       
       alert("Could not add Script");

     }});
  }

  function insert_script(script){
    
    //add new script to page
    script_div = $("<div>");
    script_div.appendTo($("#processing"));
    script_div.attr("id","script_" + script.id);
    script_div.addClass("script");
    
    //build form
    script_form = $("<form>");
    script_form.appendTo(script_div);
    script_form.attr('id',"scriptform_" + script.id);
	
    form_table = $("<table>");
    form_table.appendTo(script_form);
    header_row = $("<tr><th>Field</th><th>Value</th><th>Save</th></tr>");
    header_row.appendTo(form_table);
    
    path_row = $("<tr>");
    path_row.appendTo(form_table);    
    
    path_label  = $("<label>");
    path_label.text("Path");
    cell = $("<td>");
    cell.append(path_label);
    cell.appendTo(path_row);
    path_input  = $("<input>");
    path_input.attr('type','text');
    path_input.attr('name','path');
    path_input.attr('value',script.path);
    path_input.text("Path");
    cell = $("<td>");
    cell.append(path_input);
    cell.appendTo(path_row);

    cell = $("<td>");
    save_button = $("<input>");
    save_button.val("Save");
    save_button.attr('type','button');
    save_button.click(function(){
      save_script(script.id);
    });
    cell.append(save_button);
    cell.appendTo(path_row);

  }

  function save_script(id){

    script_url    = "/tacc/api.php?path=/scripts/" + id;
    form   = $("#scriptform_" + id);
    fields = form.serializeArray();
    script    = new Object;
    
    $.each(fields,function(key,value){
      script[value.name] = value.value;
    });


    script_json = JSON.stringify(script);
    alert(script_json);

    
    jQuery.ajax({type: "PUT", url:script_url,data:script_json,success: function(data){
      alert("Saved:" + data);
    },error:function(){
      alert("Could not save");
    }});


  }


  function new_argument(){



    apiurl = "/tacc/api.php?path=/arguments/";
    argument = new Object;
    argument["generator_id"] = generator_id;
    arg_json = JSON.stringify(argument);
    jQuery.ajax({type: "POST", data:arg_json, url: apiurl, contentType:"application/json", success: function(result){
      arg = JSON.parse(result);
      insert_argument(arg);    
    },error: function(){
       alert("Could not add Argument");
     }});
  }


  function insert_argument(arg){

      //add new argument to page
      var fields_div  = $("#argument_template").clone();
      var arguments   = $("#arguments");
      var argument_id = "arg_" + arg.id;
      fields_div.attr("id",argument_id);
      //tag the form with a matching ID
      fields_div.find("form").attr("id","argform_" + arg.id);
      arguments.append(fields_div);
      fields_div.addClass("section");
      fields_div.addClass("argument");

      //hide help
      $("#argument_help").hide();

      //set section title
      var argument_count = arguments.find(".argument").size();
      fields_div.find("h2").text("Argument # " + (argument_count));
      
      //fill in values
      fields_div.find("input[name=name]").val(arg.name);
      fields_div.find("input[name=variable]").val(arg.variable);
      fields_div.find("input[name=default_value]").val(arg.default_value);
      fields_div.find("select[name=type]").val(arg.type);
      fields_div.find("input[name=description]").val(arg.description);
      fields_div.find("input[name=options]").val(arg.options);

      //attach type change function
      var type_function = (function(){ //type change handler
        //find new type
	var type = $("#arg_" + arg.id).find("select[name=type]").val();
	//toggle select options div
	if(type == "SELECT"){
	  $("#arg_" + arg.id).find(".argument_select_options").show();
	}else{
	  $("#arg_" + arg.id).find(".argument_select_options").hide();
	}
      });
      fields_div.find("select[name=type]").change(type_function);
      fields_div.find("select[name=type]").change();

      //attach change function (for autosave)
      var save_function = (function(){ //save handler
        //show spinner and status
	$("#arg_" + arg.id).find(".section_status").text("Saving...");
	$("#arg_" + arg.id).find(".section_save").css("display","block");
	//save
	save_argument(arg.id);	
	//show status
	$("#arg_" + arg.id).find(".section_status").text("Saved!");
	setTimeout(function() { $("#arg_" + arg.id).find(".section_save").fadeOut(); },1000);
      });
      fields_div.find("input,select").change(save_function); //attach

      //add listener functions for actions
      fields_div.find(".argument_action_delete").click((function(){
        delete_argument(arg.id);
      }));
      fields_div.find(".argument_action_new").click((function(){
        new_argument();
      }));
      fields_div.find(".argument_action_up").click((function(){
	alert("Not Implemented");
      }));
      fields_div.find(".argument_action_down").click((function(){
        alert("Not Implemented");
      }));



}


  function save_argument(id){

    var arg_url    = "/tacc/api.php?path=/arguments/" + id;
    var form   = $("#argform_" + id);
    var fields = form.serializeArray();
    var arg    = new Object;
    
    $.each(fields,function(key,value){
      arg[value.name] = value.value;
    });

    //split up options into an array
    arg['options'] = arg['options'].split(',');

    var arg_json = JSON.stringify(arg);
 

    
    jQuery.ajax({type: "PUT", url:arg_url,data:arg_json,success: function(data){
      //alert("Saved:" + data);
    },error:function(){
      //alert("Could not save");
    }});

  }


  function delete_argument(id){
    
    var do_delete = confirm("Are you sure you want to delete this argument?");
    if(do_delete){
      var arg_url    = "/tacc/api.php?path=/arguments/" + id;
      jQuery.ajax({type: "DELETE", url:arg_url,success: function(){
        $("#arg_" + id).remove();
	if($(".argument").size() < 1){
	  $("#argument_help").show();
	}
      },error:function(){
        alert("Could not Delete");
      }});
    }

  }
  

  function save_overview(){
    form   = $("#generator_form");
    fields = form.serializeArray();
    
    generator = new Object;

    for (i in fields){
      f     = fields[i];
      name  = f.name;
      value = f.value;
      generator[name] = value;
    } 
    


    gen_json = JSON.stringify(generator);
    gen_url  = "/tacc/api.php?path=/generators/" + generator_id;
    //alert(gen_json);
    jQuery.ajax({type:"PUT", data:gen_json,url:gen_url, success:function(data){
      //alert(data);
      $(".generator_name").text($("#generator_name").val());
    }, error: function(){
      alert("Could not Save");
    }});

  }
  
  function make_example(arg_id){

    //locate
    example_box = $("#ex_" + arg_id);
    
    //delete the table
    example_box.children("table").remove();
    
    //new table
    table   = $("<table>");
    table.appendTo(example_box);


    //header
    table.append("<tr><th>Name</th><th>Value</th></tr>");
    
    //get current options
    argform     = $("#argform_" + arg_id); 
    fields      = argform.serializeArray();
    argument    = new Object;
    
    for (i in fields){
      f     = fields[i];
      name  = f.name;
      value = f.value;
      argument[name] = value;
    }    
    
    
    //name
    ex_row = $("<tr>");
    table.append(ex_row);
    argname   = $("<td>" + argument["name"] + "</td>");
    argname.appendTo(ex_row);


    if(argument.type != "SELECT"){
      ex_input = $("<input>");
      ex_input.appendTo(ex_row);
      ex_input.val(argument.default_value);
    }else{
      ex_input = $("<select>");
      ex_input.appendTo(ex_row);
      ex_input.val(argument.default_value);
      options = argument.options.split(",");
      for (o in options){
        opt = $("<option>");
	opt.val(options[o]);
	opt.text(options[o])
	
	if(argument.default_value == opt.val()){
	  opt.attr("selected","selected");
	}
	ex_input.append(opt);
      }
    }
    
  }  

 function delete_generator(){

   $.ajax({
	url:"/tacc/api.php?path=/generators/" + generator_id,
	type: "delete",
	success: function(){
	  alert("The generator was deleted.");
	  window.location = "generator.php";
	},
	error: function(){
	  alert("An error occured and the generator was not deleted");
	}});
 }

 function test_generator(){

   //show test started
   
   $("#test_results").append($("<h3>Test Started</h3>"))
 

   $.ajax({type:"GET",url:"/tacc/api.php?path=/arguments/?generator_id=" + generator_id,success:function(data){
     var arguments     = JSON.parse(data);
     var test_problem  = new Object;
     var test_args     = new Object;
     $.each(arguments,function(i,field){
       test_args[field.variable] = field.default_value;
     });
     test_problem['generator_id'] = generator_id;
     test_problem['arguments']    = test_args;
     var test_json = JSON.stringify(test_problem);

     //show status and args
     $("#test_results").append($("<h3>Test Arguments:</h3>"))
     var argument_table = $("<table>");
     argument_table.append("<tr><th>Variable</th><th>Value</th></tr>");
     $("#test_results").append(argument_table);
     
     $.each(test_args, function(k,v){
       argument_table.append("<tr><td>" + k + "</td><td>" + v + "</td></tr>");
     });

 


     //submit problem
     $.ajax({contentType:"application/json", data:test_json,type:"POST",url:"/tacc/api.php?path=/products/", success: function(data){
       var product    = JSON.parse(data);
       var identifier = product["identifier"];
       $("#test_results").append($("<h3>Problem Submitted</h3>"));
       $("#test_results").append($("<p>Data Located At: /data/storage/"+identifier+"</p>"));  
       $("#test_results").append($("<p><a href='/matrices/matrix.php?id="+identifier+"'>View Result (Problem # "+identifier+" )</a></p>"));  
        
     }, error:function(){
      $("#test_results").append($("<h3>Error Submitting Problem</h3>")); 
      //could not build test problem
     }});

   },error: function(){
      $("#test_results").append($("<h3>Error Getting Arguments</h3>")); 
     //could not retrieve arguments
   }});
 }


</script>
<link  rel="stylesheet" type="text/css" href="../css/main.css" />
<style type="text/css">


.argument_action{
  display:inline-block;
}

.fields{
  display:block;
}


.argument{

}

.selected{
   font-weight:bold;
}

div.fields input[type=text]{
  width: 20em;
}

div.fields select{
  width: 20.5em;
}

div#overview input[type=text]{
  width: 20em;
}

div#overview select{
  width: 20.5em;
}

</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
</head>
<body onload='start()'>
<div id="wrapper">
  <div id="titlebar">
  <?php include("../includes/templating/title.inc.php"); ?>
</div>
<div id="menu">
  <?php include("../includes/templating/adminmenu.inc.php"); ?>
</div>

<div id="content">
  <h1><span id="overview_title"><a href="/admin/">Admin</a> / <a href="/admin/generators/">Generators</a> / <span class="generator_name"></span></span></h1>
  <div style="border-right-style:solid;border-right-width:1px;padding:2em;display:block;float:left;margin-right:2em;">
    <span id="overview_tab_label"   class="tab_button selected"> Overview </span>
    <span id="arguments_tab_label"  class="tab_button"> Arguments </span>
    <span id="images_tab_label"     class="tab_button"> Images</span>
    <span id="test_tab_label"       class="tab_button"> Test </span>
    <!-- 
    <span id="validation_tab_label" class="tab_button"> Validation </span>
    <span id="processing_tab_label" class="tab_button"> Processing </span>
    -->
    <span id="delete_tab_label" class="tab_button"> Delete </span>
  </div>

<div id="overview" class="tab">

<div class="section">
  <div class="section_save"><span class="section_status">Saving...</span><img src="/images/spin.gif"></div>
  <h2>Basics</h2>
  <form id="generator_form">
  <table>
    <tr><td><label for="generator_name">Name:</label></td><td><input type="text" name="name" value="" id="generator_name"></td></tr>
    <tr><td><label for="generator_collection">Collection:</label></td><td><select name="collection_id" type="select" value="" id="generator_collection"></td></tr>
    <tr><td><label for="generator_script">Script:</label></td><td><input type="text" size="50" name="script" value="" id="generator_script"></td></tr>
    <tr><td><label for="generator_description">Description:</label></td><td><textarea cols="80" rows="6" id="generator_description" name="description"></textarea></td></tr>
  </table>
  </form>
</div>
</div>




<div id="arguments" class="tab">

 <div class="section" id="argument_help">
 <h2>Help</h2>
   <p>It appears that this generator has no arguments.</p>
   <p>Click below to create a blank one and get started.<p>
 <input type="button" value="Create New Argument" onclick="new_argument()">
 </div>


 <div style="display:none">
   <div id="argument_template">
     <div class="section_save"><span class="section_status">Saving...</span><img src="/images/spin.gif"></div>
     <div class="fields">
       <h2>Section Title</h2>
       <form>
         <input type="hidden" name="sequence">
	 <h3>Argument Properties</h3>
	 <table>
	   <tr><th>Field</th><th>Value</th><th>Description</th></tr>
	   <tr>
	     <td><label>Name:</label></td>
	     <td><input type="text" name="name"></td>
	     <td><span>The display name that is presented to the user</span></td>
	   </tr>
	   <tr>
	     <td><label>Description:</label></td>
	     <td><input type="text" name="description"></td>
	     <td><span>A short description to accompany the form item.</span></td></tr>
	   <tr>
	     <td><label>Variable:</label></td>
	     <td><input type="text" name="variable"></td>
	     <td><span>The key used in the map passed to the generator.</span></td>
	   </tr>
	   <tr>
	     <td><label>Type:</label></td>
	     <td>
	       <select name="type">
	         <option value="INTEGER">Integer</option>
	         <option value="FLOAT">Float</option>
	         <option value="DOUBLE">Double</option>
	         <option value="STRING">String</option>
	         <option value="SELECT">Select</option>
	       </select>
	     </td>
	     <td><span>The display name that is presented to the user</span></td>
	   </tr>
	   <tr>
	     <td><label>Default:</label></td>
	     <td><input type="text" name="default_value"></td>
	     <td><span>A default value for the variable used as an example.</span></td>
	   </tr>
	   <tr>
	     <td><label>Options:</label></td>
	     <td><input type="text" name="options"></td>
	     <td><span>A comma seperated list of values from which to select.</span></td>
	   </tr>
	 </table>	 
	 

	 <div class="argument_select_options">
	   <h3>Select Options</h3>
	   <table>
	   <tr>
	     <th>Name</th>
	     <th>Value</th>
	     <th></th>
	   <tr>
	   <tr>
	     <td><input type="text" style="width:10em"></td>
	     <td><input type="text" style="width:10em"></td>
	     <td><span style="font-weight:800;">&#x2717;</span></td>
	   </tr>
	   </table>
	   
	 </div>

       </form>

       <div class="section_actions">
         <span style="font-weight:800;vertical-align:-10%;font-size:150%;" class="argument_action_new argument_action section_action" title="New Argument">+</span>         
	 &nbsp;
	 &nbsp;
         <span class="argument_action_up argument_action section_action" title="Move Argument Up">&#x2b06</span>
         <span class="argument_action_down argument_action section_action" title="Move Argument Down">&#x2b07</span>
         &nbsp;
	 &nbsp;
         <span style="float:right" class="argument_action_delete argument_action section_action" title="Delete Argument">&#x2717;</span>
	 
       </div>

     </div>
   </div>
 </div>
</div>


<div id="images" class="tab">
 <div id="upload_dnd_target" class="section" style="width:40em;height:10em;">
   <h2>Upload Images</h2>
   <span style="margin-top:2em;text-align:center;width:100%;display:inline-block;font-weight:bold;">Drag and Drop files here to upload.</span>
 </div>
 <div class="section">
   <h2>Stored Images</h2>
 </div>
 
</div>


<div id="test" class="tab">
 <div id="" class="section">
   <h2>Test Defaults</h2>
   <p>This tests submits the default values for each argument to the generator. Upon completion, a link to the results is returned so the output can be inspected.</p>
   
   <p><input type="button" value="Test Generator" onclick="test_generator()"></p>

   <div id="test_results">
     
   </div>   

 </div> 
</div>

<div id="validation" class="tab">
<h2>Parameter Validation Rules</h2>
<input type="button" value="New Rule" onClick="new_rule()">
</div>

<div id="processing" class="tab">
<form>
<input type="button" onclick="new_script()" value="New Script"></input>
</form>
<h2>Pre-processing</h2>
<h2>Post-processing</h2>
</div>

<div id="delete" class="tab">
<div class="section">
<h2>Delete Generator</h2>
<p>If are are sure you want to delete this generator, click the button below. This operation cannot be undone.</p>

<input type="button" value="Delete This Generator" onclick="delete_generator()">
</div>

</div>

<div id="footer">
</div>
</div>
</body>
 </html>
  