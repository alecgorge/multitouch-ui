$.fn.fullHeight = function () {
	return this.each(function () {
		$(this).css('height', (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight)-($(this).outerHeight(true)-$(this).height())+'px');
	});
};
$.fn.fullWidth = function () {
	return this.each(function () {
		$(this).css('width', (typeof window.innerWidth != 'undefined' ? window.innerWidth : document.body.offsetWidth)-($(this).outerWidth(true)-$(this).width())+'px');
	});
};
$.fn.stretch = function () {
	return this.each(function () {
		$(this).css('height', (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight)-$(this).offset().top+'px');
	});
};

/* Copyright 2011, Ben Lin (http://dreamerslab.com/)
* Licensed under the MIT License (LICENSE.txt).
*
* Version: 1.0.0
*
* Requires: jQuery 1.2.6+
*/
$.fn.center=function(a){var b=$(window),c=b.scrollTop();return this.each(function(){var f=$(this),e=$.extend({against:"window",top:false,topPercentage:0.5},a),d=function(){var h,g,i;if(e.against==="window"){h=b;}else{if(e.against==="parent"){h=f.parent();c=0;}else{h=f.parents(against);c=0;}}g=((h.width())-(f.outerWidth()))*0.5;i=((h.height())-(f.outerHeight()))*e.topPercentage+c;if(e.top){i=e.top+c;}f.css({left:g,top:i});};d();b.resize(d);});};
