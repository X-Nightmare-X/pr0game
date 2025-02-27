<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="{$lang}" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="{$lang}" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="{$lang}" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="{$lang}" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="{$lang}" class="no-js"> <!--<![endif]-->
<style>
  .colorPositive {
    color: #00ff00;
  }

  .colorNegative {
    color: #ff0000; !important;
  }

  .colorNeutral {
    color: #ffffff;
  }
</style>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/theme/nova/formate.css">
	<link rel="stylesheet" type="text/css" href="styles/resource/css/login/main.css">
	<link rel="stylesheet" type="text/css" href="styles/resource/css/base/jquery.fancybox.css">
	<link rel="stylesheet" type="text/css" href="styles/resource/css/login/icon-font/style.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600" type="text/css">
	<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
	<title>{$gameName}</title>
	<meta name="keywords" content="pr0game, Steem, Browsergame, MMOSG, MMOG, Strategy, XNova, 2Moons, Space">
	<meta name="description" content="pr0game, ein freies Browser Game">
	<!-- open graph protocol -->
	<meta property="og:title" content="pr0game">
	<meta property="og:type" content="website">
	<meta property="og:description" content="pr0game, a free Browser Game.">
	<meta property="og:image" content="styles/resource/images/meta.png">
	<!--[if lt IE 9]>
	<script src="scripts/base/html5.js"></script>
	<![endif]-->
	<script src="scripts/base/jquery.js"></script>
	<script src="scripts/base/jquery.cookie.js"></script>
	<script src="scripts/base/jquery.fancybox.js"></script>
	<script src="scripts/login/main.js"></script>
	<script>{if isset($code)}var loginError = {$code|json_encode};{/if}</script>
	{block name="script"}{/block}
</head>
<body id="{$smarty.get.page|default:'overview'}" class="{$bodyclass}">
	<div id="page">
