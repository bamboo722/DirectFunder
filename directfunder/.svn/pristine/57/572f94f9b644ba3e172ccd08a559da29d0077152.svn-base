var dsform = false;
jQuery.extend(jQuery.validator.messages, {
    required: "Vui lòng nhập thông tin",
    email: "Email không hợp lệ",
    url: "Địa chỉ url không hợp lê",
    number: "Giá trị số không hợp lê",
});
$(document).ready(function() {
	$("form").submit(function (e) {
        e.preventDefault();
	});
	$(".input-start").click(function(){
		$("body").scrollTop(0);
		var form  =$(".form");
		form.css("display","block");
		/*form.validate();
		if(form.valid()) {
			localStorage.setItem('fName', $("#fName").val());
			localStorage.setItem('lName', $("#lName").val());
			localStorage.setItem('phone', $("#phone").val());
			localStorage.setItem('email', $("#email").val());
		}
		window.location.href = "getapproved.html";*/
	});
	$(".close").click(function(){
		var form  =$(".form");
		form.css("display","none");
		HideDialog();
	});
	$(".menu .input-apply").click(function(){
		//window.location.href = "getapproved.html";
		$("body").scrollTop(0);
		var form  =$(".form");
		form.css("display","block");
	});
	$("#frmSend .input-apply").click(function(e){
		//alert("aa");
		var form  =$("#frmSend");
		form.validate({
			messages: {
			}
			});
		if(form.valid()) {
			$("#dialog div.loading").css("display","block");
			$("#apply").css("display","none");
			$(".form .close").css("display","none");
			$("#dialog div.msg").css("display","none");
			$(".form").css("top","250px");
			$.ajax({
			url:'../apply.php',
			type: 'POST',
			data: $('#frmSend').serializeArray(),
			success: function(data)
			{
				$("#dialog div.loading").css("display","none");
				$("#dialog div.msg").css("display","block");
				$("#dialog div.msg").html(data.msg);
				$("input").val("");
				$("textarea").val("");
				if(data.status =="OK"){
					setTimeout("RedirectApp();",3000);
				}
				$(".form .close").css("display","");
			},
			error:function(data)
			{
				$("#dialog div.loading").css("display","none");
				$("#dialog div.msg").css("display","block");
				$("#dialog div.msg").html("There is an error connecting to server");
				setTimeout(HideDialog, 3000);
			}
			});
			ShowDialog(true);
		}
	});
});
function ShowDialog(modal) {
  $("#dialog div.loading").css("display","block");
}

function HideDialog() {
  $("#dialog div.loading").css("display","none");
  $("#dialog div.msg").css("display","none");
  $("#apply").css("display","block");
  $(".form").css("display","none");
  $(".form").css("top","140px");
}
function RedirectApp() {
	window.location.href = "getapproved.html";
}