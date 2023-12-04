{block name="title" prepend}{$pageTitle}{/block}
{block name="content"}
{if isset($Info)}
<table>
	<tr>
		<td class="transparent" style="width:40%;font-size:22px;font-weight:bold;padding:10px 0 30px;color:{if $Raport.result == "a"}colorPositive{elseif $Raport.result == "r"}colorNegative{else}white{/if}">{$Info.0}</td>
		<td class="transparent" style="font-size:22px;font-weight:bold;padding:10px 0 30px;">VS</td>
		<td class="transparent" style="width:40%;font-size:22px;font-weight:bold;padding:10px 0 30px;color:{if $Raport.result == "r"}colorPositive{elseif $Raport.result == "a"}colorNegative{else}white{/if}">{$Info.1}</td>
	</tr>
</table>
{/if}
<div style="width:100%;text-align:center">
{if $Raport.mode == 1}{$LNG.sys_destruc_title}{else}{$LNG.sys_attack_title}{/if} {$Raport.time}:<br><br>
{foreach $Raport.rounds as $Round => $RoundInfo}
{if isset($Info) && $Round > 0 && !$RoundInfo@last}
	{continue}
{/if}
<table class="auto">
	<tr>
		{foreach $RoundInfo.attacker as $PlayerNr => $Player}
		{$PlayerInfo = $Raport.players[$Player.userID]}
		<td class="transparent">
			<table>
				<tr>
					<td>
						{$LNG.sys_attack_attacker_pos} {$PlayerInfo.name} {if isset($Info)}([XX:XX:XX]){else}([{$PlayerInfo.koords[0]}:{$PlayerInfo.koords[1]}:{$PlayerInfo.koords[2]}]{if isset($PlayerInfo.koords[3])} ({$LNG["type_planet_short_{$PlayerInfo.koords[3]}"]}){/if}){/if}
						{if !isset($Info)}<br>{$LNG.sys_ship_weapon} {$PlayerInfo.tech[0]}% - {$LNG.sys_ship_shield} {$PlayerInfo.tech[1]}% - {$LNG.sys_ship_armour} {$PlayerInfo.tech[2]}%{/if}


						<table>
						{if !empty($Player.ships)}
							<tr>
								<td class="transparent">{$LNG.sys_ship_type}</td>
								{foreach $Player.ships as $ShipID => $ShipData}
								<td class="transparent">{$LNG.shortNames.{$ShipID}}</td>
								{/foreach}
							</tr>
							<tr>
								<td class="transparent">{$LNG.sys_ship_count}</td>
								{foreach $Player.ships as $ShipID => $ShipData}
									<td class="transparent">{number_format($ShipData[0], 0, ",", ".")}
										{if $Round > 0 && $ShipData[0] - $Raport.rounds[0].attacker[$PlayerNr].ships[$ShipID][0]}
											<br><span style="color:#ee4d2e"> ({number_format(($ShipData[0] - $Raport.rounds[0].attacker[$PlayerNr].ships[$ShipID][0]), 0, ",", ".")})</span>
										{/if}
									</td>
								{/foreach}
							</tr>
							{if !isset($Info)}
								<tr>
									<td class="transparent">{$LNG.sys_ship_weapon}</td>
									{foreach $Player.ships as $ShipID => $ShipData}
									<td class="transparent">{number_format($ShipData[1], 0, ",", ".")}</td>
									{/foreach}
								</tr>
								<tr>
									<td class="transparent">{$LNG.sys_ship_shield}</td>
									{foreach $Player.ships as $ShipID => $ShipData}
									<td class="transparent">{number_format($ShipData[2], 0, ",", ".")}</td>
									{/foreach}
								</tr>
								<tr>
									<td class="transparent">{$LNG.sys_ship_armour}</td>
									{foreach $Player.ships as $ShipID => $ShipData}
									<td class="transparent">{number_format($ShipData[3], 0, ",", ".")}</td>
									{/foreach}
								</tr>
							{/if}
						{else}
							<tr>
								<td class="transparent">
									<br>{$LNG.sys_destroyed}<br><br>
								</td>
							</tr>
						{/if}
						</table>
					</td>
				</tr>
			</table>
		</td>
		{/foreach}
	</tr>
</table>
<table class="auto">
	<tr>
		{foreach $RoundInfo.defender as $PlayerNr => $Player}
		{$PlayerInfo = $Raport.players[$Player.userID]}
		<td class="transparent">
			<table>
				<tr>
					<td>
						{$LNG.sys_attack_defender_pos} {$PlayerInfo.name} {if isset($Info)}([XX:XX:XX]){else}([{$PlayerInfo.koords[0]}:{$PlayerInfo.koords[1]}:{$PlayerInfo.koords[2]}]{if isset($PlayerInfo.koords[3])} ({$LNG["type_planet_short_{$PlayerInfo.koords[3]}"]}){/if}){/if}
						{if !isset($Info)}<br>{$LNG.sys_ship_weapon} {$PlayerInfo.tech[0]}% - {$LNG.sys_ship_shield} {$PlayerInfo.tech[1]}% - {$LNG.sys_ship_armour} {$PlayerInfo.tech[2]}%{/if}

						<table>
						{if !empty($Player.ships)}
							<tr>
								<td class="transparent">{$LNG.sys_ship_type}</td>
								{foreach $Player.ships as $ShipID => $ShipData}
								<td class="transparent">{$LNG.shortNames.{$ShipID}}</td>
								{/foreach}
							</tr>
							<tr>
								<td class="transparent">{$LNG.sys_ship_count}</td>
								{foreach $Player.ships as $ShipID => $ShipData}
								<td class="transparent">{number_format($ShipData[0], 0, ",", ".")}
									{if $Round > 0 && $ShipData[0] - $Raport.rounds[0].defender[$PlayerNr].ships[$ShipID][0]}
										<br><span style="color:#ee4d2e"> ({number_format(($ShipData[0] - $Raport.rounds[0].defender[$PlayerNr].ships[$ShipID][0]), 0, ",", ".")})</span>
									{/if}
								</td>
								{/foreach}
							</tr>
							{if !isset($Info)}
								<tr>
									<td class="transparent">{$LNG.sys_ship_weapon}</td>
									{foreach $Player.ships as $ShipID => $ShipData}
									<td class="transparent">{number_format($ShipData[1], 0, ",", ".")}</td>
									{/foreach}
								</tr>
								<tr>
									<td class="transparent">{$LNG.sys_ship_shield}</td>
									{foreach $Player.ships as $ShipID => $ShipData}
									<td class="transparent">{number_format($ShipData[2], 0, ",", ".")}</td>
									{/foreach}
								</tr>
								<tr>
									<td class="transparent">{$LNG.sys_ship_armour}</td>
									{foreach $Player.ships as $ShipID => $ShipData}
									<td class="transparent">{number_format($ShipData[3], 0, ",", ".")}</td>
									{/foreach}
								</tr>
							{/if}
						{else}
							<tr>
								<td class="transparent">
									<br>{$LNG.sys_destroyed}<br><br>
								</td>
							</tr>
						{/if}
						</table>
					</td>
				</tr>
			</table>
		</td>
		{/foreach}
	</tr>
</table>
{if !$RoundInfo@last}
{$LNG.fleet_attack_1} {number_format($RoundInfo.info[0], 0, ",", ".")} {$LNG.fleet_attack_2} {number_format($RoundInfo.info[3], 0, ",", ".")} {$LNG.damage}<br>
{$LNG.fleet_defs_1} {number_format($RoundInfo.info[2], 0, ",", ".")} {$LNG.fleet_defs_2} {number_format($RoundInfo.info[1], 0, ",", ".")} {$LNG.damage}<br><hr>
{/if}
{/foreach}
<br><br>
{if $Raport.result == "a"}
{$LNG.sys_attacker_won}<br>
{$LNG.sys_stealed_ressources} {foreach $Raport.steal as $elementID => $amount}{number_format($amount, 0, ",", ".")} {$LNG.tech.$elementID}{if ($amount@index + 2) == count($Raport.steal)} {$LNG.sys_and} {elseif !$amount@last}, {/if}{/foreach}
{elseif $Raport.result == "r"}
{$LNG.sys_defender_won}
{else}
{$LNG.sys_both_won}
{/if}
<br><br>
{$LNG.sys_attacker_lostunits} {number_format($Raport['units'][0], 0, ",", ".")} {$LNG.sys_units}<br>
{$LNG.sys_defender_lostunits} {number_format($Raport['units'][1], 0, ",", ".")} {$LNG.sys_units}<br>
{$LNG.debree_field_1} {foreach $Raport.debris as $elementID => $amount}{number_format($amount, 0, ",", ".")} {$LNG.tech.$elementID}{if ($amount@index + 2) == count($Raport.debris)} {$LNG.sys_and} {elseif !$amount@last}, {/if}{/foreach}{$LNG.debree_field_2}<br><br>
{if $Raport.mode == 1}
	{* Destruction *}
	{if $Raport.moon.moonDestroySuccess == -1}
		{* Attack not win *}
		{$LNG.sys_destruc_stop}<br>
	{else}
		{* Attack win *}
		{sprintf($LNG.sys_destruc_lune, "{$Raport.moon.moonDestroyChance}")}<br>{$LNG.sys_destruc_mess1}
		{if $Raport.moon.moonDestroySuccess == 1}
			{* Destroy success *}
			{$LNG.sys_destruc_reussi}
		{elseif $Raport.moon.moonDestroySuccess == 0}
			{* Destroy failed *}
			{$LNG.sys_destruc_null}
		{/if}
		<br>
		{sprintf($LNG.sys_destruc_rip, "{$Raport.moon.fleetDestroyChance}")}
		{if $Raport.moon.fleetDestroySuccess == 1}
			{* Fleet destroyed *}
			<br>{$LNG.sys_destruc_echec}
		{/if}
	{/if}
{else}
	{* Normal Attack *}
    {$LNG.sys_moonproba} {$Raport.moon.moonChance} %{if !empty($Raport.moon.additionalChance) && $Raport.moon.additionalChance != 0} + {$Raport.moon.additionalChance} %{/if}<br>
	{if !empty($Raport.moon.moonName)}
		{$LNG.sys_moonbuilt}
	{/if}
{/if}

{$Raport.additionalInfo}

{if $Raport.wreckfield_created}
	<br>
  {$LNG.sys_wreckfield}
	<br>
{/if}

{if !empty($Raport.repaired)}
	<br>
	{$lastRound = count($Raport.rounds)-1}
	{$lastDefender = count($Raport.rounds[0].defender)-1}
	<table class="auto">
	<tr>
		<td>
			<table>
				<tr>
					<th colspan="4" class="transparent">{$LNG.sys_def_rebuild}</th>
				</tr>
				<tr>
					<th class="transparent">{$LNG.sys_ship_type}</th>
					<th class="transparent">{$LNG.sys_ship_repaired}</th>
					<th class="transparent">%</th>
					<th class="transparent">{$LNG.sys_destroyed}</th>
				</tr>
				{foreach $Raport.repaired as $ShipID => $ShipData}
					<tr>
						<td class="transparent">{$LNG.shortNames.{$ShipID}}</td>
						<td class="transparent">{$ShipData.units} /
							{if empty($Raport.rounds[$lastRound].defender[$lastDefender].ships) || empty($Raport.rounds[$lastRound].defender[$lastDefender].ships[$ShipID])}
								{$Raport.rounds[0].defender[$lastDefender].ships[$ShipID][0]}
							{else}
								{$Raport.rounds[0].defender[$lastDefender].ships[$ShipID][0]-$Raport.rounds[$lastRound].defender[$lastDefender].ships[$ShipID][0]}
							{/if}
						</td>
						<td class="transparent">{$ShipData.percent|string_format:"%.1f"}</td>
						<td class="transparent">
							{$dest = 0}
							{if empty($Raport.rounds[$lastRound].defender[$lastDefender].ships) || empty($Raport.rounds[$lastRound].defender[$lastDefender].ships[$ShipID])}
								{$dest = $Raport.rounds[0].defender[$lastDefender].ships[$ShipID][0]-{$ShipData.units}}
							{else}
								{$dest = $Raport.rounds[0].defender[$lastDefender].ships[$ShipID][0]-$Raport.rounds[$lastRound].defender[$lastDefender].ships[$ShipID][0]-{$ShipData.units}}
							{/if}
							{if $dest > 0}
								<span style="color:#ee4d2e">-{$dest}</span>
							{else}
								{$dest}
							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
		</td>
	</tr>
	</table>
{/if}

{if !empty($Raport.wreckfield)}
  <br>
  <table class="auto">
	<tr>
		<td>
			<table>
				<tr>
					<th colspan="2" class="transparent">{$LNG.sys_wreckfield_added}</th>
				</tr>
				{foreach $Raport.wreckfield as $ShipID => $amount}
					<tr>
						<td class="transparent">{$LNG.shortNames.{$ShipID}}</td>
						<td class="transparent">{$amount}</td>
					</tr>
				{/foreach}
			</table>
		</td>
	</tr>
	</table>
{/if}
</div>
{/block}
