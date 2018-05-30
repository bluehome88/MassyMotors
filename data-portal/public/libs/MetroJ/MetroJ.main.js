$(document).ready(function(){
	if(Moj){
		Moj
		.opts({b:baseUrl()})
		.init()
		.jp.lapp();
	} else {
		alert('MetroJ not loaded!');
	}
});