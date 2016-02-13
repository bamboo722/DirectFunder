var dsform = false;
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
		$("body").scrollTop(0);
		//window.location.href = "getapproved.html";
		var form  =$(".form");
		form.css("display","block");
	});
	$("#frmSend .input-apply").click(function(e){
		//alert("aa");
		var form  =$("#frmSend");
		form.validate();
		if(form.valid()) {
			$("#dialog div.loading").css("display","block");
			$("#apply").css("display","none");
			$(".form .close").css("display","none");
			$("#dialog div.msg").css("display","none");
			$(".form").css("top","250px");
			$.ajax({
			url:'apply.php',
			type: 'POST',
			data: $('#frmSend').serializeArray(),
			success: function(data)
			{
                             //alert(data.responseText);
				$("#dialog div.loading").css("display","none");
				$("#dialog div.msg").css("display","block");
				$("#dialog div.msg").html(data.msg);
				$("input").val("");
				$("textarea").val("");
				if(data.status =="OK"){
					setTimeout("RedirectApp();",3000);
				}
				$(".form .close").css("display","");
				//setTimeout(HideDialog, 3000);
			},
			error:function(data)
			{
                               
				$("#dialog div.loading").css("display","none");
				$("#dialog div.msg").css("display","block");
				$("#dialog div.msg").html("There is an error connecting to server");
				$(".form .close").css("display","");
				setTimeout(HideDialog, 3000);
			}
			});
			ShowDialog(true);
		}
	});
});
function ShowDialog(modal){
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