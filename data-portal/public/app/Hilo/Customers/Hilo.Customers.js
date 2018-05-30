$(function(){
	Moj.u.lc("Hilo.Customers.css",true);
			
	Moj.extend("Hilo.Customers",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Customers','index',function(d){
					$(Moj.n.t()).html(d);
					
					Moj.Hilo.Customers.search();
				});
				
				Moj.Arn.Resources.d('Customers');
			};
			f();
			
			Moj.n.a('Customers',f);
			Moj.n.h('Customers');
		},
		
		search: function(){
			$("#hilo-search-customers").keyup(function(){
				var term = $(this).val();
				if(term.length < 3) {
					$(".hilo-customers-list").empty();
					return;
				}
				
				Moj.api("Customers","search","Read",{'q':term},function(d){
					if(d && d.response){
						$(".hilo-customers-list").empty();
						$("#hilo-customers-options").empty();
						$("#hilo-customers-view").empty();
						
						var customers = d.response;
						
						if(customers.length == 0) {
							$(".hilo-customers-list").append("<li>No results</li>");
							return;
						}
						
						for(var i=0;i<customers.length;i++){
							try {
							var cust = customers[i];
							var li = $("<li>");
							li.html(cust['AcctNo']+"<br/>"+cust['FirstName']+" "+cust['LastName']);
							li.attr("data-id",cust['AcctNo']);
							$(".hilo-customers-list").append(li);
							} catch(e){}
						}
						
						$(".hilo-customers-list li").click(function(){
							
							$(".hilo-customers-list li").removeClass("selected");
							$(this).addClass("selected");
							
							Moj.Hilo.Customers.viewcust.call(this);
						});
					}
				});
			});
		},
		
		viewcust:function(){
			var li = $(this);
			Moj.jp.lv("Hilo","Customers","view",{'cust':li.attr('data-id')},function(v){
				$("#hilo-customers-view").html(v);
								
				$("#hilo-customer-view dd[data-role-iedit]").each(function(){
					$(this).data("ieditCB",function(t){
						if(t=="Not Defined"){
							$(this).html("<em>Not Defined</em>");
							return;
						}
						
						var dt = $(this).prev("dt");
						var c = dt.attr("data-role-id");
						if(c != undefined){
							Moj.api("Customers","one","Update",{'p':c,'v':t,'s':li.attr("data-id")},"POST");
						}
					});
				});
				
				$("#hilo-customer-view select").change(function(){
					var c = $(this).closest("dd").prev("dt").attr("data-role-id");
					var v = $(this).val();
					Moj.api("Customers","one","Update",{'p':c,'v':v,'s':li.attr("data-id")},"POST");
					
				});
				
				$("#hilo-customers-view button[data-id='pwdreset']").click(function(){
					Moj.confirm("Reset this account password?",function(){
						Moj.api("Customers","resetPassword","Update",{'s':li.attr("data-id")},function(v){
							if(v.response == 1){
								alert('Password was reset');
							} else {
								alert('Password could not be reset or it is already reset. Try again or contact support if this message persists.');
							}
						},"POST");
					});
				});
			});			
		}
	});
			
	Moj.Hilo.Customers.run();
	Moj.u.lslist['Hilo.Customers.js'] = Moj.Hilo.Customers.run;
			
});
