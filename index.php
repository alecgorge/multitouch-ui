<!DOCTYPE html>
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
				background-image:-moz-linear-gradient(#732121, #5E1C1D);			
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
				padding:7px;
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
			#sidebar h2 {
				padding:20px;
				/*background: rgba(94,28,28,0.75);
				position:fixed;
				width:100%;*/
			}
			#lsidebar .gutter {
				padding:20px;
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
				background-image:-moz-linear-gradient(#521818,#481616);			
				border-bottom:1px #451414 solid;
				text-decoration:none;
				padding:10px 15px;
			}
			ul.list li a:hover, ul.list li a:active, ul.list li a:focus {
				/* background-image:-moz-linear-gradient(#6D2020,#511919);	*/
				background-image:-moz-linear-gradient(#FCD04B,#FBBC0F);	
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
		</style>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
		<script type="text/javascript">
			$(function () {
				$.fn.fullHeight = function () {
					return this.each(function () {
						$(this).css('height', (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight)-($(this).outerHeight(true)-$(this).height())+'px');
					});
				};
				$.fn.stretch = function () {
					return this.each(function () {
						console.log($(this).offset());
						$(this).css('height', (typeof window.innerHeight != 'undefined' ? window.innerHeight : document.body.offsetHeight)-$(this).offset().top+'px');
					});
				};
				$('#wrapper').fullHeight();
				$('#sidebar,#lsidebar').stretch();
				$('#search').click(function () {
					this.select() 
				});
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
						<ul id="grad-years" class="list">
							<?php foreach(range(1966, 2013) as $v) { ?><li><a href="/year/<?php echo $v; ?>"><?php echo $v; ?></a></li><?php } ?>
						</ul>
					</div>
				</div>
				<div id="lsidebar">
					<div class="gutter">
						<h2>Random Alums</h2>
						<p>yarbl</p>
					</div>
				</div>
				<div id="content">
					<div class="gutter">
						<h2>Welcome</h2>
						<p>Some really sweet text might be here someday...</p>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>