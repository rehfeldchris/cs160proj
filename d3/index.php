<?php

/**
 * Displays data using Zoomable Partition 
 * Original version:
 * http://mbostock.github.io/d3/talk/20111018/partition.html
 * 
 * @author Chris Rehfeld
 * @author Tatiana Bragints
 */

?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
    <link type="text/css" rel="stylesheet" href="style.css"/>
    <script type="text/javascript" src="d3/d3.v3.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <style type="text/css">

.chart {
  display: block;
  margin: auto;
  margin-top: 100px;
  font-size: 11px;
}

rect {
  stroke: #eee;
  fill: #aaa;
  fill-opacity: .8;
}

rect.child {
	cursor: pointer;
}

rect.parent {
  fill: steelblue;
}

text {
  pointer-events: none;
}

    </style>
  </head>
  <body>
    <div id="body">
		<div id="header">
			Course name starts with..
			<div id="letters"></div>
		</div>
	
      <div id="footer">
        MOOCs
        <div class="hint">click or option-click to descend or ascend</div>
      </div>
    </div>
	<script type="text/javascript">

	/**
	 * Recursively processes JSON data and generates page
	 * @param JSON root
	 */
	function initD3(root) {
		var w = 1120,
		h = 600,
		x = d3.scale.linear().range([0, w]),
		y = d3.scale.linear().range([0, h]);

		var vis = d3.select("#body").append("div")
		.attr("class", "chart")
		.style("width", w + "px")
		.style("height", h + "px")
		.append("svg:svg")
		.attr("xmlns","http://www.w3.org/2000/svg")
		.attr("width", w)
		.attr("height", h);

		var partition = d3.layout.partition()
		.value(function(d) { return d.size; });


		var g = vis.selectAll("g")
		.data(partition.nodes(root))
		.enter().append("svg:g")
		.attr("transform", function(d) { return "translate(" + x(d.y) + "," + y(d.x) + ")"; })
		.on("click", click);

		var kx = w / root.dx,
		ky = h / 1;

		g.append("svg:rect")
            .attr("width", root.dy * kx)
            .attr("height", function(d) { return d.dx * ky; })
            .attr("class", function(d) { return d.children ? "parent" : "child"; });
    
	
		g.append("text")
			.attr("transform", transform)
			.attr("dy", ".35em")
			.style("opacity", function(d) { return d.dx * ky > 12 ? 1 : 0; })
			.style("font-size", function(d) { return d.parent ? "11px" : "36px"; })
			.text(function(d) { var t = new RegExp('.{1,' + Math.floor(root.dy * kx / 6.5) + '}', "g");
							return (root.dy * kx / d.name.length > 5.5) ? 
							d.name : d.name.match(t)[0] + " [..]";});


		d3.select(window)
		.on("click", function() { click(root); });

		function click(d) {

		if (!d.children ) { 
			if (d.parent) {
				d3.event.stopPropagation();
				window.open(d.course_link, "_blank");
				return; 
			}
		};

		kx = (d.y ? w - 40 : w) / (1 - d.y);
		ky = h / d.dx;
		x.domain([d.y, 1]).range([d.y ? 40 : 0, w]);
		y.domain([d.x, d.x + d.dx]);

		var t = g.transition()
		.duration(d3.event.altKey ? 7500 : 750)
		.attr("transform", function(d) { return "translate(" + x(d.y) + "," + y(d.x) + ")"; });

		t.select("rect")
		.attr("width", d.dy * kx)
		.attr("height", function(d) { return d.dx * ky; });

		t.select("text")
        .attr("transform", transform)
        .style("opacity", function(d) { return d.dx * ky > 12 ? 1 : 0; })
		.text(function(d) { var t = new RegExp('.{1,' + Math.floor(d.dy * kx / 6.5) + '}', "g");
							return (d.dy * kx / d.name.length > 5.5) ? 
							d.name : d.name.match(t)[0] + " [..]";});

		d3.event.stopPropagation();


		}

		function transform(d) {
			return "translate(8," + d.dx * ky / 2 + ")";
		}
	}

	/**
	 * Based on the letter selected by user,
	 * selects relevant part of JSON data
	 * (courses that starts with this letter)
	 */
	$(function(){
		var file = "../courseList.php";
		$.getJSON(file, function(data){
			var s = "abcdefghijklmnopqrstuvwxyz";
			$.each(s.split(""), function(_, letter){
				$letter = $('<button class="button blue">' +letter +"</button>");
				$.data($letter[0], "letter", letter);
				$letter.click(function(){
					var letter = $.data(this, "letter");
					var root = {name: letter, children: data[letter]};
					$(".chart").remove();
					initD3(root);
				}).appendTo("#letters");
			});
		});
	});
	</script>

  </body>
</html>