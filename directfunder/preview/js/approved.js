$(document).ready(function() {
	$("#startup").click(function(){
		$("#startup").removeClass("btn-gray");
		$("#startup").addClass("btn-orange");
		$("#exisiting").removeClass("btn-orange");
		$("#exisiting").addClass("btn-gray");
	});
	$("#exisiting").click(function(){
		$("#exisiting").removeClass("btn-gray");
		$("#exisiting").addClass("btn-orange");
		$("#startup").removeClass("btn-orange");
		$("#startup").addClass("btn-gray");
	});
	$(".input-continue").click(function(){
		/*$("#step1").removeClass("nv-active");
		$(".left-cnt").css("display","none");
		$("#step2").addClass("nv-active");
		$(".right-cnt").css("display","block");
		$("body").scrollTop(0);*/
		var form  =$("#frmSend");
		form.validate();
		if(form.valid()) {
			$("#step1").removeClass("nv-active");
		$(".left-cnt").css("display","none");
		$("#step2").addClass("nv-active2");
		$(".right-cnt").css("display","block");
		$("body").scrollTop(0);
		}
	});
	$("form").submit(function (e) {
        e.preventDefault();
	});
	$(".input-hsubmit").click(function(e){
		var form  =$("#frmSend");
		form.validate();
		if(form.valid()) {
			$("#dialog div.loading").css("display","block");
			$("#dialog div.msg").html("");
			$.ajax({
			url:'send.php',
			type: 'POST',
			data: $('#frmSend').serializeArray(),
			success: function(data)
			{
				if(data.status =="OK"){
					$("#dialog div.loading").css("display","none");
					$("#dialog div.msg").html(data.msg);
					$("input").val("");
					setTimeout(HideDialog, 3000);
				} else {
					$("#dialog div.loading").css("display","none");
					$("#dialog div.msg").html(data.msg);
					setTimeout(HideDialog, 3000);
				}
			},
			error:function(data)
			{
				$("#dialog div.loading").css("display","none");
					$("#dialog div.msg").html("There is an error connecting to server");
					setTimeout(HideDialog, 3000);
			}
			});
			ShowDialog(true);
		}
		e.preventDefault();
		//e.unbind();
	});
	/*$("#fName").val(localStorage.getItem('fName'));
	$("#lName").val(localStorage.getItem('lName'));;
	$("#phone").val(localStorage.getItem('phone'));
	$("#email").val(localStorage.getItem('email'));
	localStorage.clear();*/
	$(".menu .input-apply").click(function(){
		//window.location.href = "getapproved.html";
		$( "#enter" ).hide();
		$("body").scrollTop(0);
		$( "#enter" ).slideToggle( "slow", function() {
		// Animation complete
		setTimeout("$( '#enter' ).hide();",2000);
		});

	});
	$("input:radio").click(function(){
		iptName = $(this).attr("name");
		if($(this).val() =="Yes") {
			$("#" + iptName ).removeAttr("disabled");
			if(iptName == "givePermission") {
				$("input[name$='usrReport']" ).removeAttr("disabled");
				$("input[name$='pswReport']" ).removeAttr("disabled");
			}
		}
		else {
			$("#" + iptName ).attr("disabled","disabled");
			if(iptName == "givePermission") {
				$("input[name$='usrReport']" ).attr("disabled","disabled");
				$("input[name$='pswReport']" ).attr("disabled","disabled");
			}
		}
	});
	$("select").change(function(){
		if( $(this)[0].selectedIndex ==0) {
			$(this).css("color","#A0A0A0");
		} else {
			$(this).css("color","#000000");
		}
	});
	$("select").each(function(element){
		SelectChange($(this));
	});
	$("select").change(function(){
		SelectChange($(this));
	});
});
function SelectChange(element) {
	if( element[0].selectedIndex ==0) {
		element.css("color","#A0A0A0");
	} else {
		element.css("color","#000000");
	}
}

$.validator.addMethod("valueNotEquals", function(value, element, arg){
  return arg != value;
 }, "Please input a valid value.");
 
$( "#frmSend" ).validate({
  rules: {
	urlWebsite: {
      required: "#haveWebsiteYes:checked"
    },
	urlShowPersonalCredit: {
      required: "#dontShowPersonalCreditYes:checked"
    },
	haveIRAHowMuch: {
      required: "#haveIRAYes:checked"
    },
	usrReport: {
      required: "#givePermissionYes:checked"
    },
	pswReport: {
      required: "#givePermissionYes:checked"
    },
	whoFMService: {
      required: "#haveFMServiceYes:checked"
    },
	whoAnyoneElse: {
      required: "#haveAnyoneElseYes:checked"
    },
	Years: { valueNotEquals: "0" },
	haveFMService: { valueNotEquals: "" }
  }
});
function ShowDialog(modal)
   {
      $("#overlay").show();
      $("#dialog").fadeIn(300);

      if (modal)
      {
         $("#overlay").unbind("click");
      }
      else
      {
         $("#overlay").click(function ()
         {
            HideDialog();
         });
      }
   }

   function HideDialog()
   {
      $("#overlay").hide();
      $("#dialog").fadeOut(300);
   } 