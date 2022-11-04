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
      let returntime=new Date(Date.now()+parseInt(fleet.starttime)*2)
      let daydiff= Math.trunc((returntime - cdate) / (1000 * 60 * 60 * 24));

      if(daydiff>0){
        fleet.innerText = daydiff + "T "  + daydiff.getHours() + ":" + pad(daydiff.getMinutes(), 2) + ":" + pad(daydiff.getSeconds())
      }else{
        fleet.innerText = daydiff.getHours() + ":" + pad(daydiff.getMinutes(), 2) + ":" + pad(daydiff.getSeconds())

      }
    }
	}, 1000);
});
