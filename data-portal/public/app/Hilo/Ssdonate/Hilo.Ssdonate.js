$(function(){
	Moj.u.lc("Hilo.Ssdonate.css",true);
			
	Moj.extend("Hilo.Ssdonate",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Ssdonate','index',function(d){
					$(Moj.n.t()).html(d);
					
					$("#listPending > tbody > tr[data-id]").click(function(){
						var did = $(this).attr('data-id');
						Moj.jp.lv("Hilo","Ssdonate","details",{'did':did},function(v){
							$("#donateDetails").html(v);
							
							$("#btnProcess").click(function(){
								Moj.confirm("Mark as processed? Note: This will remove the record and it will no longer be available.<br/>" +
										"Please ensure you process the transfer via SMS before marking as processed.",function(){
									Moj.api("Ssdonate","process","Update",{'did':did},function(){
										Moj.Hilo.Ssdonate.run();
									});
								});
							});
						});
					});
				});
				
				Moj.Arn.Resources.d('Smart Shopper Donate');
			};
			f();
			
			Moj.n.a('Ssdonate',f);
			Moj.n.h('Ssdonate');
		}
	});
			
	Moj.Hilo.Ssdonate.run();
	Moj.u.lslist['Hilo.Ssdonate.js'] = Moj.Hilo.Ssdonate.run;
			
});
