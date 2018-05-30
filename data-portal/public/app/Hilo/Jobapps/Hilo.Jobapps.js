$(function(){
	Moj.u.lc("Hilo.Jobapps.css",true);
			
	Moj.extend("Hilo.Jobapps",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Jobapps','index',function(d){
					$(Moj.n.t()).html(d);
					
					$("#appDate").keyup(function(){
						var date = $(this).val();
						
						if(date.length > 2){
							Moj.Hilo.Jobapps.clearSecs();
							
							Moj.api("Jobapps","datelist","Read",{'date':date},Moj.Hilo.Jobapps.datelist);
							
						} else {
							Moj.Hilo.Jobapps.clearSecs();
						}
					});
					
					$("#appDetails").keyup(function(){
						var query = $(this).val();
						
						if(query.length > 2){
							Moj.Hilo.Jobapps.clearSecs();
							
							Moj.api("Jobapps","applookup","Read",{'q':query},Moj.Hilo.Jobapps.listapp);
							
						} else {
							Moj.Hilo.Jobapps.clearSecs();
						}
					});
					
				});
				
				Moj.Arn.Resources.d('Job Applications');
			};
			f();
			
			Moj.n.a('Jobapps',f);
			Moj.n.h('Jobapps');
		},
		
		datelist: function(d){
			$("#resDate").empty();
			
			if(d.response){
				var dates = d.response;
				for(var i=0;i<dates.length;i++){
					var day = dates[i];
					var li = $("<li>");
					
					li.text(day);
					li.click(Moj.Hilo.Jobapps.applist);
					
					$("#resDate").append(li);
					
				}
				
			}
		},
		
		applist: function(){
			$("#resDate > li").removeClass("selected");
			$(this).addClass("selected");
			
			var date = $(this).text();
			$("#resDetails").empty();
			Moj.api("Jobapps","applist","Read",{'date':date},Moj.Hilo.Jobapps.listapp);
		},
		
		listapp: function(d){
			$("#resDetails").empty();
			if(d.response){
				var apps = d.response;
				for(var i=0;i<apps.length;i++){
					var app = apps[i];
					var li = $("<li>");
					li.attr('data-id',app['id']);
					
					var dl = $("<dl>");
					dl.append("<dt>Name</dt><dd>"+app['firstname']+" "+app['lastname']+"</dd>");
					dl.append("<dt>"+app['employment_type']+"</dt><dd>"+app['position']+"</dd>");
					li.append(dl);
					li.click(Moj.Hilo.Jobapps.appdetails);
					
					$("#resDetails").append(li);
					
				}
			}
			Moj.scroll.top();
		},
		
		appdetails: function(){
			$("#resDetails > li").removeClass("selected");
			$(this).addClass("selected");
			
			var apid = $(this).attr('data-id');
			Moj.jp.lv("Hilo","Jobapps","appdetails",{'id':apid},function(v){
				$("#jobapps-details").html(v);
				
				Moj.scroll.top();
			});
		},
		
		clearSecs: function(){
			$("#jobapps-details").empty();
			$("#resDetails").empty();
			$("#resDate").empty();
		}
	});
			
	Moj.Hilo.Jobapps.run();
	Moj.u.lslist['Hilo.Jobapps.js'] = Moj.Hilo.Jobapps.run;
			
});
