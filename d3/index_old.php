<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <link type="text/css" rel="stylesheet" href="style.css"/>
    <script type="text/javascript" src="d3/d3.v3.min.js"></script>
    <style type="text/css">

.chart {
  display: block;
  margin: auto;
  margin-top: 60px;
  font-size: 11px;
}

rect {
  stroke: #eee;
  fill: #aaa;
  fill-opacity: .8;
}

rect.parent {
  cursor: pointer;
  fill: steelblue;
}

text {
  pointer-events: none;
}

    </style>
  </head>
  <body>
    <div id="body">
      <div id="footer">
        d3.layout.partition
        <div class="hint">click or option-click to descend or ascend</div>
      </div>
    </div>
    <script type="text/javascript">

var w = 1120,
    h = 600,
    x = d3.scale.linear().range([0, w]),
    y = d3.scale.linear().range([0, h]);

var vis = d3.select("#body").append("div")
    .attr("class", "chart")
    .style("width", w + "px")
    .style("height", h + "px")
  .append("svg:svg")
    .attr("width", w)
    .attr("height", h);

var partition = d3.layout.partition()
    .value(function(d) { return d.size; });

d3.json("big.json", function(root) {
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
      
  var svg = g.append("svg:text")
            .attr("transform", transform)
            .attr("dy", ".35em")
            .style("opacity", function(d) { return d.dx * ky > 12 ? 1 : 0; })
            .style("text-decoration", "underline")
            .style("color", "blue")
            .style("font-size", function(d) { return d.dx * ky / 10 + "px"; })
            .text(function(d) { return d.name; })
        .append("foreignObject")
.attr("width", 80)
.attr("height", 80)
.append("xhtml:body")
.style("font", "14px 'Helvetica Neue'")
.html("<h1>text</h1>");
  
    
    d3.select(window)
      .on("click", function() { click(root); });

  function click(d) {
    
    if (!d.children) { 
        if ((d.y + d.dy) === h) {
            window.open("http://www.google.com", "_blank"); 
            d3.event.stopPropagation();
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
        .style("font-size", function(d) { return d.dx * ky / 10 + "px"; });

    d3.event.stopPropagation();
    
    
  }

  function transform(d) {
    return "translate(8," + d.dx * ky / 2 + ")";
  }
});

    </script>
  </body>
</html>