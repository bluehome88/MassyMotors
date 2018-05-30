$(function(){
	Moj.u.lc('Arn.Groupadmin.css',true);
	
	$(document).off("click","#arn-ugroups-mgt > li");
	$(document).on("click","#arn-ugroups-mgt > li",function(){
		$("#arn-ugroups-mgt > li").removeClass("selected");
		$(this).addClass("selected");
		
		Moj.Arn.Groupadmin.g = $(this).text();
		
		$("#arn-groupadmin-access").empty();
		
		Moj.jp.lv("Arn","Aclgroupadmin","group-details",{m:Moj.Arn.Groupadmin.g},function(d){
			$("#arn-groupadmin-details").html(d);
			
			$("li[data-role-item='gperms']").click(function(){
				Moj.Arn.Groupadmin.loadPerms();
			});
			
			$("li[data-role-item='gmembers']").click(function(){
				Moj.Arn.Groupadmin.loadMembers();
			});
		});
	});
	
	$(document).off("click","#arn-grp-desc");
	$(document).on("click","#arn-grp-desc",function(e){
		Moj.u.iedit.call(this,function(t){
			Moj.api("Groupadmin","one","Append",{"g":Moj.Arn.Groupadmin.g,"desc":t},"POST");
		});
	});
	
	$(document).off("click","#arn-grp-desc > textarea");
	$(document).on("click","#arn-grp-desc > textarea",function(e){
		return false;
	});
	
	$(document).off("change","#arn-grp-parent");
	$(document).on("change","#arn-grp-parent",function(){
		Moj.confirm("Change parent group?",function(){
			Moj.api("Groupadmin","one","Append",{"g":Moj.Arn.Groupadmin.g,"p":$("#arn-grp-parent").val()},"POST");
		});
	});
	
	$(document).off("click","#arn-groupadmin-details .btnDelete");
	$(document).on("click","#arn-groupadmin-details .btnDelete",function(){
		Moj.confirm("Delete this group?",function(){
			Moj.api("Groupadmin","one","Delete",{"g":Moj.Arn.Groupadmin.g},function(j){
				Moj.n.go("AclGroups");
			},"POST");
		});
	});
	
	$(document).off("click","#arn-modperm-menu > .icon-locked");
	$(document).on("click","#arn-modperm-menu > .icon-locked",function(){
		var pMod = $(this).closest("li").next();
		var perm = $("#arn-mod-permlist").val();
		var mod = $(this).closest("li").attr('data-role-id');
		Moj.confirm("Deny "+perm+" access to "+Moj.Arn.Groupadmin.g+" on "+mod,
		function(){
			Moj.api("Groupadmin","groupPerms","Write",{'role':Moj.Arn.Groupadmin.g,'resource':mod,'perm':perm,'atype':'Deny'},
			function(){
				Moj.Arn.Groupadmin.loadGroupModPerms.call(pMod.get(0),true);
			},"POST");
		});
	});
	
	$(document).off("click","#arn-modperm-menu > .icon-unlocked");
	$(document).on("click","#arn-modperm-menu > .icon-unlocked",function(){
		var pMod = $(this).closest("li").next();
		var perm = $("#arn-mod-permlist").val();
		var mod = $(this).closest("li").attr('data-role-id');
		Moj.confirm("Allow "+perm+" access to "+Moj.Arn.Groupadmin.g+" on "+mod,
		function(){
			Moj.api("Groupadmin","groupPerms","Write",{'role':Moj.Arn.Groupadmin.g,'resource':mod,'perm':perm,'atype':'Allow'},
			function(){
				Moj.Arn.Groupadmin.loadGroupModPerms.call(pMod.get(0),true);
			},"POST");
		});
	});
	
	Moj.extend("Arn.Groupadmin",{
		g:null,
		perms:null,
		lmod:null,
		ltgt:null,
		run: function(){
			var f = function(){
				Moj.jp.lv('Arn','Aclgroupadmin','group-admin',function(d){
					$(Moj.n.t()).html(d);
					
					Moj.g.form("arn-frm-addGrp",function(){
						Moj.n.go("AclGroups");
					});
				});
				
				Moj.Arn.Resources.d('Group Administration');
			};
			f();
			
			Moj.n.a('AclGroups',f);
			Moj.n.h('AclGroups');
		},
		
		loadMembers: function(){
			Moj.jp.lv("Arn","Aclgroupadmin","group-members",{'g':Moj.Arn.Groupadmin.g},function(d){
				$("#arn-groupadmin-access").html("<h2><i class=\"icon-user-2\"></i> Group Members</h2>"+d);
				
				$("#arn-acl-group-members > li").click(function(){
					var s = $(this).text();
					Moj.u.ls("Arn.Roleadmin.js",true,null,function(){
						Moj.Arn.Roleadmin.pr = function(){
							$("#arn-search-users-input").val(s).keyup();
						};
					});
				});
			});
		},
		
		loadPerms: function(){
			Moj.jp.lv("Arn","Aclmoduleadmin","all-modules",function(d){
				$("#arn-groupadmin-access").html("<h2><i class=\"icon-locked\"></i> Group Permissions</h2>"+d);
				
				$("#arn-groupadmin-access #arn-acl-all-mods > li:nth-child(odd)").click(function(e,i){
					
					$("#arn-groupadmin-access #arn-acl-all-mods > li:nth-child(even)").each(function(){
						if(!$(this).is(":hidden") && $(e.currentTarget).attr('data-role-id') != $(this).prev().attr('data-role-id')){
							$(this).slideUp(200);
						}
					});
					
					if($(this).next().is(":hidden")){
						Moj.Arn.Groupadmin.clearModMenu();
						$(this).next().slideDown(200,Moj.Arn.Groupadmin.loadGroupModPerms);
					}
				});
			});
		},
		
		loadGroupModPerms: function(renderMenu){
			var mod = $(this).prev().attr('data-role-id');
			var tgt = $(this);
			
			if(!mod){
				mod = Moj.Arn.Groupadmin.lmod;
				tgt = Moj.Arn.Groupadmin.ltgt;
			} else {
				Moj.Arn.Groupadmin.lmod = mod;
				Moj.Arn.Groupadmin.ltgt = tgt;
			}
			
			if(!renderMenu)
				Moj.Arn.Groupadmin.setModMenu.call(this);
			
			Moj.api("Groupadmin","groupPerms","Read",{'role':Moj.Arn.Groupadmin.g,'resource':mod},
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
										Moj.api("Groupadmin","groupPerms","Delete",{'role':Moj.Arn.Groupadmin.g,'resource':mod,'perm':li.attr('data-role-mod')},
										function(){
											Moj.Arn.Groupadmin.loadGroupModPerms.call(li.closest("li"),true);
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
			
			if(Moj.Arn.Groupadmin.perms == null){
				Moj.api("Groupadmin","allPerms","Read",
					function(j){
						if(j && j.response){
							var perms = j.response;
							for(var i=0;i < perms.length;i++){
								var perm = perms[i]['access'];
								if(perm != "Service")
									Moj.Arn.Groupadmin.perms += "<option>"+perm+"</option>";
							}
						}
						
						plist.append(Moj.Arn.Groupadmin.perms);
					});
			} else {
				plist.append(Moj.Arn.Groupadmin.perms);
			}
		},
		
		clearModMenu: function(){
			$("#arn-groupadmin-access #arn-acl-all-mods > li:nth-child(odd) > div").remove();
		}
	});
	
	Moj.Arn.Groupadmin.run();
	Moj.u.lslist['Arn.Groupadmin.js'] = Moj.Arn.Groupadmin.run;
});