$(document).ready(function(){
	Moj.u.lc("Arn.Auth.css",true);
	Moj.extend("Arn.Auth",{
		lf: function(){
			Moj.jp.lv(Moj.ns,'Auth','index',function(h){
				$('body').empty().append(h).show();
				
				Moj.g.form(null,function(){	// Set form to submit via ajax, pass a callback
					
					if($("input[name='AUTH_MOODLE']").length > 0){
						$.post($("input[name='AUTH_MOODLE']").val(),
								{
									'username':$("input[name='Jax-Auth-Username']").val(),
									'password':$("input[name='Jax-Auth-Password']").val(),
									'mdb':$("select[name='Jax-Auth-Domain']").val()
								},function(){
									Moj.Arn.Auth.redirect(_.o.b);
						},'html')
						.fail(function(){
							//Moj.Arn.Auth.redirect(_.o.b);
						});
					} else {
						$("#Auth").fadeOut(function(){
							Moj.Arn.Auth.redirect(_.o.b);
						});
					}
				});
			});
		},
		
		redirect: function(url){
			$("#Auth").fadeOut(function(){
				location.href=url;
			});
		},
		
		usr: function(cb,n){
			if(!n) n=Moj.ns;
			Moj.jp.lv(n,'Auth','user',function(d){
				$(".arn-auth-user").remove();
				$(".arn-header-bar").prepend(d);
				if(Moj.u.t("Function",cb)) cb.call(cb.prototype,d);
			});
		}
	});
});
