$(function() {
	window.setInterval(function() {
		$('.fleets').each(function() {
			var s		= $(this).data('fleet-time') - (serverTime.getTime() - startTime) / 1000;
			if(s <= 0) {
				$(this).text('-');
			} else {
				$(this).text(getRestTimeFormat(s));
			}
		})
    const abortfleet=document.getElementsByClassName("aborttime")
    let cdate=new Date(Date.now())
    for(let fleet of abortfleet){
      let startdate=new Date(parseInt(fleet.getAttribute("aborttime")))
      let difftonow=cdate-startdate
      let returntime=new Date(cdate.getTime() + difftonow)
      let daydiff= Math.trunc((difftonow) / (1000 * 60 * 60 * 24));

      if(daydiff>0){
        fleet.innerText = daydiff + "T "  + returntime.getHours() + ":" + pad(returntime.getMinutes(), 2) + ":" + pad(returntime.getSeconds())
      }else{
        fleet.innerText = returntime.getHours() + ":" + pad(returntime.getMinutes(), 2) + ":" + pad(returntime.getSeconds())
      }
    }
	}, 1000);
});
