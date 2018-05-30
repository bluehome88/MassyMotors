$(function(){
	Moj.u.lc("Hilo.Fbpromos.css",true);
			
	Moj.extend("Hilo.Fbpromos",{
		run: function(){
			var f = function(){
				Moj.jp.lv('Hilo','Fbpromos','index',function(d){
					$(Moj.n.t()).html(d);
						
				});
				
				Moj.Arn.Resources.d('Facebook Promos');
			};
			f();
			
			Moj.n.a('Fbpromos',f);
			Moj.n.h('Fbpromos');
		}
	});
			
	Moj.Hilo.Fbpromos.run();
	Moj.u.lslist['Hilo.Fbpromos.js'] = Moj.Hilo.Fbpromos.run;
			
});
