$.templates = {
	/*"randomAlumLine" : '<li><a href="#" rel="id:%s" class="search-link">%s %s %s <span>%s</span></a></li>',
	"gradYearLine": '<li><a href="#" rel="year:%s" class="search-link">%s</a></li>',
	"resultGroup" : '<ul class="result-group list large-list">%s</ul>',
	"person" : '<li class="result result-person"><a href="#" rel="id:%s"><b class="thumb-person"><img src="%s" /></b><b class="name">%s &#39;%s</b><br class="clear"/></a></li>',
	"details" : '<div class="details-person person-%s" rel="id:%s"><div class="gutter"><div class="image-person"><img src="%s" width="256" /></div><div class="data"><h3>%s &#39;%s</h3><p>Extra Information Goes Here, man</p></div></div><br class="clear"/></div>',
	"historyLink" : '<a href="#" rel="%s" class="history-link search-link">%s</a>',*/
	"class":'<li class="class class-%s"><div class="title-bar gold-gradient"><span class="gold-gradient">%s</span></div>%s</li>',
	"grid":'<ul class="alumni grid">%s</ul>',
	"search-result":'<li class="search-result"><a href="javascript:;" data-alum-id="%s" class="detail-link maroon-gradient"><b class="thumb-person"><img src="%s" /></b><b class="name">%s \'%s</b><span class="clear"></span></a></ul>',
	"classbox":'<li><a href="javascript:;" data-class="%s" class="class-jump-link">%s</a></li>',
	"grid-alumn":'<td><a href="javascript:;" data-alum-id="%s" class="detail-link"><b class="thumb-person"><img src="%s" /></b><b class="name">%s</b></a></td>',
	render : function (k,vs) {
		vs.unshift($.templates[k]);
		return sprintf.apply(window, vs);
	}
};
