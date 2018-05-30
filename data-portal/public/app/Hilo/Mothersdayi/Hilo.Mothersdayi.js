$(function(){
	Moj.u.lc("Hilo.Mothersdayi.css",true);
			
	Moj.extend("Hilo.Mothersdayi",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Mothersdayi','index',function(d){
					$(Moj.n.t()).html(d);
					
					$(".tblEntries td").click(function(){
						$(".tblEntries tr").removeClass("selected-row");
						$(this).parent("tr").addClass("selected-row");
						
						var eid = $(this).parent("tr").attr("data-id");
						
						Moj.jp.lv("Hilo","Mothersdayi","view",{'eid':eid},function(v){
							$("#valEntry").html(v);
							
							$("#entryApprove").click(function(){
								
								var eid = $(this).attr('data-entry');
								
								Moj.confirm("Approve this poem?<br/>Note: Poem will be LIVE on contest gallery and the entrant will no longer be able to edit.",
										function(){
									
									Moj.api("Mothersdayi","one","Update",{'eid':eid},function(){
										Moj.Hilo.Mothersdayi.run();
									});
									
								});
							});
						});
					});
						
				});
				
				Moj.Arn.Resources.d('Mothers Day 2014');
			};
			f();
			
			Moj.n.a('Mothersdayi',f);
			Moj.n.h('Mothersdayi');
		}
	});
			
	Moj.Hilo.Mothersdayi.run();
	Moj.u.lslist['Hilo.Mothersdayi.js'] = Moj.Hilo.Mothersdayi.run;
			
});
