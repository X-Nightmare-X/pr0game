<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="{$lang}" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="{$lang}" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="{$lang}" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="{$lang}" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="{$lang}" class="no-js">
<!--<![endif]-->
<style>
  .colorPositive {
    color:{$signalColors.colorPositive}
  }

  .colorNegative {
    color:{$signalColors.colorNegative} !important;
  }

  .colorNeutral {
    color:{$signalColors.colorNeutral}
  }
</style>

<head>
  <title>{block name="title"} - {$uni_name} - {$game_name}{/block}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
  {if !empty($goto)}
    <meta http-equiv="refresh" content="{$gotoinsec};URL={$goto}">
  {/if}
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="./styles/resource/css/base/boilerplate.css">
  <link rel="stylesheet" type="text/css" href="./styles/resource/css/ingame/main.css">
  <link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.css">
  <link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.fancybox.css">
  <link rel="stylesheet" type="text/css" href="./styles/resource/css/base/validationEngine.jquery.css">
  <link rel="stylesheet" type="text/css" href="{$dpath}formate.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.12/css/all.css">
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">

  <script type="text/javascript">
    var ServerTimezoneOffset = {$Offset};
    var serverTime 	= new Date({$date.0}, {$date.1 - 1}, {$date.2}, {$date.3}, {$date.4}, {$date.5});
    var startTime = serverTime.getTime();
    var localTime = serverTime;
    var localTS = startTime;
    var Gamename = document.title;
    var Ready		= "{$LNG.ready}";
    var Skin		= "{$dpath}";
    var Lang		= "{$lang}";
    var head_info	= "{$LNG.fcm_info}";
    var auth		= {$authlevel|default:'0'};
    var days 		= {$LNG.week_day|json_encode|default:'[]'}
    var months 		= {$LNG.months|json_encode|default:'[]'} ;
    const localtimezonestring="{$TIMEZONESTRING}"
    var tdformat	= "{$LNG.js_tdformat}";
    var queryString	= "{$queryString|escape:'javascript'}";
    var isPlayerCardActive	= "{$isPlayerCardActive|json_encode}";
    var relativeTime = Math.floor(Date.now() / 1000);

    setInterval(function() {
      if (relativeTime < Math.floor(Date.now() / 1000)) {
        serverTime.setSeconds(serverTime.getSeconds() + 1);
        relativeTime++;
      }
    }, 1);
  </script>
  {if $captchakey!=""}
    <script src="https://www.google.com/recaptcha/api.js?render={$captchakey}"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const captchakey="{$captchakey}"
        $('form').submit(function(event) {
          event.preventDefault();
          grecaptcha.ready(() => {
            grecaptcha.execute(captchakey).then((token) => {
              let hinpt = document.createElement("input")
              hinpt.type = "hidden"
              hinpt.name = "rcaptcha"
              hinpt.value = token
              this.insertBefore(hinpt, this.firstChild);
              this.submit()
            });
          });
        });
      });
    </script>
    <style>
      .grecaptcha-badge {
        visibility: hidden;
      }
    </style>
  {/if}


  <script type="text/javascript" src="./scripts/base/jquery.js"></script>
  <script type="text/javascript" src="./scripts/base/jquery.ui.js"></script>
  <script type="text/javascript" src="./scripts/base/jquery.cookie.js"></script>
  <script type="text/javascript" src="./scripts/base/jquery.fancybox.js"></script>
  <script type="text/javascript" src="./scripts/base/jquery.validationEngine.js"></script>
  <script type="text/javascript" src="./scripts/l18n/validationEngine/jquery.validationEngine-{$lang}.js"></script>
  <script type="text/javascript" src="./scripts/base/tooltip.js"></script>
  <script type="text/javascript" src="./scripts/game/base.js"></script>
  {foreach item=scriptname from=$scripts}
    <script type="text/javascript" src="./scripts/game/{$scriptname}.js"></script>
  {/foreach}
  {block name="script"}
    <script>
      $(function() {
        $("#gl1").on('click', function() {
          $(".planetb").hide();
          $(".planetb1").show();
          $("#gl2, #gl3").removeClass("selected");

          $(this).addClass("selected");
        });
      });
      $(function() {
        $("#gl2").on('click', function() {
          $(".planetb1").toggle();
          $(".planetb").show();
          $("#gl1, #gl3").removeClass("selected");

          $(this).addClass("selected");
        });
      });
      $(function() {
        $("#gl3").on('click', function() {
          $(".planetb").show();
          $(".planetb1").show();
          $("#gl2, #gl1").removeClass("selected");

          $(this).addClass("selected");
        });
      });

      $(function() {
        $("#0h").on('click', function() {
          for (i = 1; i <= 99; i++)
            $("#h" + i).hide();
          $("#0s").show();
          $("#0h").hide();

        });
      });

      $(function() {
        $("#0s").on('click', function() {
          for (i = 1; i <= 99; i++)
            $("#h" + i).show();
          $("#0h").show();
          $("#0s").hide();

        });
      });

      $(function() {
        $("#100h").on('click', function() {
          for (i = 101; i <= 199; i++)
            $("#h" + i).hide();
          $("#100s").show();
          $("#100h").hide();

        });
      });

      $(function() {
        $("#100s").on('click', function() {
          for (i = 101; i <= 199; i++)
            $("#h" + i).show();
          $("#100h").show();
          $("#100s").hide();

        });
      });

      $(function() {

        $("#200h").on('click', function() {
          for (i = 201; i <= 299; i++)
            $("#h" + i).hide();
          $("#200s").show();
          $("#200h").hide();

        });
      });
      $(function() {

        $("#200s").on('click', function() {
          for (i = 201; i <= 299; i++)
            $("#h" + i).show();
          $("#200s").hide();
          $("#200h").show();

        });
      });
      $(function() {

        $("#400h").on('click', function() {
          for (i = 401; i <= 499; i++)
            $("#h" + i).hide();
          $("#400s").show();
          $("#400h").hide();

        });
      });
      $(function() {

        $("#400s").on('click', function() {
          for (i = 401; i <= 499; i++)
            $("#h" + i).show();
          $("#400s").hide();
          $("#400h").show();

        });
      });
      $(function() {

        $("#500h").on('click', function() {
          for (i = 501; i <= 599; i++)
            $("#h" + i).hide();
          $("#500s").show();
          $("#500h").hide();

        });
      });
      $(function() {

        $("#500s").on('click', function() {
          for (i = 501; i <= 599; i++)
            $("#h" + i).show();
          $("#500s").hide();
          $("#500h").show();

        });
      });
      $(function() {

        $("#600h").on('click', function() {
          for (i = 601; i <= 699; i++)
            $("#h" + i).hide();
          $("#600s").show();
          $("#600h").hide();

        });
      });
      $(function() {

        $("#600s").on('click', function() {
          for (i = 601; i <= 699; i++)
            $("#h" + i).show();
          $("#600s").hide();
          $("#600h").show();

        });
      });
    </script>
  {/block}
  <script type="text/javascript">
    $(window).scroll(function() {
      // affix
      windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
      lastScroll = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

      // menu
      if (document.getElementsByTagName("menu").length > 0) {
        elementHeight = document.getElementsByTagName("menu")[0].getElementsByClassName("fixed")[0].clientHeight;
        element = document.getElementsByTagName("menu")[0].getElementsByClassName("fixed")[0];
        if (elementHeight > windowHeight - 100) {
          a = 100 - lastScroll;
          b = windowHeight - elementHeight;
          scrollTo = Math.max(a, b);
          element.style.top = scrollTo + 'px';
        }
      }
    });
    $(function() {
      {$execscript}
    });
  </script>
</head>

<body id="{$smarty.get.page|default:'overview'}" class="{$bodyclass}">
<div id="tooltip" class="tip"></div>
