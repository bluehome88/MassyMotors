$(function(){
	Moj.u.lc("Hilo.Triplepoints.css",true);
			
	Moj.extend("Hilo.Triplepoints",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Triplepoints','index',function(d){
					$(Moj.n.t()).html(d);
					
					$("#tcplist > li").click(function(){
						$("#tcplist > li").removeClass("selected");
						$(this).addClass("selected");
						
						Moj.Hilo.Triplepoints.tcp_options(this);
					});
				});
				
				Moj.Arn.Resources.d('Triple Points');
			};
			f();
			
			Moj.n.a('Triplepoints',f);
			Moj.n.h('Triplepoints');
		},
		
		tcp_options:function(o){
			var id = $(o).attr("data-id");
			Moj.jp.lv('Hilo','Triplepoints','options',{'p':id},function(d){
				$("#poptsdiv").html(d);
				
				$("li[data-option='extract-emails']").click(function(){
					Moj.Hilo.Triplepoints.extract_emails(this);
				});
				
				$("li[data-option='full-extract']").click(function(){
					Moj.Hilo.Triplepoints.full_extract(this);
				});
				
				$("li[data-option='weekly-email']").click(function(){
					Moj.Hilo.Triplepoints.weekly_emails(this);
				});
				
				$("li[data-option='weekly-email-test']").click(function(){
					Moj.Hilo.Triplepoints.weekly_emails_test(this);
				});
			});
		},
		
		full_extract:function(o){
			Moj.confirm("Download full email extract?",function(){
				window.open(baseUrl()+"Jax/resources/Triplepoints/access/Update/get/pExtract?all=true","_blank");
			});
		},
		
		extract_emails:function(o){
			Moj.confirm("NOTE: This will extract all signup emails since the last extract was processed. Please save the file when prompted.<br/>" +
					"This process is not reversible. Continue?",function(){
				window.open(baseUrl()+"Jax/resources/Triplepoints/access/Update/get/pExtract","_blank");
			});
		},
		
		weekly_emails: function(){
			Moj.confirm("Send weekly update email?",function(){
				Moj.alert("Sending emails. Please wait...");
				Moj.api("Triplepoints","weeklyEmail","Update",function(d){
					Moj.alert(d.response+" Emails sent!");
				});
			});
		},
		
		weekly_emails_test: function(){
			Moj.confirm("This sends a test weekly email to the following Accounts<br/>" +
					"(42000999892,42000999893,42000498020,42000101966,42000363667,42000104678). Continue?",function(){
				Moj.alert("Sending emails. Please wait...");
				Moj.api("Triplepoints","weeklyEmailTest","Update",function(d){
					Moj.alert(d.response+" Emails sent!");
				});
			});
		}
	});
			
	Moj.Hilo.Triplepoints.run();
	Moj.u.lslist['Hilo.Triplepoints.js'] = Moj.Hilo.Triplepoints.run;
			
});
