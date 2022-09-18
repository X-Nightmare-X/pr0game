var umode = false;
$(document).ready(function () {
  window.setInterval(function () {
    $('.fleets').each(function () {
      var s = $(this).data('fleet-time') - (serverTime.getTime() - startTime) / 1000;
      if (s <= 0) {
        $(this).text('-');
      } else {
        $(this).text(getRestTimeFormat(s));
      }
    })
  }, 1000);
  umode		= $('.timer:first').data('umode') == 1;
  window.setInterval(function () {
    $('.timer').each(function () {
      var s = $(this).data('time')
	    if (!umode) {
		    s = s - (serverTime.getTime() - startTime) / 1000;
	    }

      if (s <= 0) {
        s = 0;
      }

      $(this).text(getRestTimeFormat(s));
    });

  $('.timershort').each(function () {
    var s = $(this).data('time')
    if (!umode) {
      s = s - (serverTime.getTime() - startTime) / 1000;
    }

    if (s <= 0) {
      s = 0;
    }

    $(this).text(getRestTimeFormat(s,true));
  });

  }, 1000);
});
