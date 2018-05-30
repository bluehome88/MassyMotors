/**
 * *********************************************************************************************
 * jsChrome jQuery UI Plugin - jQuery Style Javascript Windows (Chromes)
 * *********************************************************************************************
 * Copyright (c) 2011 Ricardo Assing
 * http://www.ra13.com, http://www.ricardoassing.com
 * ricardoassing@ra13.com
 *
 * Project Homepage: https://github.com/icardo/JSCHROME-JQUERY-UI-PLUGIN
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
(function( $ ){
		  
	$.widget("ui.jsChrome", {

		options:{
			width: "250px",
			height: "250px",
			top: "120px",
			left: "130px",
			title: "New jsChrome",
			titleBarIcon: "ui-icon-newwin",
			html:null,
			url:null,
			closeable: "drop",
			collapsible: true,
			collapsibleWidth: "150px",
			nested: false,
			overlay: false,
			disableAll: true,
			css:null,
			persist:false,

			draggable: {
			},
			hideDragContent: true,

			resizable: {
				containment:"body",
				autoHide: true,
				minWidth: 250,
				minHeight: 250
			},

			load: {
				url: null,
				data: null,
				complete: null
			}
		},
		
		_init: function() {
			this._trigger("init");
		},

		_create: function() {
			
			/**
			* Support for Metadata plugin
			* Metadata - jQuery plugin for parsing metadata from elements
			* http://plugins.jquery.com/project/metadata
			*/
			this.options = $.metadata ? $.extend({},this.options,this.element.metadata()): this.options;
			//
			this.element.data("disabled",0);
			
			// Grab and reset existing element contents.
			this.elementContents = this.element.html();
			this.element.html("");
			
			// Grab or set element id
			if(typeof this.element.attr("id") != "undefined") {
				this.id = this.element.attr("id");
			} else {
				this.id = $.fn.jsChrome.generateChromeId();
				this.element.attr("id",this.id);
			}
			
			// Set chrome title as element title attribute (if found)
			if(this.element.attr("title")){ this.options.title = this.element.attr("title"); }
						
			// Break nested chromes
			if(this.options.nested == false) $('body').append(this.element[0]);
			
			// Set jsChrome styles
			this.element
				.css({padding:"3px",position:"absolute"})
				.addClass("ui-widget ui-corner-all ui-jsChrome-chrome");
				
			// Set/Retrieve chrome size and position (uses $.cookie if available)			
			this._setPosition(
				{
					top:this.options.top,
					left:this.options.left,
					width:this.options.width,
					height:this.options.height
				},true
			);
			
			// Create and set jsChrome controls
			this._setChromeControls();

			// Create and set jsChrome titlebar
			this._setChromeTitleBar();
			
			// Create and set jsChrome body
			if(this.options.url != null){
				this._setChromeiFrame();
			} else {
				this._setChromeBody();	
			}

			
			// Make jsChrome draggable (if option is set)
			if (this.options.draggable != false) {
				this._draggable();
			}
			
			// Make jsChrome resizable (if option is set)
			if (this.options.resizable != false) {
				this._resizable();
			}
			
			// Event bindings
			this.element.bind("resize",$.proxy(this._chromeResizer,this));
			this.element.bind("click",$.proxy(this._focusWindow,this));
			
			//this._trigger("create",event,this.element);

			if (this.options.load.url != null) this.load();
		},
		
		_setChromeControls: function() {
			this.jsChromeControls = $("<div>").addClass("ui-state-default ui-jsChrome-controls ui-corner-bl ui-corner-tr")
												.css({position:"absolute",right:0,top:0});
			
			// Set Close Button, closeable option
			if (this.options.closeable != false){
				this.chromeClose = $('<span style="border:1px;"><span title="Close" class="ui-icon ui-icon-circle-close" style="float: left;margin: .3em;"></span></span>');
				this._hoverable(this.chromeClose);
				this._closeable(this.chromeClose,this.options.closeable);
				
				this.jsChromeControls.prepend(this.chromeClose);
			}
			
			// Set expander/toggler buttons (collapsible option)
			if (this.options.collapsible != false){
				this.chromeMin = $('<span style="border:1px;"><span title="Toggler" class="ui-icon ui-icon-circle-minus" style="float: left;margin: .3em;"></span></span>');
				
				this.chromeMax = $('<span style="border:1px;"><span title="Expander" class="ui-icon ui-icon-circle-plus" style="float: left;margin: .3em;"></span></span>');
				
				this._hoverable(this.chromeMin);
				this._hoverable(this.chromeMax);
				this._expander(this.chromeMax);
				this._toggler(this.chromeMin);
				$(this.chromeMin,this.chromeMax,this.chromeClose).bind("click",function(){return false;});
				
				this.jsChromeControls.append(this.chromeMin);
				
				// No expander for nested windows
				if(typeof this.element.parents('.ui-jsChrome-chrome').get(0) == "undefined") this.jsChromeControls.append(this.chromeMax);
			}
			
			this.element.prepend(this.jsChromeControls);
		},
		
		_setChromeTitleBar: function() {
			this.chromeTitlebar = $("<div>").addClass("ui-widget-header ui-corner-top ui-jsChrome-chrome-header").css({height:"30px"});
			this._hoverable(this.chromeTitlebar)
			this.element.append(this.chromeTitlebar);
			
			var chromeTitle = $('<div class="ui-jsChrome-titlebar">').css({
				lineHeight:this.chromeTitlebar.css("height"),
				marginLeft:(this.chromeTitlebar.height())+'px',
				height:this.chromeTitlebar.css("height"),
				overflow:"hidden"
			}).html(this.options.title)
			.prepend($('<span>').addClass("ui-icon "+this.options.titleBarIcon).css({position:"absolute",left:(this.chromeTitlebar.height()/3),top:(this.chromeTitlebar.height()/3)}));
			
			this.chromeTitlebar.append(chromeTitle).attr("title",this.options.title);
			this.chromeTitlebar.bind("click",$.proxy(function(event){this._trigger("select",event,this.element);},this));
		},
		
		_setChromeBody: function() {
			if(this.chromeBody){
				if(this.chromeBody.is(':hidden')) return false;
				
				this.chromeBody.detach();
			}
			this.chromeBody = $("<div>");

			if(this.options.html != null) {
				this.chromeBody.append(this.options.html);
			} else {
				this.chromeBody.append(this.elementContents);	
			}
			this._styleChromeBody();
		},
		
		_setChromeiFrame: function() {
			if(this.chromeBody){
				if(this.chromeBody.is(':hidden')) return false;
				
				this.chromeBody.detach();
			}
			this.chromeBody = $('<iframe frameborder="0" scrolling="auto" src="'+this.options.url+'">');
			this._styleChromeBody();
		},
		
		_styleChromeBody: function() {
			var o = {
					borderTop:"none",
					overflow:"auto",
					padding:"3px"
				};
			var css = $.extend(true,o,this.options.css);
			this.element.append(this.chromeBody);
			this.chromeBody
			.addClass("ui-widget-content ui-jsChrome-chrome-body ui-corner-bottom")
			.css(css);
			
			this.chromeBody.css({height:
						this.element.height()-this.chromeTitlebar.height()-parseInt(this.element.css("paddingTop"))
						-parseInt(this.chromeTitlebar.css("paddingTop"))
						-parseInt(this.chromeTitlebar.css("paddingBottom"))
						-parseInt(this.chromeBody.css("paddingTop"))
						-parseInt(this.chromeBody.css("paddingBottom")),
						width:
						this.element.width()-parseInt(this.element.css("paddingLeft"))
						-parseInt(this.element.css("paddingRight"))
						-parseInt(this.element.css("borderLeftWidth"))
						-parseInt(this.element.css("borderRightWidth")),
						position:'relative'
						});
		},
		
		_draggable: function() {
			if(this.options.draggable == false) return false;
			this.options.customDragStop = this.options.draggable.stop;
			this.options.customDragStart = this.options.draggable.start;
			
			draggableOptions = $.extend({},this.options.draggable,{
				distance:0,
				delay:0,
				iframeFix:true,
				scroll: false,
				stack:".ui-jsChrome-chrome",
				handle:".ui-jsChrome-chrome-header",
				start:$.proxy(function(event,ui) {
									   
					if(this.options.overlay === true)
						$("body").append($('<div id="body_overlay">').addClass('ui-widget-overlay'));
					
					if(this.options.disableAll === true)
						$(":ui-jsChrome").each(
							$.proxy(function(index,element){
									if($(element).data("disabled") == 1){
										$(element).data("temp_disable",0);
									} else {
										if($(this).attr('id') != $(element).attr('id')){
											$(element).data("temp_disable",1);
											$(element).jsChrome("disable");
										}
									}
							},this));
					
					this.element.toggleClass('ui-widget-shadow');
					this.element.children(".ui-jsChrome-chrome-header").addClass("ui-state-active");
					
					if(this.options.hideDragContent === true){
						if(this.element.children(".ui-jsChrome-chrome-body").is(':visible')){
							this.restoreOnDS = true;
						} else {
							this.restoreOnDS = false;
						}
						this.element.children(".ui-jsChrome-chrome-body").hide();
					}
					
					if(typeof this.options.customDragStart == "function") $.proxy(this.options.customDragStart,this.element)(event,ui);
				},this),
				stop:$.proxy(function(event,ui) {
									  
					if(this.options.overlay === true)
						$("#body_overlay").remove();
					
					if(this.options.disableAll === true)
						$(":ui-jsChrome").each(
							$.proxy(function(index,element){
									if ($(element).data("temp_disable") == 1){
										if($(this).attr('id') != $(element).attr('id'))
											$(element).jsChrome("enable");
									}
							},this));
					
					if(this.options.hideDragContent === true){
						if(this.restoreOnDS === true)
							this.element.children(".ui-jsChrome-chrome-body").show();
					}
						
					this.element.children(".ui-jsChrome-chrome-header").removeClass("ui-state-active");
					wtop = parseInt(this.element.css("top"));
					wleft = parseInt(this.element.css("left"));
					wwidth = this.element.width();
					wheight = this.element.height();
					
					if(wtop < 0) this.element.css("top","10px");
					if(wleft < 0) this.element.css("left","10px");
					
					dwidth = $(window).width();
					dheight = $(window).height();
					
					if(wtop+25 > dheight) this.element.css("top",dheight-wheight);
					if(wleft+25 > dwidth) this.element.css("left",dwidth-wwidth);
					
					this.element.toggleClass('ui-widget-shadow');
					
					this._setPosition({top:this.element.css("top"),left:this.element.css("left")});
					
					if(typeof this.options.customDragStop == "function") $.proxy(this.options.customDragStop,this.element)(event,ui);
				},this)
			});
			this.options.draggable = draggableOptions;
			this.element.draggable(draggableOptions);
		},
		
		_resizable: function () {
			if(this.options.resizable == false) return false;
			this.options.customResizeStop = this.options.resizable.stop;
			
			resizeOptions = $.extend({},this.options.resizable,{
				animate: false,
				helper: 'ui-resizable-helper',
				distance:0,
				delay:0,
				ghost:false,
				stop:$.proxy(function(event,ui){
					chromeBody = this.element.children(".ui-jsChrome-chrome-body");
					chromeHeader = this.element.children(".ui-jsChrome-chrome-header");
					chromeBody.css({
						height:this.element.height()
						-parseInt(this.element.css("paddingTop"))-parseInt(chromeHeader.height())
						-parseInt(chromeHeader.css("paddingTop"))
						-parseInt(chromeHeader.css("paddingBottom"))
						-parseInt(chromeBody.css("paddingTop"))
						-parseInt(chromeBody.css("paddingBottom")),
						width:
						this.element.width()-parseInt(this.element.css("paddingLeft"))
						-parseInt(this.element.css("paddingRight"))
						-parseInt(this.element.css("borderLeftWidth"))
						-parseInt(this.element.css("borderRightWidth"))
					});
					this._setPosition({width:this.element.css("width"),height:this.element.css("height")});
					
					if(typeof this.options.customResizeStop == "function") $.proxy(this.options.customResizeStop,this.element)(event,ui);
				},this)
			});
			this.options.resizable = resizeOptions;
			this.element.resizable(resizeOptions);
		},
		
		_chromeResizer: function() {
			chromeBody = this.element.children(".ui-jsChrome-chrome-body");
			chromeHeader = this.element.children(".ui-jsChrome-chrome-header");
			nheight = this.element.height()
							-parseInt(this.element.css("paddingTop"))-parseInt(chromeHeader.height())
							-parseInt(chromeHeader.css("paddingTop"))
							-parseInt(chromeHeader.css("paddingBottom"))
							-parseInt(chromeBody.css("paddingTop"))
							-parseInt(chromeBody.css("paddingBottom"));
			nwidth = this.element.width()-parseInt(this.element.css("paddingLeft"))
						-parseInt(this.element.css("paddingRight"))
						-parseInt(this.element.css("borderLeftWidth"))
						-parseInt(this.element.css("borderRightWidth"))
			chromeBody.css({height:nheight,width:nwidth});
			this._trigger("resizing",null,{width:this.element.width(),height:this.element.height()});
		},
		
		_setPosition: function(cssMap,stateful) {
			var ckey = 'curPos';
			var pkey = 'prePos';
			var prevCid = this.id+'_prev';
			
			if(stateful === true){
				cCookie = this._getCookie(this.id);
				if(cCookie !== null) cssMap = this._mapmaker(cCookie);
				
				dwidth = $(window).width();
				dheight = $(window).height();
				if(parseInt(cssMap.width)+parseInt(cssMap.left) > dwidth) cssMap.left=dwidth-parseInt(cssMap.left);
				if(parseInt(cssMap.height)+parseInt(cssMap.top) > dheight) cssMap.top=dheight-parseInt(cssMap.top);
			}
			
			cMap = this.element.data(ckey);
			pMap = this.element.data(pkey);
			
			if(typeof pMap == 'undefined') var pMap = {};
			
			if(typeof cMap !== 'undefined') {
				upMap = this._updateMap(pMap,cMap);
				this.element.data(pkey,upMap);
				this._setCookie(prevCid,this._stringmaker(upMap),365,'/');
			} else {
				pCookie = this._getCookie(prevCid);
				if(pCookie !== null) this.element.data(pkey,this._mapmaker(pCookie));
			}

			if(typeof cMap == 'undefined') var cMap = {};
			ucMap = this._updateMap(cMap,cssMap);
			this.element.data(ckey,ucMap);
			this._setCookie(this.id,this._stringmaker(ucMap),365,'/');

			this.element.css({width:cssMap.width,height:cssMap.height});
			if(cssMap.top && cssMap.left) this.element.css({top:cssMap.top,left:cssMap.left});
			this._chromeResizer();
		},
		
		_revertPosition: function(key) {
			
			if(typeof key != 'undefined'){
				var pkey = key;
			} else {
				var pkey = 'prePos';
			}
			
			if(this.chromeBody.is(':hidden')) this.chromeMin.click();
			
			if(typeof this.element.data(pkey) !== 'undefined') {
				pMap = this.element.data(pkey);
				this._setPosition($.extend({},pMap));
			}
		},
		
		_setCookie: function(name,data,expires,path) {
			if(this.options.persist === true)
				if($.cookie) $.cookie(name,data,{expires:expires,path:path});
		},
		
		_getCookie: function(name) {
			if(this.options.persist === true)
				if($.cookie) return $.cookie(name);
			return null;
		},
		
		_updateMap: function(cur,updates) {
			for (var i in updates) {
				cur[i] = updates[i];
			}
			
			return cur;
		},
		
		_focusWindow: function(){
			$(".ui-jsChrome-chrome").css("zIndex",0);
			this.element.css("zIndex",1);
			
			this._trigger("click",null,this.element);
		},
		
		_hoverable: function(element) {
			element
				.mouseenter(function(){$(this).addClass('ui-state-hover');})
				.mouseleave(function(){$(this).removeClass('ui-state-hover');});
		},
		
		_toggler: function(el) {
			el.click($.proxy(function() {
				jsChrome = this.element.closest(".ui-jsChrome-chrome");
				jsChromeBody = jsChrome.children(".ui-jsChrome-chrome-body");
				jsChromeHeader = jsChrome.children(".ui-jsChrome-chrome-header");
				
				if(jsChromeBody.css("display") == "none") {
					cmap = this.element.data('curPos');
					jsChrome.animate({height:cmap.height,width:cmap.width},{
						complete:$.proxy(function(){
							if(this.options.resizable != false)
								jsChrome.resizable(this.options.resizable);
								
							this._trigger("show",null,{width:jsChrome.width(),height:jsChrome.height(),top:parseInt(jsChrome.css("top")),left:parseInt(jsChrome.css("left"))});
						},this)
					});
					jsChromeBody.show('fade');
					jsChromeHeader.removeClass("ui-corner-all");
					
				} else {
					jsChromeBody.hide("fade");
					jsChrome.animate({height:jsChromeHeader.height()+parseInt(jsChrome.css("paddingBottom")),width:this.options.collapsibleWidth},{
						complete:$.proxy(function(){
							
							if(this.options.resizable != false)
								jsChrome.resizable("destroy");
							
							jsChromeHeader.addClass("ui-corner-all");
							this._trigger("hide",null,this.element);
						},this)
					});
				}
				return false;
			},this));
		},
		
		_expander: function(el) {
			el.click($.proxy(function() { 
				c = this;
				jsChrome = this.element.closest(".ui-jsChrome-chrome");
				jsChromeBody = jsChrome.children(".ui-jsChrome-chrome-body");
				jsChromeHeader = jsChrome.children(".ui-jsChrome-chrome-header");
			
				if(jsChromeBody.css("display") == "none"){
					jsChromeBody.show('fade');
					
					if (this.options.resizable != false)
						jsChrome.resizable(this.options.resizable);
					
					jsChromeHeader.removeClass("ui-corner-all");
				}
				
				dwidth = $(window).width();
				dheight = $(window).height();
				nwidth = dwidth-150;
				nheight = dheight-150;
				cmap = this.element.data('curPos');
				
				if(nwidth == parseInt(cmap.width) && nheight == parseInt(cmap.height)){
					this._revertPosition();
					
					setTimeout('c._trigger("revert",null,{width:jsChrome.width(),height:jsChrome.height(),top:parseInt(jsChrome.css("top")),left:parseInt(jsChrome.css("left"))});',1000);
					
				} else {
					this._setPosition({width:nwidth+'px',height:nheight+'px',top:"75px",left:"75px"});
					setTimeout('c._trigger("expand",null,{width:jsChrome.width(),height:jsChrome.height(),top:parseInt(jsChrome.css("top")),left:parseInt(jsChrome.css("left"))});',1000);
				}
				
				jsChrome.resize();
				
				return false;
			},this));
		},
		
		_closeable: function(el,effect) {
			el.click($.proxy(function(){
					this.element.hide(effect,$.proxy(function(){this.element.detach();},this));
					this._trigger("close");
					return false;
			},this));
		},

		_mapmaker: function(str){
			if(typeof str != "string") return;
				cssM = {};
				cssArray = str.split(';');
					for(z=0;z<cssArray.length;z++)
					{
						prop = cssArray[z];
						propA = prop.split(':');
						cssM[propA[0]] = propA[1];
					}
				return cssM;
		},
		
		_windowType: function() {
			return this.chromeBody.get(0).tagName.toLowerCase();
		},

		_stringmaker:function(map){
			str = '';
			for(var i in map) str += i+':'+map[i]+';';
			return str;
		},
		
		_setOption: function(option,value) {
			$.Widget.prototype._setOption.apply( this, arguments );
			
			switch(option){
				case "html":
					this._setChromeBody();
				break;
				
				case "width":
					if(this.chromeBody.is(':hidden')) return;
					this.element.css({width:parseInt(value)});
					this._chromeResizer();
					this._setPosition({width:parseInt(value)+'px'});
				break;
				
				case "height":
					if(this.chromeBody.is(':hidden')) return;
					this.element.css({height:parseInt(value)});
					this._chromeResizer();
					this._setPosition({height:parseInt(value)+'px'});
				break;
				
				case "top":
					this.element.animate({top:parseInt(value)});
					this._setPosition({top:parseInt(value)+'px'});
				break;
				
				case "left":
					this.element.animate({left:parseInt(value)});
					this._setPosition({left:parseInt(value)+'px'});
				break;
				
				case "title":
					icon = $(this.element.find(".ui-jsChrome-titlebar").get(0)).children('span');
					$(this.element.find(".ui-jsChrome-titlebar").get(0)).html(value).prepend(icon).closest('.ui-jsChrome-chrome-header').attr("title",value);
				break;
				
				case "titleBarIcon":
					$(this.element.find(".ui-jsChrome-titlebar").get(0)).children('span').removeClass().addClass('ui-icon '+value);
				break;
				
				case "resizable":
					if(this.options.resizable == false){
						this.element.resizable("destroy");	
					} else {
						this._resizable();
					}
				break;
				
				case "draggable":
					if(this.options.draggable == false){
						this.element.draggable("destroy");
					} else {
						this._draggable();
					}
				break;
				
				case "url":
					this._setChromeiFrame();
				break;
			}
		},
		
		disable: function() {
			if(this.element.data("disabled") == 0){
				this.element.data("disabled",1);
				//this.element.addClass('ui-widget-shadow');
				if(this.element.children(".ui-jsChrome-chrome-body").is(':visible')){
					this.restoreOnEnable = true;
					this.element.children(".ui-jsChrome-chrome-body").hide();
				} else {
					this.restoreOnEnable = false;
				}
				this.element.children(".ui-jsChrome-controls").hide();
				this.element.draggable("destroy");
				this.element.resizable("destroy");
			}
		},
		
		enable: function() {
			if(this.element.data("disabled") == 1){
				this.element.data("disabled",0);
				this.element.removeClass('ui-widget-shadow');
				if(this.restoreOnEnable === true){
					this.element.children(".ui-jsChrome-chrome-body").show();
					if(typeof this.options.resizable == 'object') this.element.resizable(this.options.resizable);
				}
				this.element.children(".ui-jsChrome-controls").show();
				if(typeof this.options.draggable == 'object') this.element.draggable(this.options.draggable);
			}
		},
		
		// Public functions
		append: function(data) {
			if(this._windowType() == 'div')
				if(typeof data != "undefined") this.element.children('.ui-jsChrome-chrome-body').append(data);
		},
		
		setCss: function(css){
			if(this._windowType() == 'div')
				if(typeof data != "undefined") this.element.children('.ui-jsChrome-chrome-body').css(css);
		},
		
		prepend: function(data) {
			if(this._windowType() == 'div')
				if(typeof data != "undefined") this.element.children('.ui-jsChrome-chrome-body').prepend(data);
		},
		
		html: function(data) {
			if(typeof data != "undefined"){
				this._setChromeBody();
				this.element.children('.ui-jsChrome-chrome-body').html(data);
			}
		},
		
		restore: function()
		{
			this.enable();
			this._revertPosition('curPos');
		},
		
		minimize: function()
		{
			this.chromeMin.click();
		},
		
		load: function(urlMap) {
			if(typeof urlMap != 'undefined'){
				this.options.load.url = urlMap.url;
				this.options.load.data = urlMap.data;
				this.options.load.complete = urlMap.complete;
			} 
			if(this.options.load.url != null && typeof this.options.load.url == 'string'){
				
				this._setChromeBody();
				this.element.children(".ui-jsChrome-chrome-body")
					.load(this.options.load.url,
						  this.options.load.data,
						  $.proxy(function(responseText,textStatus,xhr){
							  if(textStatus != 'success')
							  		$('#'+this.id).jsChrome('html','<div class="ui-state-error">'+responseText+'</div>');
							  if(typeof this.options.load.complete == "function") $.proxy(this.options.load.complete,this)(responseText,textStatus,xhr);
							  this._trigger('loaded',null,{responseText:responseText,textStatus:textStatus,xhr:xhr});
						  },this)
					);
			}
		},
		
		destroy: function() {
			 $.Widget.prototype.destroy.apply(this, arguments);
			 this.element.detach();
			 this._trigger("destroy");
		}
			 
	});
	
	$.fn.jsChrome.generateChromeId = function() {
		var date = new Date();
		return "jsChrome_"+date.getTime();
	};
	
})( jQuery );