{block name="title" prepend}{$LNG.lm_research}{/block}
{block name="content"}
	{include file='shared.messages.tpl'}
	{if !empty($Queue)}
		<div id="buildlist" class="infos1">
			{foreach $Queue as $List}
				{$ID = $List.element}
				<div class="buildb">

					{if isset($ResearchList[$List.element])}
						{$CQueue = $ResearchList[$List.element]}
					{/if}

					{$List@iteration}.:
					{if isset($CQueue) && $CQueue.maxLevel != $CQueue.level && !$IsFullQueue && $CQueue.buyable}
						<form action="game.php?page=research" method="post" class="build_form">
							<input type="hidden" name="cmd" value="insert">
							<input type="hidden" name="tech" value="{$ID}">
							<button type="submit" class="build_submit onlist">{$LNG.tech.{$ID}} {$List.level}{if !empty($List.planet)} @ {$List.planet}{/if}</button>
						</form>
					{else}
						{$LNG.tech.{$ID}} {$List.level}{if !empty($List.planet)} @ {$List.planet}{/if}
					{/if}

					{if $List@first}
						<br><br>
						<div id="progressbar" data-time="{$List.resttime}"></div>
					</div>
					<div class="bulida">
						<div id="time" data-time="{$List.time}"><br></div>
						{if $umode == 0}
							<form action="game.php?page=research" method="post" class="build_form">
								<input type="hidden" name="cmd" value="cancel">
								<button type="submit" class="build_submit onlist">{$LNG.bd_cancel}</button>
							</form>
						{else}
							-
						{/if}
					{else}
					</div>
					<div class="bulida">
						{if $umode == 0}
							<form action="game.php?page=research" method="post" class="build_form">
								<input type="hidden" name="cmd" value="remove">
								<input type="hidden" name="listid" value="{$List@iteration}">
								<button type="submit" class="build_submit onlist">{$LNG.bd_cancel}</button>
							</form>
						{else}
							-
						{/if}
					{/if}
					<br><span class="colorPositive" data-time="{$List.endtime}" data-umode="{$umode}" class="timer">{if $umode == 0}{$List.display}{else}{$LNG.bd_paused}{/if}</span>
				</div>
			{/foreach}
		</div>
	{/if}

	{if $IsLabinBuild}<div class="hidden-div">{$LNG.bd_building_lab}</div>{/if}
	<div>
		<div class="planeto">
			<button id="lab1">{$LNG.fm_imperial}</button> | <button id="lab2">{$LNG.fm_military}</button> | <button id="lab3">{$LNG.fm_engines}</button> | <button id="lab4">{$LNG.fm_mining}</button> | <button id="lab5" class="selected">{$LNG.fm_all}</button>
		</div>

		{foreach $ResearchList as $ID => $Element}
			<div class="infos {if $Element.fade}unavailable{/if}" id="t{$ID}">
				<div class="buildn">
					<a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>{if $Element.level != 0} ({$LNG.bd_lvl} {$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if}){/if}
				</div>
				<div class="buildl">
					<a href="#" onclick="return Dialog.info({$ID})">
						<img style="float: left;" src="{$dpath}gebaeude/{$ID}.gif" width="120" height="120">
					</a>
					{if !empty($Element.requirements)}
						{$LNG.tt_requirements}: </br>
						{foreach $Element.requirements as $requireID => $NeedLevel}
						  	<a href="#" onclick="return Dialog.info({$requireID})">
							<span class="{if $NeedLevel.own < $NeedLevel.count}colorNeutral{else}colorPositive{/if}">
								{$LNG.tech.$requireID} ({$LNG.tt_lvl} {$NeedLevel.own}/{$NeedLevel.count})
							</span>
						  	</a>{if !$NeedLevel@last}<br>{/if}
						{/foreach}
					{else}
						{$LNG.bd_remaining}:<br>
						{foreach $Element.costOverflow as $ResType => $ResCount}
							<a href='#' onclick='return Dialog.info({$ResType})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$ResType}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$ResType}.{if $ResType >=600 && $ResType <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$ResType}</td></tr></table></tr></table>">{$LNG.tech.{$ResType}}</a>: <span style="font-weight:700">{number_format($ResCount, 0, ",", ".")}</span><br>
						{/foreach}
						{if $Element.timetobuild!= 0}<div>{$LNG['whenbuildable']}: <span style="font-weight: bold" class="buildcountdown" timestamp="{$Element.timetobuild}"></span></div>{/if}
					{/if}
				</div>

				<div class="buildl">
					<span>
						{foreach $Element.costResources as $RessID => $RessAmount}
							<a href='#' onclick='return Dialog.info({$RessID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$RessID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$RessID}</td></tr></table></tr></table>">{$LNG.tech.{$RessID}}</a>: <b><span class="{if $Element.costOverflow[$RessID] == 0}colorPositive{else}colorNegative{/if}">{number_format($RessAmount, 0, ",", ".")}</span></b>
						{/foreach}
					</span>
					<br><br>
					{if $vacation}
						<span class="colorNeutral">{$LNG.op_options_vacation_activated}</span>
					{elseif !empty($Element.requirements)}
						<span class="colorNeutral">{$LNG.bd_requirements}</span>
					{elseif $Element.maxLevel == $Element.levelToBuild}
						<span class="colorNeutral">{$LNG.bd_maxlevel}</span>
					{elseif $IsLabinBuild}
						<span class="colorNeutral">{$LNG.sys_buildlist_fail}</span>
					{elseif $IsFullQueue}
						<span class="colorNeutral">{$LNG.bd_researclist_full}</span>
					{elseif !$Element.buyable}
						<span class="colorNeutral">{$LNG.bd_remaining}</span>
					{else}
						<form action="game.php?page=research" method="post" class="build_form">
							<input type="hidden" name="cmd" value="insert">
							<input type="hidden" name="tech" value="{$ID}">
							<button type="submit" class="colorPositive build_submit">{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_tech}{else}{$LNG.bd_tech_next_level}{$Element.levelToBuild + 1}{/if}</button>
						</form>
					{/if}
					</br>
					{$LNG.fgf_time}
					<span class="statictime" timestamp="{$Element.elementTime}"></span>
				</div>
			</div>
		{/foreach}
	</div>
{/block}
{block name="script" append}
	{if !empty($Queue)}
		<script src="scripts/game/research.js"></script>
	{/if}
	<script>
		$(function() {
			$("#lab1").on('click', function() {
				$(".infos").hide();
				$("#t108, #t113, #t114, #t123, #t124").show();
				$("#lab2, #lab3, #lab4, #lab5").removeClass("selected");

				$(this).addClass("selected");
			});
		});
		$(function() {
			$("#lab2").on('click', function() {
				$(".infos").hide();
				$("#t109, #t106, #t110, #t111, #t120, #t121, #t122, #t199").show();
				$("#lab1, #lab3, #lab4, #lab5").removeClass("selected");

				$(this).addClass("selected");
			});
		});
		$(function() {
			$("#lab3").on('click', function() {
				$(".infos").hide();
				$("#t114, #t115, #t117, #t118").show();
				$("#lab1, #lab2, #lab4, #lab5").removeClass("selected");

				$(this).addClass("selected");
			});
		});
		$(function() {
			$("#lab4").on('click', function() {
				$(".infos").hide();
				$("#t131, #t132, #t133").show();
				$("#lab1, #lab2, #lab3, #lab5").removeClass("selected");

				$(this).addClass("selected");
			});
		});
		$(function() {
			$("#lab5").on('click', function() {
				$(".infos").show();

				$("#lab1, #lab2, #lab3, #lab4").removeClass("selected");

				$(this).addClass("selected");
			});
		});
	</script>
{/block}