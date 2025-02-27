{block name="title" prepend}{$LNG.lm_alliance}{/block}
{block name="content"}
	<style>
	.tooltip1 {
		position: relative;
		display: inline-block;
		border-bottom: 1px dotted black;
	}

	.tooltip1 .tooltiptext {
		visibility: hidden;
		width: 120px;
		background-color: black;
		color: #fff;
		text-align: center;
		border-radius: 6px;
		padding: 5px 0;
		position: absolute;
		z-index: 1;
		top: 150%;
		left: 50%;
		margin-left: -60px;
	}

	.tooltip1 .tooltiptext::after {
		content: "";
		position: absolute;
		bottom: 100%;
		left: 50%;
		margin-left: -5px;
		border-width: 5px;
		border-style: solid;
		border-color: transparent transparent black transparent;
	}

	.tooltip1:hover .tooltiptext {
		visibility: visible;
	}
	</style>

	<table class="table519">
	<tr>
		<th colspan="5">{sprintf($LNG.al_request_from,$name)} {$apply_time}</th>
	</tr>
	<tr>
		<td colspan="2">{$LNG.pl_name}</td>
		<td colspan="3">{$name}</td>
	</tr>
	<tr>
		<td colspan="2">{$LNG.pl_homeplanet}</td>
		<td colspan="3">{$homeplanet} <a href="#" onclick="parent.location = 'game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}';return false;">[{$galaxy}:{$system}:{$planet}]</a></td>
	</tr>
	<tr>
		<td colspan="2">{$LNG.al_request_register_time}</td>
		<td colspan="3">{$register_time}</td>
	</tr>
	<tr>
		<td colspan="2">{$LNG.al_request_last_onlinetime}</td>
		<td colspan="3">{$onlinetime}</td>
	</tr>
	<tr>
		<th colspan="2" style="text-align:center;">&nbsp;</th>
		<th colspan="2" style="text-align:center;">{$LNG.pl_points}</th>
		<th colspan="1" style="text-align:center;">{$LNG.pl_range}</th>
	</tr>
	<tr>
		{if $showBuild == true}
			<td></td>
			<td>{$LNG.pl_builds}</td>
			<td>{$build_points}</td>
			<td>{$build_percent}%</td>
			<td>{$build_rank}</td>
		{else}
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level2}</span></div></td>
			<td colspan="1">{$LNG.pl_builds}</td>
			<td colspan="3">-</td>
		{/if}
	</tr>
	<tr>
		{if $showTech == true}
			<td></td>
			<td>{$LNG.pl_tech}</td>
			<td>{$tech_points}</td>
			<td>{$tech_percent}%</td>
			<td>{$tech_rank}</td>
		{else}
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level4}</span></div></td>
			<td colspan="1">{$LNG.pl_tech}</td>
			<td colspan="3">-</td>
		{/if}
	</tr>
	<tr>
		{if $showFleet == true}
			<td></td>
			<td>{$LNG.pl_fleet}</td>
			<td>{$fleet_points}</td>
			<td>{$fleet_percent}%</td>
			<td>{$fleet_rank}</td>
		{else}
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td colspan="1">{$LNG.pl_fleet}</td>
			<td colspan="3">-</td>
		{/if}
	</tr>
	<tr>
		{if $showDef == true}
			<td></td>
			<td>{$LNG.pl_def}</td>
			<td>{$defs_points}</td>
			<td>{$defs_percent}%</td>
			<td>{$defs_rank}</td>
		{else}
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td colspan="1">{$LNG.pl_def}</td>
			<td colspan="3">-</td>
		{/if}
	</tr>
	<tr>
		<td colspan="2">{$LNG.pl_total}</td>
		<td colspan="1">{$total_points}</td>
		<td colspan="1">100%</td>
		<td colspan="1">{$total_rank}</td>
		<tr>
		<th colspan="5">{$LNG.pl_fightstats}</th>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td>{$LNG.pl_fights}</td>
		<td colspan="2">{$LNG.pl_fprocent}</td>
	</tr>
	{if $showBattle == true}
		<tr>
			<td></td>
			<td>{$LNG.pl_fightwon}</td>
			<td>{$wons}</td>
			<td colspan="2">{$siegprozent}%</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_fightdraw}</td>
			<td>{$draws}</td>
			<td colspan="2">{$drawsprozent}%</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_fightlose}</td>
			<td>{$loos}</td>
			<td colspan="2">{$loosprozent}%</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_totalfight}</td>
			<td>{$totalfights}</td>
			<td colspan="2">100%</td>
		</tr>
		<tr>
			<th colspan="5">{$playerdestory}:</th>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_unitsshot}</td>
			<td colspan="4">{$desunits}</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_unitslose}</td>
			<td colspan="3">{$lostunits}</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_dermetal}</td>
			<td colspan="3">{$kbmetal}</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_dercrystal}</td>
			<td colspan="3">{$kbcrystal}</td>
		</tr>
		<tr>
			<th colspan="5">{$realdestory}:</th>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_unitsshot}</td>
			<td colspan="3">{$realdesunits}</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_unitslose}</td>
			<td colspan="3">{$reallostunits}</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_dermetal}</td>
			<td colspan="3">{$realmetal}</td>
		</tr>
		<tr>
			<td></td>
			<td>{$LNG.pl_dercrystal}</td>
			<td colspan="3">{$realcrystal}</td>
		</tr>
	{else}
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td colspan="1">{$LNG.pl_fightwon}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td colspan="1">{$LNG.pl_fightdraw}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td colspan="1">{$LNG.pl_fightlose}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td colspan="1">{$LNG.pl_totalfight}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<th colspan="5">{$playerdestory}:</th>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_unitsshot}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_unitslose}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_dermetal}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_dercrystal}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<th colspan="5">{$realdestory}:</th>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_unitsshot}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_unitslose}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_dermetal}</td>
			<td colspan="3">-</td>
		</tr>
		<tr>
			<td><div class="tooltip1"><img src="./styles/resource/images/icons/lock.png" height="10" width="10"><span class="tooltiptext">{$LNG.spytech_playercard.level6}</span></div></td>
			<td>{$LNG.pl_dercrystal}</td>
			<td colspan="3">-</td>
		</tr>
	{/if}
	{if !empty($applyDetail.text)}
	<tr>
		<th colspan="5">{$LNG.al_message}</th>
	</tr>
	<tr>
		<td colspan="5">{$applyDetail.text}</td>
	</tr>
	{/if}
	</table>
<br>
<form action="game.php?page=alliance&amp;mode=admin&amp;action=sendAnswerToApply&amp;id={$applyDetail.applyID}" method="post">
<table class="table519">
	<tr>
		<th colspan="4"><label for="message">{$LNG.al_reply_to_request}</label></th>
	</tr>
	<tr>
		<td colspan="4"><textarea name="text" cols="40" rows="10" class="tinymce" id="message"></textarea></td>
	</tr>
	<tr>

		<td colspan="4"><input hidden name="answer"> <button  value="yes" type="submit">{$LNG.al_acept_request}</button> <button  value="no"   type="submit">{$LNG.al_decline_request}</button></td>
	</tr>
	</table>
  <script>
    function answer(){
      document.getElementsByName("answer")[0].value=this.value
    }
    document.querySelector("button[value='yes']").onclick=answer;
    document.querySelector("button[value='no']").onclick=answer;
  </script>
{/block}
