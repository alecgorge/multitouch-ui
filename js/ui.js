function alum_headshot(fname, lname, year, isThumb) {
	return sprintf("alumni/%s/"+(isThumb ? "_thumb/" : "")+"%s_%s_%s.jpg.jpg", year, year, lname, fname);
}

// this should be the ONLY bootstrapper
$(function () {
	// create a process queue to handle the async events
	var queue = new Queue(),
		$people = $('#people');

	data.alumni = {};
	data.alumni_keys = [];
	data.alumni_id_lookup = {};
	
	queue
		.after([
			$.db.query, // load all alumni into memory
			function (cb) { // render all alumni in the canvas
				var html = ""; // gather all html so the dom is maniuplated only once
				
				data.alumni_keys.forEach(function (subk, k) {
					var in_html = "",
						grad_class = data.alumni[subk];
					grad_class.forEach(function (row, k) {
						in_html += $.templates.render('grid-alumn', [
							row.alum_id,
							alum_headshot(row.firstname, row.lastname, row["class"], true),
							row.fullname,
							//row["class"].toString().substr(2)
						]);
					});
					
					html += $.templates.render('class', [ subk, subk, $.templates.render('grid', [ in_html ]) ]);
				});
				
				// console.log(html);
				$people.html(html);
				
				cb();
			}
		], [
			[
				"SELECT `alum_id`,`firstname`,`lastname`,`middlename`,`nickname`,`fullname`,`class` FROM alumni ORDER BY `class` ASC, `lastname` ASC, `firstname` ASC",
				function (rows) {
					rows.forEach(function (row, k) {
						// check if class exists in memory yet
						// if it doesn't exist create an array at that point
						if(!data.alumni[row["class"]]) {
							data.alumni[row["class"]] = [];
							data.alumni_keys.push(row["class"]);
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
			// all done we can bind events now
			$('.detail-link').click(function () {
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
					
					$('.modal .close').click(function () {
						$(this).parent().fadeOut(function () {
							$(this).remove();
						});
						$('#overlay').fadeOut();
					});					
				});
			});
		});
});