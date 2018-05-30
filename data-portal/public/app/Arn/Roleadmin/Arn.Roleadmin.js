$(function(){
	Moj.u.lc('Arn.Roleadmin.css',true);
	
	$(document).off("click","#arn-modperm-menu > .icon-locked");
	$(document).on("click","#arn-modperm-menu > .icon-locked",function(){
		var pMod = $(this).closest("li").next();
		var perm = $("#arn-mod-permlist").val();
		var mod = $(this).closest("li").attr('data-role-id');
		Moj.confirm("Deny "+perm+" access to "+Moj.Arn.Roleadmin.usr+" on "+mod,
		function(){
			Moj.api("Roleadmin","userPermission","Write",{'role':Moj.Arn.Roleadmin.usr,'resource':mod,'perm':perm,'atype':'Deny'},
			function(){
				Moj.Arn.Roleadmin.loadUserModPerms.call(pMod.get(0),true);
			},"POST");
		});
	});
	
	$(document).off("click","#arn-modperm-menu > .icon-unlocked");
	$(document).on("click","#arn-modperm-menu > .icon-unlocked",function(){
		var pMod = $(this).closest("li").next();
		var perm = $("#arn-mod-permlist").val();
		var mod = $(this).closest("li").attr('data-role-id');
		Moj.confirm("Allow "+perm+" access to "+Moj.Arn.Roleadmin.usr+" on "+mod,
		function(){
			Moj.api("Roleadmin","userPermission","Write",{'role':Moj.Arn.Roleadmin.usr,'resource':mod,'perm':perm,'atype':'Allow'},
			function(){
				Moj.Arn.Roleadmin.loadUserModPerms.call(pMod.get(0),true);
			},"POST");
		});
	});
		
	Moj.extend("Arn.Roleadmin",{
		usr:null,
		perms:null,
		pr:null,
		lmod:null,
		ltgt:null,
		
		run: function(){
			var f = function(){
				Moj.jp.lv('Arn','Aclroleadmin','role-admin',function(d){
					$(Moj.n.t()).html(d);
					
					// Lookup users handler
					$(document)
						.off("click","#arn-search-users > .listview li")
						.on("click","#arn-search-users > .listview li",function(){
							$("#arn-search-users > .listview li").removeClass("selected");
							if($(this).attr('id')) {
								$(this).addClass("selected");
								Moj.Arn.Roleadmin.cb_lookupUser.call(this);
							}
						});
					
					Moj.g.form("arn-frm-addRole",function(v){
						if(v && v.response){
							$("#arn-search-users-input").val($('input[name="frm_username"]').val()).keyup();
							
							$("#arn-frm-addRole")
								.trigger("reset")
								.find(".btnfiltertoggles").first().click();
						}
					});
					
					if(Moj.u.t("Function",Moj.Arn.Roleadmin.pr)) Moj.Arn.Roleadmin.pr();
				});
				Moj.Arn.Resources.d('User Administration');
			};
			f();
			
			Moj.n.a('AclRoles',f);
			Moj.n.h('AclRoles');
		},
		
		cb_lookupUser: function(){
			Moj.Arn.Roleadmin.usr = $(this).attr('id');
			Moj.jp.lv('Arn','Aclroleadmin','role-admin-view',{u:Moj.Arn.Roleadmin.usr},function(d){
				$("#arn-role-admin-view").html(d);
				
				$(document).on("click","#arn-acl-ugroups > li",function(){
					if($(this).hasClass("selected")) {
						Moj.Arn.Roleadmin.grp_remove.call(this);
					} else {
						Moj.Arn.Roleadmin.grp_add.call(this);
					}
				});
				
				$("#arn-role-admin-view .accountDetails dd[data-role-iedit]").each(function(){
					$(this).data("ieditCB",function(t){
						var dt = $(this).prev("dt");
						var c = dt.attr("data-role-id");
						if(c != undefined){
							Moj.api("Roleadmin","one","Update",{'p':c,'v':t,'u':Moj.Arn.Roleadmin.usr},"POST");
						}
					});
				});
				
				$("#arn-role-admin-view .accountDetails select").change(function(){
					var c = $(this).closest("dd").prev("dt").attr("data-role-id");
					var newVal = $(this).val();
					if(newVal == "--") newVal = '';
					Moj.confirm("Update to '"+newVal+"'?",function(){
						Moj.api("Roleadmin","one","Update",{'p':c,'u':Moj.Arn.Roleadmin.usr},"POST");
					});
				});
				
				$("#arn-role-admin-view button[class='btnValidate']").click(function(){
					Moj.confirm("Enable this account?",function(){
						Moj.api("Roleadmin","one","Update",{'p':'sys_disabled','v':'0','u':Moj.Arn.Roleadmin.usr},function(){
							$("#"+Moj.Arn.Roleadmin.usr).click();
						},"POST");
					});
				});
				
				$("#arn-role-admin-view button[class='btnDelete']").click(function(){
					Moj.confirm("Disable this account?",function(){
						Moj.api("Roleadmin","one","Update",{'p':'sys_disabled','v':1,'u':Moj.Arn.Roleadmin.usr},function(){
							$("#"+Moj.Arn.Roleadmin.usr).click();
						},"POST");
					});
				});
				
				$("#arn-role-admin-view button[class='btnWarn']").click(function(){
					Moj.confirm("Reset password for this account? Password used on next login will be the new password for the account.",function(){
						Moj.api("Roleadmin","one","Update",{'p':'password','v':'0','u':Moj.Arn.Roleadmin.usr},function(){
							Moj.alert("Password reset successful.");
						},"POST");
					});
				});
			});
			
			Moj.jp.lv('Arn','Aclroleadmin','role-admin-access',{u:Moj.Arn.Roleadmin.usr},function(d){
				$("#arn-role-admin-access").html(d);
				
				$("#arn-acl-user-mods > li:nth-child(odd)").click(function(e,i){
					
					$("#arn-acl-user-mods > li:nth-child(even)").each(function(){
						if(!$(this).is(":hidden") && $(e.currentTarget).attr('data-role-id') != $(this).prev().attr('data-role-id')){
							$(this).slideUp(200);
						}
					});
					
					
					if($(this).next().is(":hidden")){
						Moj.Arn.Roleadmin.clearModMenu();
						$(this).next().slideDown(200,Moj.Arn.Roleadmin.loadUserModPerms);
					}
				});
			});
		},
		
		grp_add: function(){
			var grp = $(this);
			
			Moj.confirm("Add this role to the user?",
			function(){
				Moj.api("Roleadmin","one","Write",{'username':Moj.Arn.Roleadmin.usr,'role':grp.text()},
					function(json){
						if(json.response){
							$("#"+Moj.Arn.Roleadmin.usr).click();
						}
				},"POST");
			});
		},
		
		grp_remove: function(){
			var grp = $(this);
			
			Moj.confirm("Remove this role from the user?",
			function(){
				Moj.api("Roleadmin","one","Delete",{'username':Moj.Arn.Roleadmin.usr,'role':grp.text()},function(json){
					if(json.response){
						$("#"+Moj.Arn.Roleadmin.usr).click();
					}
				},"POST");
			});
		},
		
		loadUserModPerms: function(renderMenu){
			var mod = $(this).prev().attr('data-role-id');
			var tgt = $(this);
			
			if(!mod){
				mod = Moj.Arn.Roleadmin.lmod;
				tgt = Moj.Arn.Roleadmin.ltgt;
			} else {
				Moj.Arn.Roleadmin.lmod = mod;
				Moj.Arn.Roleadmin.ltgt = tgt;
			}
			
			if(!renderMenu)
				Moj.Arn.Roleadmin.setModMenu.call(this);
			
			Moj.api("Roleadmin","userPermission","Read",{'role':Moj.Arn.Roleadmin.usr,'resource':mod},
					function(json){
						tgt.empty();
						var r = json.response;
						if(r && r.length < 1){
							tgt.text('No permissions');
						} else {
							var list = $("<ul class=\"arn-mod-perms\">");
							tgt.append(list);
							
							for(var t in r){
								var st="";
								
								if(t.indexOf("(I)") > 0){
									st = " (inherited)";
								} else {
									st = "";
								}
								
								if(r[t] == 1){
									st = "Allowed "+st;
									var ico = "<i class=\"icon-unlocked\"></i>&nbsp;<small>"+st+"</small>";
								} else {
									st = "Denied "+st;
									var ico = "<i class=\"icon-locked\"></i>&nbsp;<small>"+st+"</small>";
								}
								
								t = t.split(" ")[0];
								list.append("<li data-role-mod='"+t+"'>"+t+ico+"</li>");
							}
							
							$(".arn-mod-perms > li").click(function(){
								var li = $(this);
								if(li.text().indexOf("inherited") < 0){
									
									Moj.confirm("Delete this permission?",function(){
										Moj.api("Roleadmin","userPermission","Delete",{'role':Moj.Arn.Roleadmin.usr,'resource':mod,'perm':li.attr('data-role-mod')},
										function(){
											Moj.Arn.Roleadmin.loadUserModPerms.call(li.closest("li"),true);
										},"POST");
									});
									
								} else {
									Moj.alert("Unable to remove inherited permissions.");
								}
							});
						}
					});
		},
		
		setModMenu: function(){
			var mod = $(this).prev();
			mod.append("<div id=\"arn-modperm-menu\"><i class='icon-locked'></i>&nbsp;<i class='icon-unlocked'></i>&nbsp;&nbsp;<select id=\"arn-mod-permlist\"></select></div>");
			
			var plist = $("#arn-mod-permlist");
			plist.empty();
			
			if(Moj.Arn.Roleadmin.perms == null){
				Moj.api("Roleadmin","allPerms","Read",
					function(j){
						if(j && j.response){
							var perms = j.response;
							for(var i=0;i < perms.length;i++){
								var perm = perms[i]['access'];
								if(perm != "Service")
									Moj.Arn.Roleadmin.perms += "<option>"+perm+"</option>";
							}
						}
						
						plist.append(Moj.Arn.Roleadmin.perms);
					},"POST");
			} else {
				plist.append(Moj.Arn.Roleadmin.perms);
			}
		},
		
		clearModMenu: function(){
			$("#arn-acl-user-mods > li:nth-child(odd) > div").remove();
		}
	});
	
	Moj.Arn.Roleadmin.run();
	Moj.u.lslist['Arn.Roleadmin.js'] = Moj.Arn.Roleadmin.run;
});