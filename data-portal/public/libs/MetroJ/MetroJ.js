/**
 * MetroJ - A JavaScript framework for JaxPHP.
 * @version 0.1.20130602.0340
 * @author Ricardo Assing
 */

// :icontains()
// Case-insenstive :contains()
// http://css-tricks.com/snippets/jquery/make-jquery-contains-case-insensitive/
$.extend($.expr[":"], {
   "icontains": function(elem, i, match, array) {
   return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
   }
});

(function (window,undefined,$) {
   function Moj()
   {
	   (window === this)?_ = new Moj():_ = this;
	   return _.opts().u.ls('MetroJ.main.js',_.o.mjp,{async:false});
   }
   Moj.moc =  {
		   jpc:'Jax-Active-Application'
   };
   Moj.mo = {
	   e:true,		// Log errors
	   v:true,		// Log to console (debugging)
	   fbr:true,	// Run frame breaker
	   b:null,		// Base Url
	   appopt:null,
	   sf:			// Support file to load
	   {
		   cookies:"jquery.cookie.js",
		   json:"json2.js"
		  // modernizer:"modernizr.custom.92330.js"
	   },
	   mjp:"libs/MetroJ/",
	   sfd:"js/MetroJ-Support/",	// JS support files dir (relative to base url)
	   apd:"app/"	// User apps directory
   };
   
   // ERROR TYPES
   Moj.et = {
	   ERROR:"error",
	   INFO:"info",
	   WARN:"warning"
   };
   
   Moj.f = {
		ldr_s: function(){
			var ldr = $("<div>").attr("id","mj-ldr");
			$('body').append(ldr);
		},
		ldr_h: function(){
			$("#mj-ldr").fadeOut(500).remove();
		}
   };
   
   // USERDEFINED CALLBACKS
   Moj.udcb = {
   };
   
   // USER NAVIGATION OBJECT
   Moj.nav = {
		   _moj_tgt:"body"
   };
   
   Moj.prototype = 
   {
		   ie_l:[],
		   ns:null,
		   
		   // OPTIONS
		   o:{},
		   opts:function(o)
		   {
			   _.o = $.extend(true,{},Moj.mo, o);
			   return _;
		   },
		   
		   // INITIALIZATION
		   init:function()
		   {
			   if(_.o.b == null)
			   {
				   _.alert('Base url not set.',Moj.et.ERROR);
			   } else {
				   
				   if(_.o.fbr) _.u.fbr();	// Run framebreaker
				   _.u.lsf();				// Load support files
				   
				   _.u.lc("MetroJ.css",_.o.b+"libs/MetroJ/");
				   
				   $.ajaxPrefilter("html",function(o,s,x){
						var ping = baseUrl()+"Jax/Auth/current-session";
						var surl = s.url.split("/");
						if(surl[surl.length-2] != "Auth" && !s['skipPrefilter']) {
							x.abort();
							_.c.ja(ping,function(r){
								if(r && r.response){
									s['skipPrefilter'] = true;
									$.ajax(s);
								} else {
									_.c.pja({"response":{"logout":"Session expired. "}});
								}
							});
						}
					});
				   
				   // Show a "loading" indicator for XMLHttpRequests (AJAX)
					$(document)
					.ajaxStart(function() {
						Moj.f.ldr_s();
					})

					// Hide "loading" indicator when AJAX request completes
					.ajaxStop(function() {
						Moj.f.ldr_h();
					})
					.ajaxComplete(function(e,xhr,o){
						if(o.dataType!='json' && xhr.responseText.substr(0,1) == "{"){
							var j = $.parseJSON(xhr.responseText);
							_.c.pja(j);
						}
					});
					
					// custom <a> handling
					_.g.a();
					
					// history api
					window.onpopstate = function(event) {
						_.n.go(location.pathname.split("/").pop(),null,true);
					};
			   }
			   return _;
		   },
		   
		   api:function(r,m,a,p,c,h){
			   if(!r) return _.alert("Resource not specified.");
			   if(!m) return _.alert("Method not specified.");
			   if(!a) return _.alert("Access type not specified.");
			   
			   var cb=function(){};
			   var o = {};
			   var ht = "GET";
			   try{
				   if(_.u.t("Function",p)) cb = p;
				   if(_.u.t("Object",p)) o = p;
				   if(_.u.t("String",p)) o = p;
				   if(_.u.t("Function",c)) cb = c;
				   if(_.u.t("String",c) && (c=="GET" || c=="POST")) ht = c;
				   if(_.u.t("String",h) && (h=="GET" || h=="POST")) ht = h;
			   } catch(e){_.log(e);}
			   
			   _.c.ja(baseUrl()+"Jax/resources/"+r+"/access/"+a+"/get/"+m, cb, o,ht);
		   },
		   
		   // UTILITIES
		   u:{
			   lslist:{},
			   fbr: function()	// Framebreaker
			   {
				   if (window.location !== window.top.location) window.top.location = window.location;
			   },
			   lsf: function()	// Load Support File
			   {
				   if(this.t("Object", _.o.sf))
				   {
					   for(var i in _.o.sf)
					   {
						   var d = _.o.sf;
						   this.ls(d[i],_.o.b+_.o.sfd, {async:false});
					   }
				   }
				   return _;
			   },
			   ls: function(f,p,o,cb)	// Load Script (file,path,ajax options,callback)
			   {
				   if(p === true) p = _.o.b+_.o.apd+_.u.pfn(f)[1];
				   
				   if(_.u.t("Function",o)) {
					   cb = o;
					   o = null;
				   }
				   
				   
				   if(_.u.lslist[f]){
					   if(_.u.t("Function",cb)){
						   cb.call(cb.prototype);
					   }
					   
					   if(_.u.t("Function",_.u.lslist[f])){
						   _.u.lslist[f].call(_.u.lslist[f].prototype);
						   return;
					   }
				   }
				   
				   				   
				   var odef = {
						   url:p+f,
						   dataType:"script"
					   };
				   var opts = $.extend(true,odef,o);
				   $.ajax(opts)
				   .done(function(r){
					   if(_.u.t("Function",cb))
						   cb.apply(this,[r]);
					   
					   if(_.o.v) _.log(f+' loaded');
				   })
				   .fail(function(xhr,e,t){
					   _.alert('Failed to load '+f,Moj.et.ERROR);
				   });
				   
				   return _;
			   },
			   lc: function(n,p)	// Load CSS (name,path)
			   {
				   if(p === true){
					   p = _.o.apd+_.u.pfn(n)[1];
				   } else {
					   if(p == undefined)
						   p = "";
				   }
				  
				   if($("link[href*='"+p+n+"']").length > 0){
					   _.log(n+' already loaded');
					   return _;
				   }
				   
				   $("head").append("<link>");
				   css = $("head").children(":last");
				   css.attr({
					   rel : "stylesheet",
					   type : "text/css",
					   href : p+n + '?' + new Date().getTime()
					   });
				   if(_.o.v) _.log(n+' loaded');
				   return _;
			   },
			   t: function(type,obj)	// Type test
			   {
				   var clas = Object.prototype.toString.call(obj).slice(8, -1);
				   return obj !== undefined && obj !== null && clas === type;
			   },
			   pfn: function(n){			// Parse file name
				   var np = n.split(".");
				   var fp = "";
				   for(var i=0;i<np.length-1;i++){
					   fp += np[i]+"/";
				   }
				   return [n,fp];
			   },
			   cntr : function(o) {			// Counter
					var t, i = this, s = o.seconds || 10, updateStatus = o.onUpdateStatus
							|| function() {
							}, counterEnd = o.onCounterEnd || function() {
					};

					function dc() {
						updateStatus(s);
						if (s === 0) {
							counterEnd();
							i.stop();
						}
						s--;
					}

					this.start = function() {
						clearInterval(t);
						t = 0;
						s = o.seconds;
						t = setInterval(dc, 1000);
					};

					this.stop = function() {
						clearInterval(t);
					};
				},
				iedit:function(c,type){		// Inline editor
					if(!type) type = "text";
					var that = this;
					var t = $(this).text();
					
					switch(type){
					case "text":
						var el = $("<textarea>");
						el.text(t);
						break;
						
					case "date":
						var el = $("<div>");
						el.empty();
						el.datepicker({
							changeMonth: true,
							changeYear: true,
							dateFormat:"yy-mm-dd",
							defaultDate:t,
							onSelect:function(sDate){
								$(that).empty().text(sDate);
								if(_.u.t("Function",c)){
									c.call(that,sDate);
								}
							}});
						break;
					}
					
					if(el){
						$(this).empty().append(el);
						el.focus();
						el.select();
						el.keyup(function(e){
							if (e.keyCode == 27){
								el.blur();
							}
						});
						
						if(type != "date"){
							$(el).blur(function(){
								var txt = el.val();
								$(that).empty().text(txt);
								$(that).prev('dt').append(" <i class='icon-loading-2'></i>").children("i").fadeOut(2000);
								if(_.u.t("Function",c)){
									c.call(that,txt);
								}
							});
						}
					}
					
					return false;
				},
				te: function(e){		// Throw exception
					_.alert(e.message,Moj.et.ERROR);
				},
			   scb:function(i,cb,x)		// Set callback
			   {
				   if(x == undefined) x = true;
				   if(x || (!x && Moj.udcb[i] == undefined))
					   Moj.udcb[i] = cb;
				   
				   return _;
			   },
				ac:function(cn){	// Checks for auth cookie
					if($.cookie(cn)) return $.cookie(cn);
					return false;
				},
				trig: function(i,p,c){
					if(!c) c=_;
					if(_.u.t("Function",Moj.udcb[i]))
						Moj.udcb[i].apply(c,p);
				}
		   },
		   
		   // JaxPHP SPECIFIC
		   jp:
		   {
			   p: function(s)	// PERSIST - Allows JaxPHP to load correct application.
			   {
				   ($.cookie)?$.cookie(Moj.moc.jpc,s,1,{path:'/'}):_.log('$.cookie not loaded');
				   _.ns = s.split(".")[0];
					   _.c.ja(_.o.b+"Jax/client/application-options",function(j){
						   if(j && j.response){
							   document.title = j.response.appname;
							   _.o.appopt = j.response;
							   _.u.ls(s,true);
						   }
					   });
				   return _;
			   },
			   r: function(){	// RESET - Deletes the cookie set above
				   if($.cookie) $.cookie(Moj.moc.jpc,null,1,{path:'/'});
				   return _;
			   },
			   t: function(){
				   if(_.u.ac(Moj.moc.jpc)) return _.u.ac(Moj.moc.jpc);
				   return false;
			   },
			   lapp: function(){
				   _.c.ja(baseUrl()+"Jax/client/load-apps",function(json){

					   if(json.response){
						   var res = json.response;
						   
						   if(res.modules){
							   l = 0;
							   for(var z in res.modules){l++;}
							   if(!_.jp.t() && l > 1){
								   if($("link[href*='Metro-UI/css/modern.css']").length == 0)
									   _.u.lc('modern.css',baseUrl()+'libs/Metro-UI/css/');
								   
								   $("body").empty().append("<div style=\"padding:5px;\" class=\"tiles\"></div>");
								   
								   for(var m in res.modules){
									   var mod = res.modules[m];
									   var colors = ['green','greenDark','greenLight','pinkDark','darken','blue','blueDark','orange','orangeDark','red'];
									   var tile = $("<div id=\""+m+"\" class=\"tile bg-color-"+colors[Math.floor(Math.random()*colors.length)]+" outline-color-blueLight icon\">"+
											   "<div class=\"tile-content\">"+
											   "<img src=\""+mod.config['iconPath']+mod.config['icon']+"\" alt=\"\" /></div>"+
											   "<div class=\"brand\"><span class=\"name\">"+mod.config['display_name']+"</span></div>"+
											   "</div>")
									   .click(function(){
										   $("body").empty();
										   _.jp.p($(this).attr('id')+".js");
									   });
									   $(".tiles").append(tile);
								   }
							   } else {
								   if(_.jp.t()){
									   _.jp.p(_.jp.t());
								   } else {
									   for(var m in res.modules){
										   _.jp.p(m+".js");
										   break;
									   }
								   }
							   }
						   }
					   }
				   },{'metro':1});
				   
				   return _;
			   },
			   lv: function(ns,con,v,p,c){	// Load view from JaxPHP via AJAX.
				   var cb = function(){};
				   var o = {};
				  
				   try{
					   if(_.u.t("Function",p)) cb = p;
					   if(_.u.t("Object",p)) o = p;
					   if(_.u.t("Function",c)) cb = c;
					   if(_.u.t("Object",c)) o = c;
				   }catch(e){_.log(e);}
				   
				   $.get(_.o.b+ns+"/"+con+"/"+v,o,cb,'html');
				   return _;
			   }
		   },
		   
		   // COMMUNICATIONS
		   c:
		   {			   
			   ja: function(u,cb,d,gp)	// JSON ajax request with additional Jax processing.
			   {
				   if(u == undefined) return _.alert('JSON endpoint undefined.',Moj.et.ERROR);
				   if(!d || d == undefined) d = null;
				   
				   if(!gp) gp = "GET";
				   switch(gp){
				   		case "GET":
				   			var xhr = $.get(u,d,function(r){
				   				_.c.pja(r,cb);
				   			},'json').fail(function(xhr,s,e){
								_.alert(e,Moj.et.ERROR,xhr);
							});
				   			break;
				   			
				   		case "POST":
				   			var xhr = $.post(u,d,function(r){
				   				_.c.pja(r,cb);
				   			},'json').fail(function(xhr,s,e){
								_.alert(e,Moj.et.ERROR,xhr);
							});
				   			break;
				   }				   
				   return xhr;
			   },
			   pja:function(r,ucb){		// JaxPHP JSON response handler
				   
				   if(_.u.t("Object",r)){
				   for(var i in r){
					   switch(i){
					   		case "load":	// Trigger loading of an app file or app plugin script
					   			for (var p = 0; p < r[i].length; p++){
					   				var pfn = _.u.pfn(r[i][p]);
					   				pfn[1] = _.o.apd+pfn[1];
									_.u.ls.apply(_,pfn);
					   			}
					   			break;
					   		
					   		case "response":	// Successful responses
					   			if(_.u.t("Object",r[i])){
						   			for (var f in r[i]){
						   				switch(f){
						   				case "logout":
						   					_.jp.r();
						   					if(_.u.t("String",r[i]['logout'])){
						   						m = r[i]['logout'];
						   					} else {
						   						m = "Logged out. Redirecting in ";
						   					}
						   					_.ui.d(m+"<span id=\"Jax-Counter\"></span>");
						   					
						   					var delay = 3;
						   					if(r[i]['delay'] != undefined) delay = r[i]['delay'];
						   					var myCounter = new _.u.cntr({
												seconds : delay,
												onUpdateStatus : function(sec) {
													$("#Jax-Counter").text(sec);
												},
												onCounterEnd : function() {
													_.reset();
												}
											});
	
											myCounter.start();
						   					break;
						   				}
						   			}
					   			}
					   			if(_.u.t("Function",ucb)) ucb.call(ucb.prototype,r);
					   			break;
					   			
					   		case "error":	// Error responses.
					   			if (_.u.t("Function", r.error)
										|| r.error == null) {

									// For abort actions
									if (r.statusText == "abort"
											|| r.error == null)
										return r;

									var e = "<h4>Server Response: </h4><p>"
											+ r.responseText + "</p>";
								} else {
									var e = r.error;
								}
								var code = 0;
								if (typeof r.code != 'undefined')
									code = r.code;

								if(!_.u.t("Object",e)){
									_.u.te({
										message : e,
										code : code,
										title : '(Server Error)'
									});
								}
					   			break;
					   			
					   		case "redirect":
					   			var u = r[i];
					   			var m = '<strong>Message: </strong>'
									+ r.message
									+ '<br/><br/><strong>Target:</strong> '
									+ u
									+ '<br/><div>Redirecting in <span id="Jax-Counter"></span></div>';
					   			_.ui.d(m,{title:'Server Redirect',modal:true});
					   			var d = 3;
								if (typeof r.delay != 'undefined')
									d = r.delay;

								var myCounter = new _.u.cntr({
									seconds : d,
									onUpdateStatus : function(sec) {
										$("#Jax-Counter").text(sec);
									},
									onCounterEnd : function() {
										location.href = u;
									}
								});

								myCounter.start();
					   			break;
					   			
					   		case "callback":
					   			try {
					   				var code = eval(r[i]);
					   				if(_.u.t("Function",code)){
					   					code();
					   				}
					   			} catch(e){
					   				_.log("Unable to evaluate callback code from server");
					   			}
					   			if(_.u.t("Function",ucb)) ucb.call(ucb.prototype,r);
					   			break;
					   			
					   		case "ajax":
					   			var d = r[i];
					   			_.c.ja(d[0],ucb,d[1],d[2]);
					   			break;

					   }
				   }
				   }
				   
				   if(r==null) ucb.call(ucb.prototype,r);
				   
				   return _;
			   }

		   },
		   
		   // NAVIGATION
		   n:{
			   last:null,
			   has:function(p){
				   if(Moj.nav[p]) return true;
				   return false;
			   },
			   t:function(t){
				   if(!t) return Moj.nav._moj_tgt;
				   if(t.substr(0,1) != "#") t = "#"+t;
				   Moj.nav._moj_tgt = t;
				   return _;
			   },
			   s:function(um){
				   Moj.nav = $.extend(true,{},Moj.nav,um);
				   return _;
			   },
			   a:function(n,f){
				   Moj.nav[n] = f;
				   return _;
			   },
			   go:function(p,d,h){
				   if(!h) _.n.h(p);
				   
				   if(!d) d=Moj.nav[p];
				   if(d == undefined) return _;
				   if(_.u.t("String",d)){
					   var path = d.split("/");
					   var last = path.pop().split("#");
					   
					   path.push(last[0]);
					   
					   target = Moj.nav._moj_tgt;
					   if(last.length > 1) target = "#"+last[1];
					   if(path.length > 1){
						   return _.n._v(path,function(d){
							   $("body").fadeIn();
							   $(target).html(d).show();
						   });
					   } else {
						   path = d.split(".");
						   if(path.length > 1){
							   return _.u.ls(d,true);
						   }
					   }
				   }
				   if(_.u.t("Object",d)){
					   for(var s in d){
						   var fcn = d[s];
						   var path = s.split("/");
						   var last = path.pop().split("#");
						   
						   path.push(last[0]);
						   
						   target = Moj.nav._moj_tgt;
						   if(last.length > 1) target = "#"+last[1];
						   if(path.length > 1){
							   return _.n._v(path,function(html){
								   $("body").fadeIn();
								   $(target).html(html).show();
								   if(_.u.t("Function",fcn))
									   fcn.call(fcn.prototype,html);
							   });
						   } else {
							   path = s.split(".");
							   if(path.length > 1){
								   return _.u.ls(s,true,null,fcn);
							   }
						   }
					   }
				   }
				   if(_.u.t("Function",d)){
					   d.call(d.prototype);
				   }
			   },
			   _v: function(path,cb){
				   if(path.length > 2){
					   var epath="";
					   for(var z=2;z<path.length;z++){
						   epath = epath+path[z]+"/";
					   }
					   _.jp.lv(path[0],path[1],epath,null,cb);
				   } else {
					   _.jp.lv(_.ns,path[0],path[1],null,cb);
				   }
				   return _;
			   },
			   h: function(href){
				   if(_.n.last == href) return;
				   
				   if(history){
					   _.n.last = href;
					   history.pushState('',href,href);
				   }
			   }
		   },
		   
		   // UI
		   ui:{
			   d: function(t,o){	// Dialog
				   if(_.u.t("Function",Moj.udcb.ui_d))
					   new Moj.udcb.ui_d(t,o);
			   },
			   btnC: function(c,t){
				   if(!t) t = "Cancel";
					var b = $("<button>").html(t).addClass('btnClose').click(function(){
						if(_.u.t("Function",c)) c.call(c.prototype,false);
						$(this).closest("div").fadeOut(function(){$(this).remove();});
					});
				
					// pressing esc when alerts/dialogs are shown will remove them.
					$(document).keyup(function(e) {
						 if (e.keyCode == 27) { $(".btnClose").click(); }
					});
					
					return b;
				},
				
				btnV: function(c,t){
					if(!t) t = "Yes";
					var b = $("<button>").html(t).addClass('btnConfirm').click(function(){
						if(_.u.t("Function",c)) c.call(c.prototype,true);
						$(this).closest("div").fadeOut(function(){$(this).remove();});
					});
					
					return b;
				}
		   },
		   
		   g: { 
			   a:function(){
				   $(document).on("click","a",function(){
					var href = $(this).attr('href');					
					var target = $(this).attr('target');
					var dataTarget = $(this).attr("data-moj-target");
					var dataCallback = $(this).attr("data-moj-callback");
					
					if(dataCallback != undefined) dataCallback = eval(dataCallback);
					
					if(!href || !target) return false;
					
					if(href && href.substr(0,1) == '#'){
						return false;
					}
					
					switch(target){
					case "_blank":
						_.u.trig("a._blank",[],this);
						return true;
						break;
						
					case "_moj":
						_.n.go(href);
						return false;
						break;
						
					case "_view":
						var ho = {};
						var hr = href.split("/").pop();
						ho[href] = dataCallback;
						_.n.a(hr,ho);
						_.n.h(hr);
						
						if(!dataTarget) {
							dataTarget = Moj.nav._moj_tgt;
						} else {
							if(dataTarget.substr(0,1) != "#") dataTarget = "#"+dataTarget;
						}
						
						$(dataTarget).load(_.o.b+_.ns+'/'+href,function(d){
							if(_.u.t("Function",dataCallback)) dataCallback.call(dataCallback.prototype,d,dataTarget);
						},'html');
						
						return false;
						break;
						
					case "_json":
						_.c.ja(href,dataCallback);
						return false;
						break;
						
					default:
						return false;
						break;
					}
				});
			   },
			   form: function(e,cb){	// Submit form via ajax
					var sel;
					if(_.u.t("String",e)){
						var fe = e.substr(0,1);
						if(fe != "#" && fe != "."){
							sel = "#"+e;
						} else {
							sel=e;
						}
					} else {
						sel="form";
					}
					
					$(sel).on("submit",function(){
						var t;
						($(this).attr('method'))?t=$(this).attr('method'):t='post';
						
						$.ajax({
							url:$(this).attr('action'),
							dataType:'json',
							type:t,
							data:$(this).serialize()
						})
						.always(function(r){
							_.c.pja(r,cb);
							return false;
						})
						.fail(function(xhr,e,t){
							_.alert('Unable to process your request',Moj.et.ERROR);
							return false;
						});
						return false;
					});
				}
		   },
		   
		   scroll: {
			   top:function(t){
				   if(!t) t=0;
				   $("html, body").animate({ scrollTop: t }, "fast");
			   }
		   },
		   
		   // METHODS
		   extend: function(ns,o){
			   var nsh = ns.split(".");
			   if(!_.u.t("Object",_[nsh[0]])) _[nsh[0]] = {};
			   
			   var co = _[nsh[0]];
			   for(var i=1;i<nsh.length;i++){
				   if(!_.u.t("Object",co[nsh[i]])) co[nsh[i]] = {};
				   co = co[nsh[i]];
			   }
			   //_[ns] = {};
			   for(var p in o){
				   co[p] = o[p];
			   }
			   return _;
		   },
		   log:function(d)
		   {
			   if(_.o.v === true && window.console) console.log(d);
			   return _;
		   },
		   reset:function(){
			   _.jp.r();
			   location.href=_.o.b;
		   },
		   alert:function(d,t)	// Alert (data,type {error, info, warning})
		   {
			   if(_.o.v) _.log(d);
			   _.u.trig("alert",[d,t]);
			   return _;
		   },
		   confirm:function(m,t,f){
			   _.u.trig("confirm",[m,t,f]);
			   return _;
		   },
		   ie:function(){		// Determine ie version based on conditional CSS markup in layout file.
			   _.ie_l = [];
			   $('link[href*="css/ie"]').each(function(){
				   var h = $(this).attr("href");
				   var ha = h.split("/");
				   var ie = ha[ha.length-1].split(".");
				   _.ie_l.push(ie[0]);
			   });
			   return _;
		   },
		   lui:function(v,p,cb){
			   _.jp.lv(_.ns,'UI',v,p,cb);
			   return _;
		   }
   };
   window.Moj = new Moj();
   
   $(document).on("click","[data-role-iedit]",function(){
	   var et = $(this).attr("data-role-iedit");
	   switch(et){
	   	   case "text":
		   if($(this).children("textarea").length < 1)
			   _.u.iedit.call(this,$(this).data("ieditCB"));
		   break;
		   
	   	   case "date":
	   		   if($(this).children("div").length < 1)
				   _.u.iedit.call(this,$(this).data("ieditCB"),"date");
	   		   break;
	   }
   });
   
	// User Lookup
	$(document).on("keyup","#arn-search-users-input",function(){
		var q = $(this).val();
		if(q.length < 3) {
			$("#arn-search-users > .listview").empty();
			return;
		}
		$.get(_.o.b+'Jax/User/search',{'query':q},function(j){
			$("#arn-search-users > .listview").empty();
			if(j && j.response){
				var r = j.response;
				if(r.length == 0){
					$("#arn-search-users > .listview").append("<li>No Results</li>");
				} else {
					for(var u in r){
						$("#arn-search-users > .listview").append("<li id='"+u+"'>"+r[u].Fullname+" ("+u+")"+"</li>");
					}
				}
			} else {
				$("#arn-search-users > .listview").append("<li>No Response</li>");
			}
		},'json');
	});
	
	// this filter code is deprecated. use code block below.
	$(document).off("keyup","#filtermoda");
	$(document).on("keyup","#filtermoda",function(){
		var skip = false;
		
		if($("ul.modfilterlist").attr("data-role-flist") == "alt") skip = true;
		
		if($(this).val().length > 1){
			$("ul.modfilterlist > li").hide();
			
			if(skip){
				$("ul.modfilterlist > li:nth-child(odd):icontains("+$(this).val()+")").show();
			} else {
				$("ul.modfilterlist > li:icontains("+$(this).val()+")").show();
			}
		} else {
			if(skip){
				$("ul.modfilterlist > li:nth-child(even)").hide();
				$("ul.modfilterlist > li:nth-child(odd)").show();
			} else {
				$("ul.modfilterlist > li").show();
			}
		}
	});
	
	var filtertaglist = {
			"ul":"li",
			"table":"tbody > tr"
		};
	//updated filter function. Should replace above code.
	$(document).off("keyup",".moj-filter-value");
	$(document).on("keyup",".moj-filter-value",function(){
		var ifilter = $(this);
		var instance = $(this).attr('data-name');
		var skip = false;
		
		$("[data-name='"+instance+"']").each(function(){
			var list = $(this);
			var tag;
		
			var detected = list.get(0).nodeName.toLowerCase();
			for(var t in filtertaglist){
				if(detected == t){
					tag = filtertaglist[t];
				}				
			}
			
			if(list.attr("data-moj-filter") == "alt") skip = true;
			
			if(ifilter.val().length > 1){
				list.find(tag).hide();
				
				if(skip){
					list.find(tag+":nth-child(odd):icontains("+ifilter.val()+")").show();
				} else {
					list.find(tag+":icontains("+ifilter.val()+")").show();
				}
			} else {
				if(skip){
					list.find(tag+":nth-child(even)").hide();
					list.find(tag+":nth-child(odd)").show();
				} else {
					list.find(tag).show();
				}
			}
		});
	});	
	
	for(var t in filtertaglist){
		var tag = filtertaglist[t];
		
		$(document).off("click",t+"[data-moj-filter='alt'] > "+tag+":nth-child(odd)");
		$(document).on("click",t+"[data-moj-filter='alt'] > "+tag+":nth-child(odd)",function(e,i){
			var cbfcn = $(this).parent(t).data("moj-filter-callback");
			
			$(t+"[data-moj-filter='alt'] > "+tag+":nth-child(even)").each(function(){
				if(!$(this).is(":hidden")){
					$(this).slideUp(200);
				}
			});
			
			var tgt = $(this).next();
			if(tgt.is(":hidden")){
				tgt.slideDown(200,function(){
					if(_.u.t("Function",cbfcn)){
						cbfcn(tgt);
					}
				});
			}
		});
	}
	
	$(document).off("click",".btnfiltertoggles");
	$(document).on("click",".btnfiltertoggles",function(){
		$(this).parent('h3').next(".divfiltertoggles").fadeToggle('fast');
	});
   
}(window,undefined,$));

function ucwords (str) {
	  // http://kevin.vanzonneveld.net
	  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	  // +   improved by: Waldo Malqui Silva
	  // +   bugfixed by: Onno Marsman
	  // +   improved by: Robin
	  // +      input by: James (http://www.james-bell.co.uk/)
	  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
  return $1.toUpperCase();
});
}