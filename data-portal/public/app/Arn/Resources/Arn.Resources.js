$(document).ready(function(){
	Moj.u.lc('Arn.Resources.css',true);
	
	if($(".arn-header-bar > div.bc").length > 0) $(".arn-header-bar > div.bc").remove();
	$(".arn-header-bar").append($("<div class='bc'><span></span></div>"));
	
	$(document).on("click",".rnav-buttons[title='Back']",function(){history.back();});
	$(document).on("click",".rnav-buttons[title='Main']",function(){
		location.href=Moj.o.b;
	});
	
	Moj.extend("Arn.Resources",{
		disp:[],
		n:[],
		load:function(p,f){
			var hcb = function(){
				Moj.jp.lv('Arn','Resources','load',p,function(d){			
					$(Moj.n.t()).html(d);
					
					$('[data-resource-children="true"]').on("click",function(){
						var id = $(this).attr('id');
						Moj.Arn.Resources.load({'m':id},Moj.Arn.Resources.appendBack);
					});
					
					if(Moj.u.t("Function",f)) f.call(f.prototype);
				});
				
				if(p && p.m){
					var bcn;
					if(Moj.Arn.Resources.disp[p.m]){
						bcn = Moj.Arn.Resources.disp[p.m];
					} else {
						bcn = $("#"+p.m).find(".name").text();
						Moj.Arn.Resources.disp[p.m] = bcn;
					}
					
					Moj.Arn.Resources.d(bcn);
					
					Moj.Arn.Resources.btns();
				}
			};
			
			if(p && p.m){
				Moj.n.h(p.m);
				Moj.n.a(p.m,hcb);
			}
			
			hcb.call(this);
		},
		
		btns:function(){
			if($(".rnav-buttons").length == 0)//Moj.Arn.Resources.n.length == 1 && 
				$(".arn-header-bar > .bc")
				.prepend('<ul id="nav" class="rnav-buttons"><li><a href="#"><i class="icon-grid-view"></i></a><ul id="mbtngrid"></ul></li></ul>');
					//.prepend('<a class="rnav-buttons" title="Main"><i class="icon-grid-view"></i></a>')
					//.prepend('<a class="rnav-buttons" title="Back"><i class="icon-arrow-left-3"></i></a>');
		},
		
		d:function(t){
			Moj.Arn.Resources.n.push(t);
			$(".arn-header-bar > .bc > span").text(t);
			Moj.Arn.Resources.btns();
		},
		
		dropdown:function(){
			Moj.Arn.Resources.btns();
			
			if($("#mbtngrid > li").length > 0) return;
			
			$.get(baseUrl()+"Jax/resources/view/access/Render",function(d){
				console.log(d);
				
				var mods = d.response.modules;
				for(var m in mods){
					var mainItem = $("<li>").append("<a id=\""+m+"\" href=\"\"><i class=\""+mods[m]['config']['icon']+"\"></i> "+mods[m]['config']['display_name']+"</a>");
					if(mods[m]['children']){
						mainItem.append($("<ul>"));
						Moj.Arn.Resources.submenu(mainItem,mods[m]['children']);
					}
					$("#mbtngrid").append(mainItem);
				}
				
			},"json");
		},
		
		submenu: function(parent,children){
			for(var m in children){
				var mainItem = $("<li>").append("<a id=\""+m+"\" href=\"\"><i class=\""+children[m]['config']['icon']+"\"></i> "+children[m]['config']['display_name']+"</a>");
				if(children[m]['children']){
					mainItem.append($("<ul>"));
					Moj.Arn.Resources.submenu(mainItem,children[m]['children']);
				}
				parent.find("ul").first().append(mainItem);
			}
		}
	});
	
	Moj.u.lslist['Arn.Resources.js'] = Moj.Arn.Resources.load;
});