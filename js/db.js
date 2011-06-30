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
