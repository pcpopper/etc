<script>
// var i = 0;
// var t = 0;
// setTimeout(function() { t = 1; }, 20);
// while (t != 1) {
// 	if (i > 50) break;
// 		document.write(i + " " + t + "<br>");
// 	i++;
// }
// document.write("fin");
// var t = setTimeout(function() { i = 1; }, 20);
// document.write(i + "<br>");
// var t2 = setTimeout(function() {
// 	document.write(i);
// }, 1000);

var i = 0;
var myTimer = setInterval(function() {
	document.write(i++);
		document.write('&pound;');
	if (i > 5) {
		clearInterval(myTimer);
	}
}, 500);

</script>
