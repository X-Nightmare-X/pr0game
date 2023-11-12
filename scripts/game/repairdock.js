var resttime = 0;
var time = 0;
var endtime = 0;
var interval = 0;
var buildname = "";
var umode = false;

function Repairlist() {
  var rest = resttime;
  if (!umode) {
    rest = resttime - (serverTime.getTime() - startTime) / 1000;
  }
  if (rest <= 0) {
    window.clearInterval(interval);
    $('#time').text(Ready);
    $('#command').remove();
    document.title = Ready + ' - ' + Gamename;
    $('#deployRepaired').show();
    $('#progress').hide();
    return;
  }
  document.title = getRestTimeFormat(rest) + ' - ' + Gamename;

  $('#time').text(getRestTimeFormat(rest));
}

$(document).ready(function () {
  time = $('#time').data('time');
  resttime = $('#progressbar').data('time');
  endtime = $('.timer').data('time');
  umode = $('.timer').data('umode') == 1;
  interval = window.setInterval(Repairlist, 1000);

  window.setTimeout(function () {
    if (time <= 0) return;

    $('#progressbar').progressbar({
      value: Math.max(100 - (resttime / time) * 100, 0.01)
    });
    if (!umode) {
      $('.ui-progressbar-value').addClass('ui-corner-right').animate({ width: "100%" }, resttime * 1000, "linear");
    }
  }, 5);

  Repairlist();
});
