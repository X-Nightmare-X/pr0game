$(document).ready(function(){
	FleetTime();
});

function FleetTime() {
	$('.fleets').each(function() {
		var s		= $(this).data('fleet-time') - (serverTime.getTime() - startTime) / 1000;
		if(s <= 0) {
			$(this).text('-');
		} else {
			$(this).text(getRestTimeFormat(s));
		}
	});
	window.setTimeout('FleetTime()', 1000);
}
function add_static_times(){
  const mh = document.getElementsByClassName("statictimer")
  for (let flight of mh) {
    let nd = new Date(new Date(parseInt(flight.getAttribute("data-time")) * 1000 ).toLocaleString('en-US', { timeZone: localtimezonestring }))
    let ddiv=Math.floor((nd-serverTime) / (1000 * 60 * 60 * 24));
    let tamt=""
    if(ddiv>0){
      tamt=ddiv + "T "
    }

    flight.innerText =tamt +   pad(nd.getHours(), 2) + ":" + pad(nd.getMinutes(), 2) + ":" + pad(nd.getSeconds(), 2)
  }
}
add_static_times();
