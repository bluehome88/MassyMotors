// JavaScript Document
$ult = jQuery.noConflict();

//	Enable chosen
$ult('.ultimate-border-style-selector').chosen({
    allow_single_deselect: true,
    width: "100%"
});

$ult(".ultimate-border").each(function(index, element) {
	var t = $ult(element);
    get_hidden_with_border_style(t);
    set_hidden_with_border_style(t);
});

function get_hidden_with_border_style(t) {
    var l = t.find(".ultimate-border-style-selector").length;
    var mv = t.find(".ultimate-border-value").val();

    if (mv != "") {
      if(l) {
        var vals = mv.split("|");
          // set border style 
          var splitval = vals[0].split(":");
          var bstyle = splitval[1].split(";");
          t.find(".ultimate-border-style-selector").val(bstyle[0]);
          t.find(".ultimate-border-style-selector").trigger("chosen:updated");

          //  set border widths
          var bw = vals[1].split(";");
      }

      $ult.each(bw, function(i, vl) {
          if (vl != "") {
              t.find(".ultimate-border-inputs").each(function(input_index, elem) {
                var splitval = vl.split(":");
                var dataid = $ult(elem).attr("data-id");
                
                if( dataid==splitval[0] ) {
                  var unit = $ult(elem).attr("data-unit");
                  mval = splitval[1].split(unit);
                  $ult(elem).val(mval[0]);
                }
             });
          }
      });

      // set color 
      var splitcols = mv.split("|");
      if(typeof splitcols[2] != 'undefined' || splitcols[2] != null){
        var sp = splitcols[2].split(":");
        var nd = sp[1].split(";");
        var did = t.find(".ultimate-colorpicker").attr("data-id");
        if(sp[0]==did) {
          t.find(".ultimate-colorpicker").val(nd[0]);
          t.find("a.wp-color-result").css({"background-color": nd[0]});
        }
      }
    } else {
      t.find(".ultimate-border-inputs").each(function(input_index, elem) {
        var d = $ult(elem).attr("data-default");
        $ult(elem).val(d);
      });
    }
}





/*
 *		Set Values
 *
 *	Set hidden field values
 */
// [1]	color
var options = {
	change: function(event, ui){
		var hxcolor = $ult( this ).wpColorPicker( 'color' );
		var t = $ult(this).closest('.ultimate-border');
		set_hidden_with_border_style(t, hxcolor);
    },
    clear: function() {
    	var hxcolor = $ult( this ).wpColorPicker( 'color' );
      	var t = $ult(this).closest('.ultimate-border');
		set_hidden_with_border_style(t, hxcolor);
    },
};
$ult(".ultimate-colorpicker").wpColorPicker(options);


// [2]	On change - input / select
$ult(".ultimate-border-input, .ultimate-border-style-selector, .ultimate-colorpicker").on('change', function(e){
	var t = $ult(this).closest('.ultimate-border');
	/*var pid = $ult("#" +t.attr('id')+"");
	var t = $ult(pid);*/

	var v = t.find('.ultimate-border-value').val();
	//alert(v);
	set_hidden_with_border_style(t);

	/*alert("Parent Class: "+t.attr('class'));
	alert("Parent ID: "+t.attr('id'));*/

});

function set_hidden_with_border_style(t, hxcolor) {
  var nval = "";
  var l = t.find(".ultimate-border-style-selector").length;
  //  check border style is avai. then add border style
  if(l) {
    var sv = t.find(".ultimate-border-style-selector option:selected").val();
    t.find(".ultimate-border-value").val(nval);
    var nval = "border-style:" +sv+ ";|";
  } 
  
  //  add all border widths, margins, paddings 
  t.find(".ultimate-border-inputs").each(function(index, elm) {
    var unit = t.find(".ultimate-border-value").attr("data-unit");

    var ival = $ult(elm).val();
    if ($ult.isNumeric(ival)) {
        if (ival.match(/^[0-9]+$/))
            var item = $ult(elm).attr("data-id") + ":" + $ult(elm).val() + unit + ";";
        nval += item;
    }
  });

  /*t.find(".ultimate-colorpicker-block .iris-square-inner").click(function(){
  	alert("KOK");
  });*/
  //  set border color 
  //var va = t.find("a.wp-color-result").css("background-color");
  //alert("va: "+va);
  //alert("hexcolor: "+hexcolor);
  

  //var va = t.find(".ultimate-colorpicker").val();
  //alert(va);  
  if(typeof hxcolor != "undefined" || hxcolor != null) {
    var nval = nval + "|border-color:" +hxcolor+ ";";
  } else {
    var va = t.find(".ultimate-colorpicker").val();
    if(va!='') {
      var nval = nval + "|border-color:" +va+ ";";
    }
  }
  t.find(".ultimate-border-value").val(nval);
}