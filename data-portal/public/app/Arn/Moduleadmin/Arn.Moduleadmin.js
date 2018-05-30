$(function(){
	Moj.u.lc('Arn.Moduleadmin.css',true);
	
	$(document).off("click","#arn-modules-mgt > li");
	$(document).on("click","#arn-modules-mgt > li",function(){
		$("#arn-modules-mgt > li").removeClass("selected");
		$(this).addClass("selected");
		
		Moj.Arn.Moduleadmin.m = $(this).text();
		
		$("#arn-moduleadmin-access").empty();
		
		Moj.jp.lv("Arn","Aclmoduleadmin","module-details",{m:Moj.Arn.Moduleadmin.m},function(d){
			$("#arn-moduleadmin-details").html(d);
			
			$("li[data-role-item='modperms']").click(function(){
				Moj.Arn.Moduleadmin.loadPerms();
			});
			
			$("li[data-role-item='submods']").click(function(){
				Moj.Arn.Moduleadmin.loadSubMods();
			});
		});
		
		Moj.scroll.top();
	});
	
	$(document).off("click","#arn-mod-desc");
	$(document).on("click","#arn-mod-desc",function(e){
		Moj.u.iedit.call(this,function(t){
			Moj.api("Moduleadmin","one","Append",{"m":Moj.Arn.Moduleadmin.m,"desc":t},"POST");
		});
	});
	$(document).off("click","#arn-mod-desc > textarea");
	$(document).on("click","#arn-mod-desc > textarea",function(e){
		return false;
	});
	
	$(document).off("change","#arn-mod-parent");
	$(document).on("change","#arn-mod-parent",function(){
		Moj.confirm("Change parent module?",function(){
			Moj.api("Moduleadmin","one","Append",{"m":Moj.Arn.Moduleadmin.m,"p":$("#arn-mod-parent").val()},"POST");
		});
	});
	
	$(document).off("click","#arn-moduleadmin-details .btnDelete");
	$(document).on("click","#arn-moduleadmin-details .btnDelete",function(){
		Moj.confirm("Delete this module?",function(){
			Moj.api("Moduleadmin","one","Delete",{"m":Moj.Arn.Moduleadmin.m},function(j){
				Moj.n.go("AclModules");
			},"POST");
		});
	});
	
	$(document).off("click","#arn-modperm-menu > .icon-locked");
	$(document).on("click","#arn-modperm-menu > .icon-locked",function(){
		var pMod = $(this).closest("li").next();
		var perm = $("#arn-mod-permlist").val();
		var group = $(this).closest("li").attr('data-role-id');
		Moj.confirm("Deny "+perm+" access to "+group+" on "+Moj.Arn.Moduleadmin.m,
		function(){
			Moj.api("Groupadmin","groupPerms","Write",{'role':group,'resource':Moj.Arn.Moduleadmin.m,'perm':perm,'atype':'Deny'},
			function(){
				Moj.Arn.Moduleadmin.loadGroupModPerms.call(pMod.get(0),true);
			},"POST");
		});
	});
	
	$(document).off("click","#arn-modperm-menu > .icon-unlocked");
	$(document).on("click","#arn-modperm-menu > .icon-unlocked",function(){
		var pMod = $(this).closest("li").next();
		var perm = $("#arn-mod-permlist").val();
		var group = $(this).closest("li").attr('data-role-id');
		Moj.confirm("Allow "+perm+" access to "+group+" on "+Moj.Arn.Moduleadmin.m,
		function(){
			Moj.api("Groupadmin","groupPerms","Write",{'role':group,'resource':Moj.Arn.Moduleadmin.m,'perm':perm,'atype':'Allow'},
			function(){
				Moj.Arn.Moduleadmin.loadGroupModPerms.call(pMod.get(0),true);
			},"POST");
		});
	});
	
	Moj.extend("Arn.Moduleadmin",{
		m:null,
		perms:null,
		lmod:null,
		ltgt:null,
		run: function(){
			var f = function(){
				Moj.jp.lv('Arn','Aclmoduleadmin','module-admin',function(d){
					$(Moj.n.t()).html(d);
					
					Moj.g.form("arn-frm-addModule",function(){
						Moj.n.go("AclModules");
					});
					
					$("#filtermod").keyup(function(){
						if($(this).val().length > 1){
							$("#arn-modules-mgt > li").hide();
							$("#arn-modules-mgt > li:contains("+$(this).val()+")").show();
						} else {
							$("#arn-modules-mgt > li").show();
						}
					});
				});
				
				Moj.Arn.Resources.d('Module Management');
			};
			f();
			
			Moj.n.a('AclModules',f);
			Moj.n.h('AclModules');
		},
		
		loadSubMods: function(){
			Moj.jp.lv("Arn","Aclmoduleadmin","sub-mods",{m:Moj.Arn.Moduleadmin.m},function(d){
				$("#arn-moduleadmin-access").html("<h2><i class=\"icon-list\"></i> Sub Modules</h2>"+d);
				
				$("#arn-moduleadmin-access #arn-acl-sub-mods > li").click(function(){
					$("#arn-modules-mgt > li:contains('"+$(this).text()+"')").click();
				});
			});
		},
		
		loadPerms: function(){
			Moj.jp.lv("Arn","Aclmoduleadmin","all-groups",function(d){
				$("#arn-moduleadmin-access").html("<h2><i class=\"icon-locked\"></i> Module Permissions</h2>"+d);
				
				$("#arn-moduleadmin-access #arn-acl-all-groups > li:nth-child(odd)").click(function(e,i){
					
					$("#arn-moduleadmin-access #arn-acl-all-groups > li:nth-child(even)").each(function(){
						if(!$(this).is(":hidden") && $(e.currentTarget).attr('data-role-id') != $(this).prev().attr('data-role-id')){
							$(this).slideUp(200);
						}
					});
					
					if($(this).next().is(":hidden")){
						Moj.Arn.Moduleadmin.clearModMenu();
						$(this).next().slideDown(200,Moj.Arn.Moduleadmin.loadGroupModPerms);
					}
				});
			});
		},
		
		loadGroupModPerms: function(renderMenu){
			var group = $(this).prev().attr('data-role-id');
			var tgt = $(this);
			
			if(!group){
				group = Moj.Arn.Moduleadmin.lmod;
				tgt = Moj.Arn.Moduleadmin.ltgt;
			} else {
				Moj.Arn.Moduleadmin.lmod = group;
				Moj.Arn.Moduleadmin.ltgt = tgt;
			}
			
			if(!renderMenu)
				Moj.Arn.Moduleadmin.setModMenu.call(this);
			
			Moj.api("Groupadmin","groupPerms","Read",{'role':group,'resource':Moj.Arn.Moduleadmin.m},
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
										Moj.api("Groupadmin","groupPerms","Delete",{'role':group,'resource':Moj.Arn.Moduleadmin.m,'perm':li.attr('data-role-mod')},
										function(){
											Moj.Arn.Moduleadmin.loadGroupModPerms.call(li.closest("li"),true);
										},"POST");
									});
									
								} else {
									Moj.alert("Unable to remove inherited permissions.");
								}
							});
						}
					}
				,'json');
		},
		
		setModMenu: function(){
			var mod = $(this).prev();
			mod.append("<div id=\"arn-modperm-menu\"><i class='icon-locked'></i>&nbsp;<i class='icon-unlocked'></i>&nbsp;&nbsp;<select id=\"arn-mod-permlist\"></select></div>");
			
			var plist = $("#arn-mod-permlist");
			plist.empty();
			
			if(Moj.Arn.Moduleadmin.perms == null){
				Moj.api("Groupadmin","allPerms","Read",
					function(j){
						if(j && j.response){
							var perms = j.response;
							for(var i=0;i < perms.length;i++){
								var perm = perms[i]['access'];
								if(perm != "Service")
									Moj.Arn.Moduleadmin.perms += "<option>"+perm+"</option>";
							}
						}
						
						plist.append(Moj.Arn.Moduleadmin.perms);
					});
			} else {
				plist.append(Moj.Arn.Moduleadmin.perms);
			}
		},
		
		clearModMenu: function(){
			$("#arn-moduleadmin-access #arn-acl-all-groups > li:nth-child(odd) > div").remove();
		}
	});
	
	Moj.Arn.Moduleadmin.run();
	Moj.u.lslist['Arn.Moduleadmin.js'] = Moj.Arn.Moduleadmin.run;
});