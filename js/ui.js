function alum_headshot(fname, lname, year, isThumb) {
	return sprintf("alumni/1966/"+(isThumb ? "_thumb/" : "")+"%s_%s_%s.jpg.jpg", year, lname, fname);
}

function preload(arrayOfImages) {
    $(arrayOfImages).each(function(){
        $('<img/>')[0].src = this;
        // Alternatively you could use:
        // (new Image()).src = this;
    });
}

// this should be the ONLY bootstrapper
$(function () {
	// create a process queue to handle the async events
	var queue = new Queue(),
		$people = $('#people');

	data.alumni = {};
	data.alumni_keys = [];
	data.alumni_id_lookup = {};
	var toPreload = [];
	var classes = [];
	
	queue
		.after([
			$.db.query, // load all alumni into memory
			function (cb) { // render all alumni in the canvas
				var html = ""; // gather all html so the dom is maniuplated only once
				
				data.alumni_keys.forEach(function (subk, k) {
					var in_html = "",
						grad_class	= data.alumni[subk],
						num_alums	= grad_class.length,
						rows		= 5,
						orphans		= num_alums % 5,
						cols		= (num_alums - orphans)/5 + (orphans != 0);
					
					in_html = "<table class='grid'><tbody>";
					for(var y = 0; y < rows; y++) {
						in_html += "<tr>";
						for(var x = 0; x < cols; x++) {
							var alum_key = (5*x)+y,
								row		 = grad_class[alum_key];
								
							if(typeof row == "undefined") { in_html += "<td></td>"; continue; }
							in_html += $.templates.render('grid-alumn', [
								row.alum_id,
								alum_headshot(row.firstname, row.lastname, row["class"], true),
								row.fullname
							]);

							toPreload.push(alum_headshot(row.firstname, row.lastname, row["class"], false));
						}
						in_html += "</tr>";
					}
					in_html += "</tbody></table>";
					html += $.templates.render('class', [ subk, subk, in_html ]);
				});
				
				// console.log(html);
				$people.html(html);
				
				cb();
			}
		], [
			[	//  WHERE `class` > 0 AND `class` < 1970
				"SELECT `alum_id`,`firstname`,`lastname`,`middlename`,`nickname`,`fullname`,`class` FROM alumni ORDER BY `class` ASC, `lastname` ASC, `firstname` ASC",
				function (rows) {
					rows.forEach(function (row, k) {
						// check if class exists in memory yet
						// if it doesn't exist create an array at that point
						if(!data.alumni[row["class"]]) {
							data.alumni[row["class"]] = [];
							data.alumni_keys.push(row["class"]);
							classes.push(row["class"]);
						}
						
						// add the whole row
						data.alumni[row["class"]].push(row);
						data.alumni_id_lookup[row.alum_id] = {class:row.class, "pos":data.alumni[row.class].length-1};
					});
					queue.next();
				},
				$.db.error
			],
			[queue.next]
		])
		.start(function () {
			preload(toPreload);
			
			$search = $("#search");
			var offsets = [];
			$('#people li').each(function () {
				offsets.push($(this).offset().left);
			});
			
			var $canvas = $('#canvas');
			$canvas.scroll(function (e) {
				if(typeof tmt != "undefined") clearTimeout(window.tmt);
				window.tmt = setTimeout(function () {
					var l = $canvas.scrollLeft(),
						last = 0;
					offsets.forEach(function (v,k) {
						if(l >= v) last = k;
					});
					
					var baseOffset = offsets[last],
						left = l - baseOffset,
						obj = $('.title-bar .gold-gradient', $('#people li')[last]);
				
					obj.css('left', (left-50)+"px").animate({
						'left': left+'px'
					}, 'easeOutCirc');
				}, 250);
			});
			
			//console.log(classes);
			var ihtml = "";
			classes.forEach(function (v, k) {
				ihtml += $.templates.render('classbox', [v,v]);
			});
			$('#class-list').html(ihtml).stretch();
			$('#searcher').stretch();
			
			var il;
			function showSearcher() {
				$('#class-picker').slideUp();
				$(this).text('Close').addClass('active').blur();
				$('.ui-keyboard').show();
				$search.focus();
				$('#searcher').slideDown(function () {
					$search.keyboard({
						usePreview:false,
						alwaysOpen: true,
						stayOpen: true,
						position: {
							of: null,
							my: 'center top',
							at: 'center bottom'
						}
					});
				});
				
				var lastVal;
				il = setInterval(function () {
					var val = $search.val();
					
					if(val.length == 0) {
						$('#search-results').html('');
						return;
					}
					if(val.length < 2 || val == lastVal) {
						return;
					}
					lastVal = val;
					
					// time to hit the db and display the results
					var ih = "",
						arg="%"+val+"%";
					$.db.query("SELECT * FROM `alumni` WHERE firstname LIKE ? OR lastname LIKE ? OR nickname LIKE ? OR fullname LIKE ? ORDER BY `class` ASC", [
						arg, arg, arg, arg
					], function (rows) {
						rows.forEach(function (row) {
							ih += $.templates.render('search-result', [
								row.alum_id,
								alum_headshot(row.firstname, row.lastname, row["class"], true),
								row.fullname,
								row["class"].substr(2)
							]);				
						});
						$('#search-results').html(ih);
					});
				}, 50);
				
				return false;
			}
			
			function hideSearcher () {
				clearInterval(il);
				$(this).text('Search').removeClass('active').blur();
				$("#searcher").slideUp();
				if($search.getkeyboard()) {
					$search.getkeyboard().destroy();
				}
				return false;
			}
			$('.toggle-search').click(function () {
				if($('#searcher').is(':visible')) {
					$('#class-picker').slideUp();
					return hideSearcher.apply(this);
				}
				else {
					return showSearcher.apply(this);
				}
			});
			
			$('.ui-keyboard').hide();
			
			$('.toggle-class-picker').click(function () {
				hideSearcher.apply($('.toggle-search')[0]);
				$('#class-picker').slideToggle();
				return false;
			});
			
			
			$('.class-jump-link').click(function () {
				$('#class-picker').slideToggle();
				var of = offsets[classes.indexOf($(this).attr('data-class'))];
				$('#canvas').scrollLeft(of);
			});
			
			$('.detail-link').live('click', function () {
				var id = $(this).attr('data-alum-id'),
					alum_info = data.alumni_id_lookup[id],
					alum = data.alumni[alum_info.class][alum_info.pos];
				
				$('#overlay').fadeIn(function () {
					var $obj = $(sprintf('<div class="modal"><a href="javascript:;" class="close">close</a><div class="gutter"><img src="%s" /><p>%s</p></div></div>', alum_headshot(alum.firstname, alum.lastname, alum["class"], false), alum.fullname));
					
					$obj.center();
					
					$('body').append($obj);
					
					$('.modal img').load(function () {
						$(this).parents('.modal').center();
					});
					
					$('.modal .close, #overlay').click(function () {
						$('.modal').fadeOut(function () {
							$(this).remove();
						});
						$('#overlay').fadeOut();
					});					
				});
			});
			showSearcher.apply($('.toggle-search')[0]);		
		});
});