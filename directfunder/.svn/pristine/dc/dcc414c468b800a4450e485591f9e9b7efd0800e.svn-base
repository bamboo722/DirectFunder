jQuery.extend(jQuery.validator.messages, {
    required: "Vui lòng nhập thông tin",
    email: "Email không hợp lệ",
    url: "Địa chỉ url không hợp lê",
    number: "Giá trị số không hợp lê",
});
$(document).ready(function() {
	/*$("#startup").click(function(){
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
	});*/
	$(".input-continue").click(function(){
		var form  =$("#frmSend");
		form.validate();
		if(form.valid()) {
			/*$("#step1").removeClass("nv-active");
			$("#persionalInfo").css("display","none");
			$("#step2").addClass("nv-active");
			$(".business").css("display","block");*/
			setTimeout("Step2();",1000);
			//$("body").scrollTop(0);
		}
		else {
			$("bar-nav").css("display","none");
		}
	});
	
	$("input").focus(function(){
		if($(this).attr("type") != "radio") {
			$(".bar-nav").css("display","none");
		}
	});
	$("input").blur(function(){
		$(".bar-nav").css("display","");
		if($(this).attr("type") != "radio") {
		var pos = $("body").scrollTop();
		$("body").scrollTop(pos-20);
		}
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
	$("form").submit(function (e) {
        e.preventDefault();
	});
	$(".input-submit").click(function(e){
		var form  =$("#frmSend");
		form.validate();
		if(form.valid()) {
			$("#dialog div.loading").css("display","block");
			$("#dialog div.msg").html("");
			$.ajax({
			url:'../../send.php',
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
});
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
    }
  }
});
function ShowDialog(modal) {
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

function HideDialog() {
  $("#overlay").hide();
  $("#dialog").fadeOut(300);
} 
function Step2() {
	$("#step1").removeClass("nv-active");
	$("#persionalInfo").css("display","none");
	$("#step2").addClass("nv-active");
	$(".business").css("display","block");
	$("body").scrollTop(0);
} 