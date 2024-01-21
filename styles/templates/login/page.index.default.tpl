{block name="title" prepend}{$LNG.siteTitleIndex}{/block}
{block name="content"}
<section>
	<h1>{$descHeader}</h1>
	<p class="desc">{$descText}</p>
	<p class="desc"><ul id="desc_list">{foreach $gameInformations as $info}<li>{$info}</li>{/foreach}</ul></p>

	</p>
</section>
<section>
	<div class="contentbox">
		<h1>{$LNG.loginHeader}</h1>
		<form id="login" name="login" action="index.php?page=login" data-action="index.php?page=login" method="post">
			<div class="row">
				<select name="uni" id="universe" class="changeAction">{html_options options=$universeSelect selected=$universeSelected}</select>
				<input name="username" id="username" type="text" placeholder="{$LNG.loginUsername}" maxlength="64">
				<input name="password" id="password" type="password" placeholder="{$LNG.loginPassword}">
				<input type="submit" value="{$LNG.loginButton}">
			</div>
		</form>

		<!-- <a href="/index.php?page=register"><input value="{$LNG.buttonRegister}"></a> -->
		{if $mailEnable}
			<a href="index.php?page=lostPassword" style="text-decoration:none;"><input type="submit" value="{$LNG.buttonLostPassword}"></a>
		{/if}
		<br><span class="small">{$loginInfo}</span>
	</div>
</section>
<section>
	<div class="button-box">
		<div class="button-box-inner">
			<div class="button-important">
				<a href="index.php?page=register">
					<span class="button-left"></span>
					<span class="button-center">{$LNG.buttonRegister}</span>
					<span class="button-right"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="button-box">
		<div class="button-box-inner">
			<div class="button multi">
				<a href="index.php?page=battleHall" target="_blank">
					<span class="button-left"></span>
					<span class="button-center">{$LNG.menu_battlehall}</span>
					<span class="button-right"></span>
				</a>
			</div>
      		<div class="button multi">
				<a href="index.php?page=screens" target="_blank">
					<span class="button-left"></span>
					<span class="button-center">{$LNG.buttonScreenshot}</span>
					<span class="button-right"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="button-box">
		<div class="button-box-inner">
			<div class="button multi">
				<a href="https://discord.gg/jhYYN3yuat" target="_blank">
					<span class="button-left"></span>
					<span class="button-center">Discord</span>
					<span class="button-right"></span>
				</a>
			</div>
      		<div class="button multi">
				<a href="https://wiki.pr0game.com" target="_blank">
					<span class="button-left"></span>
					<span class="button-center">Wiki</span>
					<span class="button-right"></span>
				</a>
			</div>
		</div>
	</div>
</section>
{if $countcaptchakey!=0}
	<p>{$LNG['disclamerRecaptcha']}</p>
{/if}
{/block}
{block name="script" append}
	<script>{if $code}alert({$code|json_encode});{/if}$(function() { $('#username').focus(); });</script>
{/block}
