/***


    User Interface


***/

function tps_ui_login(){
  $.modal.close();
  
  //get rid of any previous errors
  $("#login_dialog").find(".error").removeClass("error");
  setTimeout("$('#login_dialog').modal({overlayClose:true});", 100);
  
}

function tps_ui_register(){
  $.modal.close();
   setTimeout("$('#register_dialog').modal({overlayClose:true});", 100);
}


function tps_ui_customize(){
  if(user != null){
    if(user.admin){
      var adminbox = $("#adminbox").clone();
      adminbox.find(".user_firstname").text(user.firstname);
      adminbox.find(".user_lastname").text(user.lastname);
      adminbox.find(".user_email").text(user.email);
      $("#authbox").empty();
      $("#authbox").append(adminbox);
    }else{
      var profilebox = $("#profilebox").clone();
      profilebox.find(".user_firstname").text(user.firstname);
      profilebox.find(".user_lastname").text(user.lastname);
      profilebox.find(".user_email").text(user.email);
      $("#authbox").empty();
      $("#authbox").append(profilebox);
    }

  }else{
    $("#authbox").empty();
    $("#authbox").append($("#loginbox").clone());
  }
}


function tps_ui_headermenus(){
  var menu_names  = $(".header_menu_name");
  var menus       = $(".header_menu");
  menu_names.mouseover(function(){
    $(".header_menu_body").hide();
    $(this).siblings(".header_menu_body").show();
    $(this).addClass("header_menu_name_selected");
  });
  menus.mouseleave(function(){
    $(this).find(".header_menu_body").hide();
    $(".header_menu_name_selected").removeClass("header_menu_name_selected");
  });
}

/***


    Authentication and Authorization


***/
function tps_auth_login(){
  var user_email    = $("#login_dialog").find("input[name='email']").val();
  var user_password = $("#login_dialog").find("input[name='password']").val();

  var request = {email: user_email, password: user_password};

  var login_uri = "tacc/login.php";
  $.ajax({type:"POST", url: login_uri, data: request,
  success:function(data,code){
    $("#login_dialog").find(".error").removeClass("error");
    var user_object  = JSON.parse(data);
	console.log(data);
    user = user_object;
    $.modal.close(); //close login dialog
    tps_ui_customize();
  },
  error: function(data,code){
    $("#login_dialog").find("input[name='email']").addClass("error");
    $("#login_dialog").find("input[name='password']").addClass("error");
  }});
}

function tps_auth_logout(){
  var logout_uri = "tacc/logout.php";
  $.post(logout_uri);
  user = null;
  tps_ui_customize();
}

function tps_auth_register() {
	var email = $("#register_dialog").find("input[name='email']").val();
	var password = $("#register_dialog").find("input[name='password']").val();
	var confirm_password = $("#register_dialog").find("input[name='confirm_password']").val();
	
	if (confirm_password == password) {
		var reg_uri = "tacc/register.php";
		var request = {email : email , password : password, classId : '1'};
		$.ajax({type:"POST", url: reg_uri, data: request,	
			success:function(data, code) {
				$.modal.close();
				//@TODO: send email w/ auth code ... see todo in includes/db/users.inc.php
				//@TODO: need visual feedback when this is successful
			},
			error: function(data, code) {
				//@TODO: visual feedback when failure happens
			}
		});
	} else {
		//@TODO: warn user passwords don't match
	}

	//@TODO: Security catches an exception but doesn't do anything about it
}

/***

    Data Functions


***/



function tps_data(resource, query){
  var uri = "/tacc/api.php?path="+resource+"/?" + query;
  var resource;
  $.ajax({url:uri, async: false, success: function(data){
    resource = JSON.parse(data);
  }});
    
  while(resource == null){} //block 
  return resource;
}

/***

    Build Problems


***/

  //creates the "build" form
 
  function tps_build(generator_id, default_values, note){

    
      tps_build_createform(generator_id, default_values, note);


    //check if logged in
    if(user != null){
      //display form
      $("#build_sheet").modal();
    }else{
      tps_ui_login();
    }
  }


  //creates the table of arguments for the build form
function tps_build_createform(generator_id, default_values, notes){
      
    
    //get data  
    var generator  = tps_data("generators",generator_id);
    var collection = tps_data("collections", generator.collection_id);
    var args       = tps_data("arguments","?generator_id=" + generator_id);

    //for use by tps_build_submit
    tps_build_createform.generator = generator;
    tps_build_createform.args      = args;


    //options
    if(default_values == null){
	default_values = {};
    }
    if(notes == null){
	notes = "";
    }

    //fill in data
    var build_sheet = $("#build_sheet");
    build_sheet.find(".generator_name").text(generator.name);
    build_sheet.find(".collection_name").text(collection.name);
    

    //form div
    var build_form = $("#build_arguments");
    build_form.empty();

    //argument table
    var arg_table  = $("<table id='build_arguments_table'>");
    arg_table.appendTo(build_form);
    
    //headers

    for(i in args){
      var a = args[i];
      var argument_tr = $("<tr>");
      argument_tr.appendTo(arg_table);
      var argument_td = $("<td>");
      argument_td.appendTo(argument_tr);
       
      var argument_container = $("<div>");
      var argument_top       = $("<div>");
      var argument_bottom    = $("<div>");
      argument_top.appendTo(argument_container);
      argument_bottom.appendTo(argument_container);
      argument_container.appendTo(argument_td);
      argument_container.css("padding-bottom", "10px");
      var argument_name_c    = $("<div style='display:inline-block;width:350px'>")
      var argument_name      = $("<span style='width:150px;padding-right:10px;font-size:12pt;font-weight:200;color:#fff;'>"+a.name +"</td></tr>");
      argument_name.appendTo(argument_name_c);
      argument_name_c.appendTo(argument_top);
     
  

      //create input element
      var arg_input;


      switch(a.type){

        case "SELECT":
	  arg_input = $("<select>");
	  var o;
	  for (o in a.options){
	    
	    var option = $("<option>");
	    option.attr('value',a.options[o]);
	    option.text(a.options[o]);
	    option.appendTo(arg_input);
	  }
	  break;
	

        case "INTEGER":
	case "FLOAT":
	case "DOUBLE":
	case "STRING":
	default:
	  arg_input = $("<input type='text'>");
	  arg_input.attr('vtype',a.type);
	  if(default_values[a.variable] != undefined){
	      arg_input.attr('value', default_values[a.variable]); //override
	  }else{
	      arg_input.attr('value', a.default_value);
	  }
	  break;
      }

      arg_input.attr('name',a.variable);
      arg_input.appendTo(argument_top);
      arg_input.css("font-size","12pt");

      var argument_description = $("<span style='font-size:10pt;font-style:italic;color:#fff;'>"+a.description+"</span>");
      argument_description.appendTo(argument_bottom);



    }    


    //set the initial notes if needed
    


    tps_build_table_prevpage();
  }


  function tps_build_form2json(){

    var form_array = $("#build_form").serializeArray();
    var arg_map    = new Object();
    var json_obj   = new Object();
    var json_str   = "";
    var i          = 0;
    var args       = tps_build_createform.args;



    //create a mapping of variable=>argument
    for(i in args){
      arg_map[args[i].variable] = args[i];
    }

   
    //build object from form elements 
    for(i in form_array){
      var key   = form_array[i].name;
      var type  = arg_map[key].type;
      var value = form_array[i].value;
      
      switch(type){

        case "INTEGER":
	  value = parseInt(value,10); // this should be a base 10 integer
	  break;
	case "FLOAT":
	case "DOUBLE":
	  value = parseFloat(value); // this should be a floating point number
	  break;

	default:
	  value = value; //leave unchanged, this is fine as a string
      }
      
      json_obj[key] = value;
    }


    //serialize json
    json_str = JSON.stringify(json_obj);

    return json_str;
  }


  function tps_build_validate(){
    return true;
  }


  function tps_build_step(number){
    $(".build_sheet_step_label").css("font-weight","400").css("font-size","13px");
    $("#build_sheet_step_label_" + number).css("font-weight","800").css("font-size","14px");;
    $(".build_sheet_step").hide();
    $("#build_sheet_step_" + number).show();
  }



  function tps_build_table_prevpage(){return tps_build_table_page('prev');}


  function tps_build_table_nextpage(){return tps_build_table_page('next');}


  function tps_build_table_page(direction){
     var rows_per_page = 4;
     var table = $("#build_arguments_table tbody");
     var rows  = table.children("tr");
     
     var min_page = 0;
     var max_page = Math.ceil((rows.size()/(1.0 * rows_per_page))) -1 ; 



    if(this.page == undefined){
      this.page = 0;  // first page
    }

    if(direction == "prev"){
      this.page = Math.max(this.page -1, min_page);
    }else if(direction == "next"){
      this.page = Math.min(this.page + 1, max_page);
    }


    //set label
    $("#build_page_label").text("(Page "+(this.page+1)+" of "+(max_page+1)+")");


    //enable or disable buttons
    var next_button = $("#tps_build_table_next_button");
    var prev_button = $("#tps_build_table_previous_button");
    if(this.page == max_page){
	next_button.hide();
    }else{
        next_button.show();
    }  
    if(this.page == min_page){
	prev_button.hide();
    }else{
        prev_button.show();
    }  

    //hide all rows
    rows.hide();

    //show selected rows
    var index = rows_per_page *this.page;
    for(;index < (rows_per_page * (this.page + 1)); index++){
    table.children("tr:eq(" +index+")").show();
    }
  }



  function tps_build_submit(){

    var generator_id = tps_build_createform.generator.id;

    //validate
    if(!tps_build_validate()){
      return; //form did not validate
    }

    //prepare JSON to submit
    var formjson = tps_build_form2json();
    var submission_object = new Object();
    submission_object.generator_id = generator_id;
    submission_object.arguments    = JSON.parse(formjson);
    var submission_json = JSON.stringify(submission_object);

    //submit
    var problem_url = "/api/products/";
    $.ajax({url:problem_url, data:submission_json, type: "POST", success : function(data){
      var resulting_problem = JSON.parse(data);

      //set problem identifier
      $("#build_sheet_step_4").find(".problem_identifier").text(resulting_problem.identifier);


      tps_build_step(4);
      //send to new problem page
      //var new_url = "/problems/" + resulting_problem.identifier;
      //window.location = new_url;
      tps_build_status(resulting_problem.id);

    }, error: function(){
      
    }});

  }

  //status autoupdate function
  function tps_build_status(problem_id){

      //set updating spinner
      $("#build_sheet_step_4").find(".problem_updating").css("visibility","visible");
     
      //get status
      var problem = tps_data("problems",problem_id);
      var status  = problem.status.toUpperCase();
      //update page
      $("#build_sheet_step_4").find(".problem_status").text(status);
      
      //hide updating spinner
      setTimeout('$("#build_sheet_step_4").find(".problem_updating").css("visibility","hidden")',1000);

      //forward if error or built
      if(status == "ERROR" || status == "BUILT"){
	  var new_url = "/tacc/api.php?path=problems/?identifier=" + problem.identifier;
	  window.location = new_url;
      }


      var delay = 5*1000;//5 seconds
      setTimeout("tps_build_status("+problem_id+")",delay);
  }
