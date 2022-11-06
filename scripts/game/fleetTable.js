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


    for(let fleet of abortfleet){
      let cdate=new Date(serverTime)
      let startdate=new Date(1000*parseInt(fleet.getAttribute("starttime")))
      let difftonow=cdate-startdate
      cdate.setMilliseconds(cdate.getMilliseconds() + difftonow)
      let daydiff= Math.trunc((cdate - new Date(serverTime)) / (1000 * 60 * 60 * 24));

      if(daydiff>0){
        fleet.innerText = daydiff + "T "  + pad(cdate.getHours(),2) + ":" + pad(cdate.getMinutes(), 2) + ":" + pad(cdate.getSeconds(),2)
      }else{
        fleet.innerText = pad(cdate.getHours(),2) + ":" + pad(cdate.getMinutes(), 2) + ":" + pad(cdate.getSeconds(),2)
      }
    }
	}, 1000);
});
