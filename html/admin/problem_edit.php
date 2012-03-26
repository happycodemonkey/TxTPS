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
<style>
</style>
<script src="http://www.google.com/jsapi?key=ABQIAAAAFm_alaZoE-oHFncIiaLcIxSvXgVckI6g_NvjnVRrpDD27K_tUxThinip_QAXOlx_WYg32QPVCEBh5A" type="text/javascript"></script>
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

    $("#overview_tab_label").click(function(){
      set_tab("overview");
    });

    $("#arguments_tab_label").click(function(){
      set_tab("arguments");
    });

    $("#validation_tab_label").click(function(){
      set_tab("validation");
    });

    $("#processing_tab_label").click(function(){
      set_tab("processing");
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



    //load arguments
    $(".argument").remove();
    arg_url = "/tacc/api.php?path=/arguments/?generator_id=" + generator_id;
    jQuery.ajax({type: "GET", url: arg_url, success: function(arg_json){

      console.debug(arg_json);
      args = JSON.parse(arg_json);
      console.debug(args);
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
      console.debug(script_json);
      scripts = JSON.parse(script_json);
      console.debug(scripts);
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
      arg_div      = $("<div>");
      arg_div.attr("id","arg_" + arg.id);
      arg_div.addClass("argument");

      //place before the first argument that has a greater sequence
      arguments = $("#arguments").children(".argument");
      if(arguments.length == 0){
        $("#arguments").append(arg_div);
      }else{
        inserted = false;
        arguments.each(function(index){
	
	  sequence = $(this).find('input[name="sequence"]').first().val();
	  if(parseInt(sequence) > parseInt(arg.sequence)){
	    $(this).before(arg_div);
	    inserted = true;
	    return false;
	  }else{
	      //alert(sequence + " is less than " + arg.sequence);
	  }

	});

	if(inserted!= true){
	  $("#arguments").append(arg_div);
	}
      }


      actions_div= $("<div>");
      actions_div.addClass("actions");
      actions_div.css("display","block");
      actions_div.css("float","left");
      actions_div.appendTo(arg_div);


      actions_div.append("<h2>Actions</h2>");      


      // save and delete buttons
      control_box    = $("<div>");
      control_box.addClass("controls")
      control_box.appendTo(actions_div);

      save_button  = $("<input>");
      save_button.css("display", "inline");
      save_button.attr("type","button");
      save_button.attr("value","Save");
      save_button.click(function(){
        save_argument(arg.id);
      });
      control_box.append(save_button);
      control_box.append("<br>");
      delete_button  = $("<input>");
      delete_button.css("display", "inline");
      delete_button.attr("type","button");
      delete_button.attr("value","Delete");
      delete_button.click(function(){
        delete_argument(arg.id);
      });
      control_box.append(delete_button);
      control_box.append("<br>");
      control_box.append("<br>");
      
      new_button  = $("<input>");
      new_button.css("display", "inline");
      new_button.attr("type","button");
      new_button.attr("value","New");
      new_button.click(function(){
        new_argument();
      });
      control_box.append(new_button);
      control_box.append("<br>");
      control_box.append("<br>");
      
      up_button  = $("<input>");
      up_button.css("display", "inline");
      up_button.attr("type","button");
      up_button.attr("value","Up");
      up_button.click(function(){
      
	current_arg  = $(this).parents(".argument");
        previous_arg = current_arg.prev(".argument");
	if(previous_arg.length != 0){
	  previous_arg.before(current_arg);
	}
      });
      control_box.append(up_button);
      control_box.append("<br>");
      down_button  = $("<input>");
      down_button.css("display", "inline");
      down_button.attr("type","button");
      down_button.attr("value","Down");
      down_button.click(function(){
      
	current_arg  = $(this).parents(".argument");
        next_arg = current_arg.next(".argument");
	if(next_arg.length != 0){
	  next_arg.after(current_arg);
	}
      });
      control_box.append(down_button);

      fields_div = $("<div>");
      fields_div.addClass("fields");
      fields_div.attr("id","fields_" + arg.id);
      fields_div.css("display","block");
      fields_div.css("float","left");
      fields_div.append($("<h2>Fields</h2>"));
      fields_div.appendTo(arg_div);

      arg_form     = $("<form>");
      arg_form.attr("id","argform_" + arg.id);
      arg_form.appendTo(fields_div);



      //hidden sequence field
      sequence_input = $("<input>");
      sequence_input.attr("type","hidden");
      sequence_input.attr("name","sequence");
      sequence_input.val(arg.sequence);
      sequence_input.appendTo(arg_form);




      field_table  = $("<table>");
      field_table.prependTo(arg_form);

      //header
      header_row = $("<tr>");
      header_row.appendTo(field_table);
      f_header   = $("<th>");
      f_header.text("Field");
      header_row.append(f_header);
      v_header   = $("<th>");
      v_header.text("Value");
      header_row.append(v_header);
      d_header   = $("<th>");
      d_header.text("Description");
      header_row.append(d_header);

      //arg name
      name_row   = $("<tr>");
      name_row.appendTo(field_table);
      //
      name_label = $("<label>");
      name_label.text("Name:");
      td         = $("<td>");
      td.append(name_label);
      name_row.append(td);
      //
      name_input = $("<input>");
      name_input.attr("type", "text");
      name_input.attr("name", "name");
      name_input.val(arg.name);
      td         = $("<td>");
      td.append(name_input);
      name_row.append(td);
      //
      name_desc  = $("<span>");
      name_desc.text("The display name that is presented to the user.");
      td         = $("<td></td>");
      td.append(name_desc);
      name_row.append(td); 

      //arg description
      desc_row   = $("<tr>");
      desc_row.appendTo(field_table);
      //
      desc_label = $("<label>");
      desc_label.text("Description:");
      td         = $("<td>");
      td.append(desc_label);
      desc_row.append(td);
      //
      desc_input = $("<input>");
      desc_input.attr("type", "text");
      desc_input.attr("name", "description");
      desc_input.val(arg.description);
      td         = $("<td>");
      td.append(desc_input);
      desc_row.append(td);
      //
      desc_desc  = $("<span>");
      desc_desc.text("A short description to accompany the form item");
      td         = $("<td></td>");
      td.append(desc_desc);
      desc_row.append(td);

      //arg variable
      var_row   = $("<tr>");
      var_row.appendTo(field_table);
      //
      var_label = $("<label>");
      var_label.text("Variable:");
      td         = $("<td>");
      td.append(var_label);
      var_row.append(td);
      //
      var_input = $("<input>");
      var_input.attr("type", "text");
      var_input.attr("name", "variable");
      var_input.val(arg.variable);
      td         = $("<td>");
      td.append(var_input);
      var_row.append(td);
      //
      var_desc  = $("<span>");
      var_desc.text("The key used in the map passed to the generator.");
      td         = $("<td></td>");
      td.append(var_desc);
      var_row.append(td);

      //arg type
      type_row   = $("<tr>");
      type_row.appendTo(field_table);
      //
      type_label = $("<label>");
      type_label.text("Type:");
      td         = $("<td>");
      td.append(type_label);
      type_row.append(td);
      //
      type_select = $("<select>");
      type_select.attr("name", "type");
      
      data_types = ["INTEGER","FLOAT","DOUBLE","STRING","SELECT"];
      for (i in data_types){
        value = data_types[i].toUpperCase();
	text  = data_types[i].toLowerCase();
	text  = ("" + text.charAt(0)).toUpperCase() + text.substr(1); 
        console.debug(value + " " + text);
	type_option = $("<option>");
	type_option.val(value);
	type_option.text(text);
	if(value == arg.type){
	  type_option.attr("selected", "selected");
	}
	type_select.append(type_option);
      }


      td         = $("<td>");
      td.append(type_select);
      type_row.append(td);
      //
      type_desc  = $("<span>");
      type_desc.text("The data type of the argument");
      td         = $("<td></td>");
      td.append(type_desc);
      type_row.append(td);

      //arg default
      def_row   = $("<tr>");
      def_row.appendTo(field_table);
      //
      def_label = $("<label>");
      def_label.text("Default:");
      td         = $("<td>");
      td.append(def_label);
      def_row.append(td);
      //
      def_input = $("<input>");
      def_input.attr("type", "text");
      def_input.attr("name", "default_value");
      def_input.val(arg.default_value);
      td         = $("<td>");
      td.append(def_input);
      def_row.append(td);
      //
      def_desc  = $("<span>");
      def_desc.text("The an example value used as a default");
      td         = $("<td></td>");
      td.append(def_desc);
      def_row.append(td);

      // select options
      opt_row   = $("<tr>");
      opt_row.appendTo(field_table);
      //
      opt_label = $("<label>");
      opt_label.text("Options");
      td        = $("<td>");
      td.append(opt_label);
      opt_row.append(td);
      //
      opt_input = $("<input>");
      opt_input.attr("type","text");
      opt_input.attr("name","options");
      opt_input.val(arg.options);
     
      /* 
      opt_table = $("<table>");
      opt_table.css("margin","0");
      opt_table.css("padding","0");
      //opt_table.append("<tr><th>Value</th></tr>");
      option_parts = (arg.options + "").split(",");
      for (i in option_parts){
        value = option_parts[i];
	text  = value;
	tr    = $("<tr>");
	tr.css("margin","0");
	tr.css("padding","0");
	vtd   = $("<td>");
	vtd.append("<input>");
	vtd.appendTo(tr);
	//ttd   = $("<td>");
	//ttd.append("<input>");
	//ttd.appendTo(tr)
	tr.appendTo(opt_table);
	
      }
      */

      
      td        = $("<td>");
      td.append(opt_input);
      opt_row.append(td);
      opt_desc  = $("<span>");
      opt_desc.text("Options from which the user can select");
      td        = $("<td>");
      td.append(opt_desc);
      opt_row.append(td);


      //hide options row if type is not select
      type_select.change(function(){
        id   = arg.id;
        form = $("#argform_" + id);
	input  = form.find("input[name=options]").first();
	row     = input.parent().parent();
	
	select_value = $(this).val();
	if($(this).val() == "SELECT"){
	  console.debug("Show Row");
          row.show();
        }else{
	  console.debug("Hide Row");
	  row.hide();
	}
      });
      type_select.change();


      fields_div.append("<br><br><br>");



      //add modification listeners
      $("#" + fields_div.attr("id") + " input[type!=button],select").change(function(){
        changed = true;
	make_example(arg.id);
      });



      //example box
      example_div = $("<div>");
      example_div.attr("id","ex_" + arg.id);
      example_div.addClass("example")
      example_div.css("display","block");
      example_div.css("float","left");
      example_div.append($("<h2>Example</h2>"));
      example_div.appendTo(arg_div);
      make_example(arg.id);


  }


  function save_argument(id){

    arg_url    = "/tacc/api.php?path=/arguments/" + id;
    form   = $("#argform_" + id);
    fields = form.serializeArray();
    arg    = new Object;
    
    $.each(fields,function(key,value){
      arg[value.name] = value.value;
    });

    //split up options into an array
    arg['options'] = arg['options'].split(',');

    arg_json = JSON.stringify(arg);
    alert(arg_json);

    
    jQuery.ajax({type: "PUT", url:arg_url,data:arg_json,success: function(data){
      alert("Saved:" + data);
    },error:function(){
      alert("Could not save");
    }});

  }


  function delete_argument(id){
    
    do_delete = confirm("Are you sure you want to delete this argument?");
    if(do_delete){
      arg_url    = "/tacc/api.php?path=/arguments/" + id;
      jQuery.ajax({type: "DELETE", url:arg_url,success: function(){
        $("#arg_" + id).hide(); 
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
      console.debug(argument.options);
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



  google.load("jquery", "1");
  google.load("jqueryui", "1");
  google.setOnLoadCallback(start); 
</script>
<link  rel="stylesheet" type="text/css" href="../css/main.css" />
<style type="text/css">
th,td{
	padding:.125em .5em .125em .5em;
}
.panelHeader{
	font-weight:bold;
	font-size:medium;
	cursor:pointer;
}
.mdHover{
	color:#666;
}
.mdSelected{
	color:#00CCFF;
}

.example{
  padding-left:1em;
}

.tab{
  display:block;
  float:left;

}

.actions{
 padding-right:3em;

}

.fields{
  padding-left:1em; 
  display:block;
  border-right:1px dashed grey;
  border-left:1px dashed grey;
}

.controls input{
  width:5em;
}

.argument{
  display:block;
  clear:both;
  border-bottom: 1px solid grey;
}

.tab_button{
  font-size:1.5em;
  display:block;
  width:10em;
  height:2em;
  margin-bottom:0.5em;
   margin-left:-2em;
   margin-right:-2em;
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
<body>
<div id="wrapper">
  <div id="titlebar">
  <?php include("../includes/templating/title.inc.php"); ?>
</div>
<div id="menu">
  <?php include("../includes/templating/adminmenu.inc.php"); ?>
</div>

<div id="content">
  <div style="border-right-style:solid;border-right-width:1px;padding:2em;display:block;float:left;margin-right:2em;">
    <span id="overview_tab_label"   class="tab_button selected"> Overview </span>
    <span id="arguments_tab_label"  class="tab_button"> Arguments </span>
    <span id="validation_tab_label" class="tab_button"> Validation </span>
    <span id="processing_tab_label" class="tab_button"> Processing </span>
  </div>

  <div id="overview" class="tab">
  <h1><span id="overview_title"><span class="collection_name"></span><span class="generator_name"></span>Generator: Overview</span></h1>

<form id="generator_form">
<table>
<tr><td><label for="generator_name">Name:</label></td><td><input type="text" name="name" value="" id="generator_name"></td></tr>
<tr><td><label for="generator_collection">Collection:</label></td><td><select name="collection_id" type="select" value="" id="generator_collection"></td></tr>
<tr><td><label for="generator_script">Script:</label></td><td><input type="text" size="50" name="script" value="" id="generator_script"></td></tr>
<tr><td><label for="generator_description">Description:</label></td><td><textarea cols="80" rows="6" id="generator_description" name="description"></textarea></td></tr>
</table>

<br><br>
<input type="button" value="Save Overview" onclick="save_overview()">
</form>
</div>



<div id="arguments" class="tab">
 <h1><span id="overview_title"><span class="collection_name"></span><span class="generator_name"></span>Generator: Arguments</span></h1>
</div>

<div id="validation" class="tab">
<h1><span id="overview_title"><span class="collection_name"></span><span class="generator_name"></span>Generator: Validation</span></h1>
<h2>Parameter Validation Rules</h2>
<input type="button" value="New Rule" onClick="new_rule()">
</div>

<div id="processing" class="tab">
<h1><span id="overview_title"><span class="collection_name"></span><span class="generator_name"></span>Generator: Processing</span></h1>
<form>
<input type="button" onclick="new_script()" value="New Script"></input>
</form>
<h2>Pre-processing</h2>
<h2>Post-processing</h2>
</div>

</div>

<div id="footer">
</div>
</div>
</body>
 </html>
  