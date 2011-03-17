var trigger_s = {};
var scrollers = [
	"sidebar",
	"inner-content",
	"lsidebar"
];
var scroll_obj = {};
function bind (k,v) {
	if(typeof(trigger_s[k]) == undefined) {
		trigger_s[k] = [];
	}
	trigger_s[k].push(v);
}

function trigger (k,args, that) {
	if(typeof(trigger_s[k]) != undefined) {
		_.each(trigger_s[k], function () {
			this.apply(that, args);
		});
	}
}

// rebinds iScroll. should be called after ajax
function rebuildScrollers (v) {
	if(typeof v == "string") {
		if(typeof scroll_obj[v] == "object") {
			scroll_obj[v].destroy();
		}
		scroll_obj[v] = new iScroll(v);
		return;
	}
	_.each(scrollers, function (v) {
		if(typeof scroll_obj[v] == "object") {
			scroll_obj[v].destroy();
		}
		scroll_obj[v] = new iScroll(v);
	});
}
function setHistory(name, search) {
	if(typeof(search) != "string") {
		$('#history-bar > .gutter').html('');
		return;
	}
	$("#history-bar > .gutter").html($.templates.render("historyLink", [
		search,
		name.toString()
	]));
}
	

	
function displayFromDatasource(ds) {
	var lis = "";
	// console.log("ds",ds);
	if(ds.length == 1) {
		var v = ds[0];
		var fullname = v.firstname + ' ' + (v.nickname != "NULL" && v.nickname != v.firstname ? '"' + v.nickname + '"' : "") + " " + middle_initial(v.middlename) + " " + v.lastname;
		$('#ajax-content').html($.templates.render("details", [v.alum_id, v.alum_id, "bigbatman.jpg", fullname, v["class"].toString().substr(2)]));
		rebuildScrollers('inner-content');
		return;
	}
	_.each(ds, function (v,k) {
		var fullname = v.firstname + ' ' + (v.nickname != "NULL" && v.nickname != v.firstname ? '"' + v.nickname + '"' : "") + " " + middle_initial(v.middlename) + " " + v.lastname;
		lis += $.templates.render("person", [v.alum_id, "batman.jpg", fullname,v["class"].toString().substr(2)]);
	});
	$('#ajax-content').html($.templates.render("resultGroup", [lis]));
	rebuildScrollers('inner-content');
}

function middle_initial (x) {
	return x == "NULL" ? "" : x.substr(0,1)+".";
}
var generateRandomAlums = function () {
	// random ppl
	var $ra = $('#random-alums');
	$ra.fadeOut(function () {
		$.db.query("SELECT * FROM alumni ORDER BY RANDOM() LIMIT 10", function (rows) {
			$ra.html('');				
			_.each(rows, function (v, k) {
				$ra.append($.templates.render("randomAlumLine", [v.alum_id, v.firstname, middle_initial(v.middlename), v.lastname, v["class"]]));
			});
			$ra.fadeIn(function () {
				rebuildScrollers("lsidebar");
			});
		}, $.db.error);
	});
};

// returns a "Datasource"
function filter(query) {
	var q;
	if(typeof(query) == "string") {
		q = parseQuery(query);
	}
	else {
		q = query;
	}
	
	// console.log(q);
	if(q.id > 0) {
		$.db.query("SELECT * FROM alumni WHERE alum_id = ?", [q.id], function (rows) {
			displayFromDatasource(rows);
		});
	}
	else if (q.year > 1966 && q.names.length == 0) {
		$.db.query("SELECT * FROM alumni WHERE class = ?", [q.year], function (rows) {
			displayFromDatasource(rows);
			setHistory(q.year, "year:"+q.year);
		});
	}
	else if(q.names.length > 2) {
		$.db.query("SELECT * FROM alumni WHERE firstname LIKE '%?%' OR lastname LIKE '%?%' OR middlename LIKE '%?%'", q.names.slice(0,3), function (results) {
			displayFromDatasource(results);
			console.log("results1", results);
		});	
	}
	else {
		$.db.query("SELECT * FROM alumni WHERE fullname LIKE ?"+(new Array(q.names.length)).join(" OR fullname LIKE ?"), q.names.map(function (v) { return '%'+v+'%'}), function (results) {
			displayFromDatasource(results);
			console.log("results2", results);
		});
	}
}

var genGradYears = function () {
	var $gy = $('#grad-years');
	$gy.fadeOut(function () {
		$gy.html('');
		_.range(1966,((new Date()).getFullYear()) + 1).forEach(function (v,k) {
			$gy.append($.templates.render("gradYearLine", [v,v]));
		});
		$gy.fadeIn(function () {
			rebuildScrollers("sidebar");
		});
	});
}

var doSearch = function (v) {
	filter(parseQuery(typeof(v) == "string" ? v : $("#search").val()));
};



function parseQuery(str) {
	var parts = ($.trim(str)).replace("'", "").replace(/\s+/g, " ").replace(/\s*class of\s*/g, " ").split(" ");
	var year = false;
	var gender = false;
	var names = [];
	var id = false;
	
	var numstrings = 0;
	_.each(parts, function (v) {
		var subparts = v.split(":").map(function (s) { return s.trim(); });
		switch(subparts[0].toLowerCase()) {
			case "year":
				var i = parseInt(subparts[1]);
				
				if(i > 65 && i < 100) {
					i += 1900;
				}
				else if(i >= 0 && i < parseInt((new Date()).getFullYear().toString().substr(2))+1) {
					i += 2000;
				}
				year = i;
				break;
			case "firstname":
				names[0] = subparts[1];
				break;
			case "lastname":
				names[2] = subparts[1];
				break;
			case "gender":
				gender = subparts[1];
				break;
			case "id":
				var possible_id = parseInt(subparts[1]);
				if(!isNaN(possible_id)) {
					id = possible_id;
				}
				break;
			default:
				var i = parseInt(subparts[0]);
				if(i > 0) {
					if(i > 65 && i < 100) {
						i += 1900;
					}
					else if(i >= 0 && i < parseInt((new Date()).getFullYear().toString().substr(2))+1) {
						i += 2000;
					}
					year = i;
				}
				else {
					names.push(subparts[0]);
				}
				break;
		}
	});
	
	if(names.length == 2 && typeof(names[1]) == "string") {
		names[2] = names[1];
		names[1] = "";
	}
	
	return {
		"year": year,
		"gender": gender,
		"id" : id,
		"names": names
	};
}

$(function () {
	$.fn.fullHeight = function () {
		return this.each(function () {
			$(this).css('height', (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight)-($(this).outerHeight(true)-$(this).height())+'px');
		});
	};
	$.fn.stretch = function () {
		return this.each(function () {
			$(this).css('height', (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight)-$(this).offset().top+'px');
		});
	};
	$('#wrapper').fullHeight();
	$('#sidebar,#lsidebar,#inner-content').stretch();
	$('#search').click(function () {
		this.select() 
	});
		
	$.templates = {
		"randomAlumLine" : '<li><a href="#" rel="id:%s" class="search-link">%s %s %s <span>%s</span></a></li>',
		"gradYearLine": '<li><a href="#" rel="year:%s" class="search-link">%s</a></li>',
		"resultGroup" : '<ul class="result-group list large-list">%s</ul>',
		"person" : '<li class="result result-person"><a href="#" rel="id:%s"><b class="thumb-person"><img src="%s" /></b><b class="name">%s &#39;%s</b><br class="clear"/></a></li>',
		"details" : '<div class="details-person person-%s" rel="id:%s"><div class="gutter"><div class="image-person"><img src="%s" width="256" /></div><div class="data"><h3>%s &#39;%s</h3><p>Extra Information Goes Here, man</p></div></div><br class="clear"/></div>',
		"historyLink" : '<a href="#" rel="%s" class="history-link search-link">%s</a>',
		render : function (k,vs) {
			vs.unshift($.templates[k]);
			return sprintf.apply(window, vs);
		}
	};
	
	$.db = {
		query : function (sql, args, cb, cbe) {
			if(typeof(args) != "object") {
				cbe = cb;
				cb = args;
				args = [];
			}
			$.get("ajax.php", {query:sql,"args":JSON.stringify(args)}, function (str) {
				var data = JSON.parse(str);
				if(data.result == "success") {
					cb.apply($.db, [data.rows]);
				}
				else {
					$.db.error(data.error);
					console.log(data);
					//cbe.apply($.db, [data.error]);
				}
			});
		},
		error : function (err) {
			// worst. error. handling. ever.
			alert(err);
		}
	};
	
	$.drillDownHistory = {
		stack : [],
		add : function (name, e) {
			var last = this.stack[this.stack.length - 1];
			this.stack.push([name, e]);
			
			return $.templates.render("historyLink", [
				last[0],
				name
			]);
		},
		set : function (name, e) {
			this.stack = [[name,e]];
		},
		goTo : function (name) {
			this.stack.forEach(function (v,k) {
				if(v[0] == name) {
					displayFromDatasource(v[1]);
					this.stack = this.stack.slice(0,k);
					return;
				}
			});
		}
	};
	
	setInterval(function () {
		generateRandomAlums();
	}, 15 * 1000 /* 15 seconds */);
	generateRandomAlums();
	
	$("#go").click(doSearch);
	$("#search").focus(function () {
		$(this).select();
	}).keydown(function (e) {
		if(e.keyCode == 13) {
			doSearch();
		}
	});
	$(".search-link").live("click", function () {
		return false;
	});
	
	genGradYears();

	var storage = {};
	$('.list a').live('mousedown', function (e) {
		for (var n in e) {
			var m = n.match(/jQuery([0-9]+)/);
			if (m != null && m != undefined && m.length == 2) {
				storage[m[0]] = [e.pageX, e.pageY];
			}
		}
	}).live('mouseup', function (e) {
		for (var n in e) {
			var m = n.match(/jQuery([0-9]+)/);
			if (m != null && m != undefined && m.length == 2) {
				var coords = storage[m[0]];
				// console.log(Math.abs(e.pageX - coords[0]));
				// console.log(Math.abs(e.pageY - coords[1]));
				if (Math.abs(e.pageX - coords[0]) > 5 || Math.abs(e.pageY - coords[1]) > 5) {
					// $(this).bind('click', prevent);
					return false;
				}
				else {
					doSearch($(this).attr('rel'));
				}
			}
		}
	});
	

	rebuildScrollers("inner-content");
});
