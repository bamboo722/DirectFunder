var dsform = false;
$(document).ready(function() {
	$("form").submit(function (e) {
        e.preventDefault();
	});
	$(".input-start").click(function(){
		$("body").scrollTop(0);
		var form  =$(".form");
		form.css("display","block");
		$(".info").css("display","none");
		
	});
	$(".close").click(function(){
		var form  =$(".form");
		form.css("display","none");
		$(".info").css("display","");
	});
	$(".menu .input-apply").click(function(){
		window.location.href = "getapproved.html";
	});
	$("#frmSend .input-apply").click(function(e){
		//alert("aa");
		var form  =$("#frmSend");
		form.validate();
		if(form.valid()) {
			$("body").scrollTop(0);
			$("#dialog div.loading").css("display","block");
			$("#dialog div.msg").css("display","none");
			$("#apply").css("display","none");
			$(".close").css("display","none");
			$("#dialog").css("display","block");
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
				setTimeout(HideDialog, 3000);
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
});
function ShowDialog(modal)
   {
      //$("#overlay").show();
      //$("#dialog").fadeIn(300);
	  $("#dialog div.loading").css("display","block");
	  $("#dialog div.msg").css("display","none");

      /*if (modal)
      {
         $("#overlay").unbind("click");
      }
      else
      {
         $("#overlay").click(function ()
         {
            HideDialog();
         });
      }*/
   }

   function HideDialog()
   {
      //$("#overlay").hide();
      //$("#dialog").fadeOut(300);
	  $("#dialog div.loading").css("display","none");
	  $("#dialog div.msg").css("display","none");
	  $("#apply").css("display","block");
	  $(".form").css("display","none");
	  $(".info").css("display","");
	  $("#apply").css("display","");
	  $("#dialog").css("display","none");
   } 