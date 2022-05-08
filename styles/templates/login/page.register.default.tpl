{block name="title" prepend}{$LNG.siteTitleRegister}{/block}
{block name="content"}
<div id="registerFormWrapper">
<form id="registerForm" method="post" action="index.php?page=register" data-action="index.php?page=register">
<input type="hidden" value="send" name="mode">
<input type="hidden" value="{$referralData.id}" name="referralID">
	<div class="rowForm">
		<label for="universe">{$LNG.universe}</label>
		<select name="uni" id="universe" class="changeAction">{html_options options=$universeSelect selected=$UNI}</select>
		{if !empty($error.uni)}<span class="error errorUni"></span>{/if}
	</div>
	<div class="rowForm">
		<label for="username">{$LNG.registerUsername}</label>
		<input type="text" class="input" name="username" id="username" maxlenght="32">
		{if !empty($error.username)}<span class="error errorUsername"></span>{/if}
		<span class="inputDesc">{$LNG.registerUsernameDesc}</span>
	</div>
	<div class="rowForm">
		<label for="password">{$LNG.registerPassword}</label>
		<input type="password" class="input" name="password" id="password">
		{if !empty($error.password)}<span class="error errorPassword"></span>{/if}
		<span class="inputDesc">{$registerPasswordDesc}</span>
	</div>
	<div class="rowForm">
		<label for="passwordReplay">{$LNG.registerPasswordReplay}</label>
		<input type="password" class="input" name="passwordReplay" id="passwordReplay">
		{if !empty($error.passwordReplay)}<span class="error errorPasswordReplay"></span>{/if}
		<span class="inputDesc">{$LNG.registerPasswordReplayDesc}</span>
	</div>
	<div class="rowForm">
		<label for="email">{$LNG.registerEmail}</label>
		<input type="email" class="input" name="email" id="email">
		{if !empty($error.email)}<span class="error errorEmail"></span>{/if}
		<span class="inputDesc">{$LNG.registerEmailDesc}</span>
	</div>
	<div class="rowForm">
		<label for="emailReplay">{$LNG.registerEmailReplay}</label>
		<input type="email" class="input" name="emailReplay" id="emailReplay">
		{if !empty($error.emailReplay)}<span class="error errorEmailReplay"></span>{/if}
		<span class="inputDesc">{$LNG.registerEmailReplayDesc}</span>
	</div>
	{if count($languages) > 1}
	<div class="rowForm">
		<label for="language">{$LNG.registerLanguage}</label>
		<select name="lang" id="language">{html_options options=$languages selected=$lang}</select>
		{if !empty($error.language)}<span class="error errorLanguage"></span>{/if}
		<div class="clear"></div>
	</div>
	{/if}
	{if !empty($referralData.name)}
	<div class="rowForm">
		<label for="language">{$LNG.registerReferral}</label>
		<span class="text">{$referralData.name}</span>
		{if !empty($error.language)}<span class="error errorLanguage"></span>{/if}
		<div class="clear"></div>
	</div>
	{/if}
	<div class="rowForm">
		<label for="rules">{$LNG.registerRules}</label>
		<input type="checkbox" name="rules" id="rules" value="1">
		{if !empty($error.rules)}<span class="error errorRules"></span>{/if}
		<span class="inputDesc">{$registerRulesDesc}</span>
	</div>
	<div class="rowForm">
		<input type="submit" class="submitButton" value="{$LNG.buttonRegister}">
	</div>
</form>
{/block}
{block name="script" append}
<link rel="stylesheet" type="text/css" href="styles/resource/css/login/register.css?v={$REV}">
<script type="text/javascript" src="scripts/login/register.js"></script>
{/block}
