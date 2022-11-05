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
      let returntime=new Date(date.now() + date.now() - parseInt(fleet.starttime))
      let daydiff= Math.trunc((returntime - cdate) / (1000 * 60 * 60 * 24));

      if(daydiff>0){
        fleet.innerText = daydiff + "T "  + returntime.getHours() + ":" + pad(returntime.getMinutes(), 2) + ":" + pad(returntime.getSeconds())
      }else{
        fleet.innerText = returntime.getHours() + ":" + pad(returntime.getMinutes(), 2) + ":" + pad(returntime.getSeconds())
      }
    }
	}, 1000);
});
