$(function(){
	Moj.u.lc("Hilo.Valentinesi.css",true);
			
	Moj.extend("Hilo.Valentinesi",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Valentinesi','index',function(d){
					$(Moj.n.t()).html(d);
					
					$(".tblEntries td").click(function(){
						$(".tblEntries tr").removeClass("selected-row");
						$(this).parent("tr").addClass("selected-row");
						
						var eid = $(this).parent("tr").attr("data-id");
						
						Moj.jp.lv("Hilo","Valentinesi","view",{'eid':eid},function(v){
							$("#valEntry").html(v);
							
							$("#entryApprove").click(function(){
								
								var eid = $(this).attr('data-entry');
								
								Moj.confirm("Approve this poem?<br/>Note: Poem will be LIVE on contest gallery and the entrant will no longer be able to edit.",
										function(){
									
									Moj.api("Valentinesi","one","Update",{'eid':eid},function(){
										Moj.Hilo.Valentinesi.run();
									});
									
								});
							});
						});
					});
					
					
				});
				
				Moj.Arn.Resources.d('Valentines 2014');
			};
			f();
			
			Moj.n.a('Valentinesi',f);
			Moj.n.h('Valentinesi');
		}
	});
			
	Moj.Hilo.Valentinesi.run();
	Moj.u.lslist['Hilo.Valentinesi.js'] = Moj.Hilo.Valentinesi.run;
			
});
