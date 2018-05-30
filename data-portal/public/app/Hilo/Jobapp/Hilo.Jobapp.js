$(function(){
	Moj.u.lc("Hilo.Jobapp.css",true);
			
	Moj.extend("Hilo.Jobapp",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Jobapp','list',function(d){
					$(Moj.n.t()).html(d);
						
					$(".datepicker").datepicker({
						dateFormat:"yy-mm-dd"
					});
					
					
					
					Moj.g.form("frmJappRpt",function(r){
						if(r && r.response){
							
							var colspan = 0;
							
							$("#jappTblHdr").empty();
							$("#jappTblBody").empty();
							$("ul.showcols li.selected").each(function(){
								var col = $(this).attr('data-name');
								col = ucwords(col.replace("_"," "));
								$("#jappTblHdr").append("<th>"+col+"</th>");
								
								colspan++;
							});
							
							$("#jappTblHdr").append("<th>Reviewed</th><th>Reviewed By</th>");
							$("#jappTblHdr").append("<th>&nbsp;</th>");
							
							var data = r.response;
							var recordId = 0;
							for(var i=0;i<data.length;i++){
								var row = data[i];
								var tr = $("<tr>");
								
								for(var r in row){
									if(r=="id") {
										recordId = row[r];
										continue;
									}
									
									if(r == "cv" && row[r]){
										tr.append("<td><a href=\"http://massystorestt.com/data-portal/public/Hilo/Jobapp/cv/cid/"+recordId+"\" target=\"_blank\">View CV</a></td>");
									} else {
										tr.append("<td>"+row[r]+"</td>");
									}
								}
								
								var btn = "<input data-id=\""+row['id']+"\"type=\"button\" class=\"bg-color-blue fg-color-white btnReview\" value=\"Mark as Reviewed\" />";
								if(row['sys_reviewed']) btn = "";
								tr.append("<td>"+btn+"</td>");
								
								$("#jappTblBody").append(tr);
								
							}
							
							if(data.length > 0){
								$("#rpt-sumtotal")
								.empty()
								.append("<h3><strong>Records: </strong>"+data.length+"</h3>")
								.show();
							}
							
							$(".btnReview").click(function(){
								rid = $(this).attr('data-id');
								
								Moj.confirm("Mark as Reviewed?",function(){
									Moj.api("Jobapp","markReviewed","Update",{'id':rid},function(){
										$("input[value='View']").click();
									});
								});
							});
							
						}
					});
					
					$("ul.showcols li").click(function(){
						$(this).toggleClass("selected");
						
						if(!$(this).hasClass("selected")){
							$(this).find("input").remove();
						} else {
							$(this).append("<input type=\"hidden\" name=\"col[]\" value=\""+$(this).attr('data-name')+"\" />");
						}
						
						//$("#frmCustRpt").submit();
						//Moj.scroll.top();
					});
					
					$("button[data-export]").click(Moj.Hilo.Jobapp.exportAs);
					
				});
				
				Moj.Arn.Resources.d('Job Applications');
			};
			f();
			
			Moj.n.a('Jobapp',f);
			Moj.n.h('Jobapp');
		},
		
		exportAs: function(){
			var opt = $(this).attr('data-export');
			var params = $("#frmJappRpt").serialize();
			
			switch(opt){
			case "csv":
				window.open(baseUrl()+"Jax/resources/Jobapp/access/Read/get/exportCSV?"+params,"_blank");
				break;
				
			case "print":
				window.open(baseUrl()+"Hilo/Jobapp/print?"+params,"_blank");
				break;
			}
		}
	});
			
	Moj.Hilo.Jobapp.run();
	Moj.u.lslist['Hilo.Jobapp.js'] = Moj.Hilo.Jobapp.run;
			
});
