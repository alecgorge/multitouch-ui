<?php
$db = new PDO('sqlite:db/alums.sqlite');

function middle_initial ($x) {
	return $x == "NULL" ? "" : substr($x,0,1).".";
}
?><!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
			body {margin:0;padding:0;font:16px Arial,sans-serif;background:#411B18;}
			#wrapper {
				background: #57191A;
				padding:0;
				overflow:auto;
				/*margin:30px;*/
				box-shadow: 0 0 17px 0px #000;
				color:white;
				/*border-radius:15px;*/
			}
			.gradient {
				background-image:-webkit-linear-gradient(#732121, #5E1C1D);			
				border-bottom:1px #451414 solid;
			}
			#header {
				padding: 30px 20px;
				text-align:center;
			}
			#header h1 {
				color:#FBBC0F;
				font-weight:normal;
				font-family:Georgia,serif;
				margin:0;
				font-size:65px;
			}
			#header p {
				text-transform:uppercase;
				color:#ccc;
				font-weight:bold;
				letter-spacing:2px;
				font-size:14px;
				margin:0;
				padding-top:10px;
			}
			#search-bar .gutter {
				padding:10px 20px;
				text-align:center;
			}
			#search {
				padding:6px;
				border:1px #451414 solid;
				border-radius: 4px 0 0 4px;
				border-right:0;
				margin:0;
				background:#eee;
				width:250px;
				text-align:center;
				font-size:18px;
				color:#666;
			}
			#go {
				margin:0;
				padding:6px;
				border:1px #451414 solid;
				border-radius: 0 4px 4px 0;
				background:#ddd;
				text-align:center;
				font-size:18px;
				color:#222;
			}
			#sidebar, #lsidebar {
				position:relative;
				z-index:1;
				width:auto;
				overflow:scroll;			
			}
			#sidebar {
				float:right;
				width:20%;
				border-left:1px #451414 solid;
				background: #5F1C1C;
				overflow:auto;
			}
			#inner-wrapper { 
				position:relative;
			}
			#lsidebar {
				float:left;
				width:20%;
				border-right:1px #451414 solid;
				background: #5F1C1C;
				overflow:auto;
			}
			#sidebar .gutter {
				padding:0;
			}
			#sidebar h2, #lsidebar h2 {
				padding:20px;
				/*background: rgba(94,28,28,0.75);
				position:fixed;
				width:100%;*/
			}
			#lsidebar .gutter {
				padding:0px;
			}
			h2 {
				margin:0;
				font-weight:normal;
				opacity:0.7;
			}
			.clear {
				clear:both;
				height:1px;
			}
			
			ul.list {
				list-style:none;
				margin:0;
				padding:0;
			}
			ul.list li {
				margin:0;
			}
			ul.list li a {
				display:block;
				color:white;
				background-image:-webkit-linear-gradient(#521818,#481616);			
				border-bottom:1px #451414 solid;
				text-decoration:none;
				padding:10px 15px;
			}
			ul.list li a:hover, ul.list li a:active, ul.list li a:focus {
				/* background-image:-webkit-linear-gradient(#6D2020,#511919);	*/
				background-image:-webkit-linear-gradient(#FCD04B,#FBBC0F);	
				color:black;
				border-color:#000;
			}
			ul.list li a:hover span, ul.list li a:active span, ul.list li a:focus span {
				color:black;
			}
			ul.large-list {
				list-style:none;
				margin:0;
				padding:0;
			}
			ul.large-list li {
				margin:0;
			}
			ul.large-list li a {
				display:block;
				color:white;
				background-image:-webkit-linear-gradient(#521818,#481616);			
				border-bottom:1px #451414 solid;
				text-decoration:none;
				padding:10px 15px;
			}
			ul.large-list li a span{
				float:right;
				opacity:0.5;
			}
			ul.large-list li a:hover, ul.large-list li a:active, ul.large-list li a:focus {
				/* background-image:-webkit-linear-gradient(#6D2020,#511919);	*/
				background-image:-webkit-linear-gradient(#FCD04B,#FBBC0F);	
				color:black;
				border-color:#000;
			}
			#content {
				width:60%;
				margin:auto;
				overflow:auto;
			}
			#content .gutter {
				padding:20px;
			}
			#sort-box {
				float:left;
				width:45%;
			}
			#filter-box {
				float:right;
				text-align:right;
				width:45%;
			}
			#filter-sort-bar {
				background-image:-webkit-linear-gradient(#521818,#481616);			
			}
			#filter-sort-bar .bar-button {
				color:rgba(255,255,255,0.7);
				text-decoration:none;
				padding:15px;
				display:block;
				text-align:center;
				border-bottom:1px #451414 solid;
			}
			#filter-sort-bar a.bar-button:hover, #filter-sort-bar a.bar-button:active, #filter-sort-bar a.active.bar-button  {
				color:rgba(255,255,255,0.7);
				background-image:-webkit-linear-gradient(#FCD04B,#FBBC0F);	
				color:#521818;
			}
			#filter-box .bar-button {
				float:right;
			}
			#sort-box .bar-button {
				float:left;
			}
		</style>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
		<script type="text/javascript" src="http://documentcloud.github.com/underscore/underscore-min.js"></script>
		<script type="text/javascript" src="js/iscroll.js"></script>
		<script type="text/javascript">
			var trigger_s = {};
			function bind (k,v) {
				if(typeof(trigger_s[k]) == undefined) {
					trigger_s[k] = [];
				}
				trigger_s[k].push(v);
			}
			
			function trigger (k,args, that) {
				if(typeof(trigger_s[k]) != undefined) {
					_.map(trigger_s[k], function () {
						this.apply(that, args);
					});
				}
			}
				
			function displayFromDatasource(ds) {
				
			}
			
			// returns a "Datasource"
			function filter(query) {
				if(typeof(query) == "string") {
					var q = parseQuery(query);
				}
				else {
					var q = query;
				}
				
				$.db.query("SELECT * FROM alumni WHERE fullname LIKE '%?%'", [q.name], function (tx, results) {
					console.log(tx);
					console.log(results);
				});
				$.db.query("SELECT * FROM alumni WHERE firstname LIKE '%?%' OR lastname LIKE '%?%' OR middlename LIKE '%?%'", [q.name, q.name, q.name], function (tx, results) {
					console.log(tx);
					console.log(results);
				});
			}
			
			function parseQuery(str) {
				var parts = ($.trim(str)).replace("'", "").replace(/\s+/g, " ").replace(/\s*class of\s*/g, " ").split(" ");
				var year = false;
				var gender = false;
				var names = [];
				
				var numstrings = 0;
				_.map(parts, function (v) {
					var subparts = v.split(":");
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
				$('#sidebar,#lsidebar').stretch();
				$('#search').click(function () {
					this.select() 
				});
				
				$.db = {
					_db : false,
					init : function () {
						this._db = openDatabase('alumni', '2.0', 'Multitouch Alums', 25 * 1024 * 1024);
						var that = this;
						$.get('db/dump.sql', function (data) {
							var x = data.split("\n");
							// var called = false;
							that._db.transaction(function (tx) {
								// called = true;
								_.map(x, function (v,k) {
									// console.log(v);
									tx.executeSql(v);
								});
							});
							// called && alert('holy monkeybrains batman! it works!');
						}); 
					},
					query : function (sql, args, cb, cbe) {
						if(typeof(args) != "object") {
							cb = args;
							args = [];
						}
						this._db.transaction(function (tx) {
							tx.executeSql(sql, args, cb, cbe);
						});
					},
				};
				$.db.init();
				
				// a datasource is an array of persons
				// a person is:
				/*
					{
						name:
					}
				*/
				
				// var prevent = function(e) {$(this).unbind('click', prevent); return false; };
				// var storage = {};
				// $('#sidebar a, #lsidebar a').mousedown(function (e) {
					// for(var n in e) {
						// var m = n.match(/jQuery([0-9]+)/);
						// if(m != null && m != undefined && m.length == 2) {
							// storage[m[0]] = [e.pageX, e.pageY];
						// }
					// }
				// }).mouseup(function (e) {
					// for(var n in e) {
						// var m = n.match(/jQuery([0-9]+)/);
						// if(m != null && m != undefined && m.length == 2) {
							// var coords = storage[m[0]];
							// console.log(Math.abs(e.pageX - coords[0]));
							// console.log(Math.abs(e.pageY - coords[1]));
							// if(Math.abs(e.pageX - coords[0]) > 5 || Math.abs(e.pageY - coords[1]) > 5) {
								// $(this).bind('click', prevent);
								// return false;
							// }
						// }
					// }
				// });				
				var gradyears = new iScroll('sidebar');
				var ralums = new iScroll('lsidebar');				
				//$.db.init();
			});
		</script>
	</head>
	<body>
		<div id="wrapper">
			<div id="header" class="gradient">
				<h1>Brebeuf Jesuit</h1>
				<p>Men and Women for Others</p>
			</div>
			<div id="search-bar" class="gradient">
				<div class="gutter">
					<input id="search" type="text" value="Search alumni..." /><input id="go" type="submit" value="Go" />
				</div>
			</div>
			<div id="inner-wrapper">
				<div id="sidebar">
					<div class="gutter">
						<h2>Graduation Years</h2>
						<ul id="grad-years" class="list large-list" id="grad-years">
							<?php foreach(range(1966, 2013) as $v) { ?><li><a href="#" rel="/year/<?php echo $v; ?>"><?php echo $v; ?></a></li><?php } ?>
						</ul>
					</div>
				</div>
				<div id="lsidebar">
					<div class="gutter">
						<h2>Random Alums</h2>
						<ul class="large-list list" id="random-alums">
						<?php
						$smt = $db->prepare("SELECT * FROM alumni ORDER BY RANDOM() LIMIT 10");
						$smt->execute();
						foreach($smt->fetchAll(PDO::FETCH_ASSOC) as $row) {
							printf(<<<EOT
	<li><a href="#">%s %s %s <span>%s</span></a></li>		
EOT
,$row['firstname'], middle_initial($row['middlename']), $row['lastname'], $row['class']);
						}
						?>
						</ul>
					</div>
				</div>
				<div id="content">
					<div id="filter-sort-bar">
						<div id="sort-box">
							<span class="bar-button">Sort by:</span>
							<a href="#" class="bar-button active">First Name</a>
							<a href="#" class="bar-button">Last Name</a>
							<a href="#" class="bar-button">Graduation Year</a>
						</div>
						<div id="filter-box">
							<a href="#" id="filter-button" class="bar-button">Filter Results</a>
						</div>
						<br class="clear"/>
					</div>
					<div class="gutter">
						<h2>Welcome</h2>
						<p>Some really sweet text might be here someday...</p>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>