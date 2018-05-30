$(function(){
	
	var btn = Moj.ui.btnC;
	/***********************************************************************************
	 * CALLBACKS
	 */ 
	Moj.u
	
	// Alert. Defines how Moj alerts are handled.
	.scb('alert',function(d,t){
		$(".Hilo-error-bar").remove();
		$('body').append('<div class="message-dialog Hilo-error-bar bg-color-red">'+d+'</div>');
		$("body > .Hilo-error-bar").append(new btn);
	})
	
	.u.scb('confirm',function(m,t,f){
		$(".message-dialog").remove();
		var d = $("<div class='message-dialog bg-color-blue'>").html(m);
		$('body').append(d);
		d.append(new btn(f));
		d.append(new Moj.ui.btnV(t));
	})
	
	// UI Dialog. Defines how Moj dialogs are handled.
	.u.scb('ui_d',function(h,o){
		$(".message-dialog").remove();
		var d = $("<div>").append(h,new btn).addClass("message-dialog bg-color-green");
		$("body").append(d);
	});
	/***********************************************************************************/
	
	Moj
	.u.lc('modern.css',Moj.o.b+'libs/Metro-UI/css/')
	.u.lc('modern-responsive.css',Moj.o.b+'libs/Metro-UI/css/')
	.u.lc("Hilo.css",true);
	
	Moj.u.lc("jquery-ui-1.10.3.custom.min.css",baseUrl()+"libs/jQuery/UI/smoothness/");
	Moj.u.ls("jquery-ui-1.10.3.custom.min.js",baseUrl()+"libs/jQuery/UI/");
	
	/***********************************************************************************
	 * NAVIGATION
	 */
	Moj
	.n.t("#Hilo-view-content")
	.n.s({
		"main":function(){
			// Resources (tiles, menu and nav)
			Moj.u.ls("Arn.Resources.js",true,null,function(){
				Moj.Arn.Resources.load(null,function(){					
					$(document).off("click","#Roleadmin");
					$(document).on("click","#Roleadmin",function(){Moj.u.ls("Arn.Roleadmin.js",true);});
					
					$(document).off("click","#Groupadmin");
					$(document).on("click","#Groupadmin",function(){Moj.u.ls("Arn.Groupadmin.js",true);});
					
					$(document).off("click","#Moduleadmin");
					$(document).on("click","#Moduleadmin",function(){Moj.u.ls("Arn.Moduleadmin.js",true);});
					$(document).off("click","#Triplepoints");
$(document).on("click","#Triplepoints",function(){Moj.u.ls("Hilo.Triplepoints.js",true);});
$(document).off("click","#Customers");
$(document).on("click","#Customers",function(){Moj.u.ls("Hilo.Customers.js",true);});

$(document).off("click","#Valentinesi");
$(document).on("click","#Valentinesi",function(){Moj.u.ls("Hilo.Valentinesi.js",true);});
$(document).off("click","#Jobapps");
$(document).on("click","#Jobapps",function(){Moj.u.ls("Hilo.Jobapps.js",true);});
$(document).off("click","#Ssdonate");
$(document).on("click","#Ssdonate",function(){Moj.u.ls("Hilo.Ssdonate.js",true);});
$(document).off("click","#Jobapp");
$(document).on("click","#Jobapp",function(){Moj.u.ls("Hilo.Jobapp.js",true);});
$(document).off("click","#Mothersdayi");
$(document).on("click","#Mothersdayi",function(){Moj.u.ls("Hilo.Mothersdayi.js",true);});
//##INSERT_POINT








				});
				Moj.Arn.Resources.dropdown();
			});
			
			$("body").fadeIn();
		}
	});
	/***********************************************************************************/
	
	/***********************************************************************************
	 * BUILD APP
	 */ 
	Moj.n.go(null,{"Arn.Auth.js":function(){
		
		if(_.u.ac('Hilo_AUTH')){
			$('html').css("background","url("+baseUrl()+Moj.o.appopt.imagePath+"bg3.png) repeat center center fixed");
			
			var redirect = _.u.ac("MOJ-R");
			
			if(redirect && Moj.n.has(redirect)){
				Moj.n.go(redirect);
			} else {
				Moj.n.go("main");
			}
			
			// Set app name
			$(".arn-header-bar").prepend("<h1 class=\"Hilo-app-name\">"+Moj.o.appopt.appname+"</h1>");
			
			// Load username and menu
			var disp = {
					"ACL Roles":"User Administration",
					"ACL Groups":"Groups Administration",
					"ACL Modules":"Modules Setup"
			};
			
			// Load username and menu
			Moj.Arn.Auth.usr(function(){
				Moj.c.ja(baseUrl()+"Jax/Resources/view/access/Render",function(r){
					if(r && r.response){
						var m = r.response.modules;
						if(m['Acladmin'] || (m['Controlpanel'] && m['Controlpanel']['children']['Acladmin'])){

							if(m['Acladmin']){
								var acladmin = m['Acladmin'];
							} else {
								var acladmin = m['Controlpanel']['children']['Acladmin'];
							}
							
							if(acladmin['children']){
								for(var m in acladmin['children']){
									var md = $("<li><a id=\""+m+"\">"+disp[acladmin['children'][m]['config']['display_name']]+"</a></li>");
									$("#arn-auth-menu .dropdown-menu").prepend(md);
								}
							}
						}
					}
					$("#arn-auth-menu").Dropdown();
				});
				
				$("a[data-role-id='arn-lnk-logout']").attr("href",$("a[data-role-id='arn-lnk-logout']").attr("href")+"/delay/0");
			},"Arn");
			
						
		} else {
			Moj.Arn.Auth.lf();
		}
		
	}});
	/***********************************************************************************/
});