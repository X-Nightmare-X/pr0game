{block name="title" prepend}Team{/block}
{nocache}
{block name="content"}
<style>
	content {
		max-width: unset;
	}
</style>
<h1>{$LNG.team}</h1>
{* <h3>{$LNG.teamAktiv}</h3> *}
<h4>{$LNG.kontakt}</h4>
<table>
	<tr>
		<th colspan="2">{$LNG.teamIngameName}</th>
		<th>{$LNG.teamDiscordName}</th>
		<th>{$LNG.teamRole}</th>
		<th>{$LNG.teamPlayer}</th>
		<th>{$LNG.teamCommunity}</th>
		<th>{$LNG.teamAdmin}</th>
		<th>{$LNG.teamDatabase}</th>
		<th>{$LNG.teamTickets}</th>
		<th>{$LNG.teamScripte}</th>
	</tr>
	<tr>
		{if $idDawnOfTheUwe != 0}
			<td><a href="#" onclick="return Dialog.PM({$idDawnOfTheUwe});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
			<td>DawnOfTheUwe</td>
		{else}
			<td></td>
			<td>DawnOfTheUwe</td>
		{/if}
		<td>DawnOfTheUwe</td>
		<td>{$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		{if $idHackbrett != 0}
			<td><a href="#" onclick="return Dialog.PM({$idHackbrett});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
			<td>Hackbrett</td>
		{else}
			<td></td>
			<td>Hackbrett</td>
		{/if}
		<td>Hackbrett</td>
		<td>{$LNG.teamMod}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>		
		{if $idAdman != 0}
			<td><a href="#" onclick="return Dialog.PM({$idAdman});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
			<td>Adman</td>
		{else}
			<td></td>
			<td>Adman</td>
		{/if}
		<td>Hyman</td>
		<td>{$LNG.teamDev}, {$LNG.teamEmergency}, {$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
	</tr>
	<tr>
		{if $idMasterspiel != 0}
			<td><a href="#" onclick="return Dialog.PM({$idMasterspiel});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
			<td>Masterspiel</td>
		{else}
			<td></td>
			<td>Masterspiel</td>
		{/if}
		<td>Masterspiel</td>
		<td>{$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		{if $idReflexrecon != 0}
			<td><a href="#" onclick="return Dialog.PM({$idReflexrecon});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
			<td>reflexrecon</td>
		{else}
			<td></td>
			<td>reflexrecon</td>
		{/if}
		<td>reflexrecon</td>
		<td>{$LNG.teamDev}, {$LNG.teamEmergency}, {$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		{if $idSchmopfi != 0}
			<td><a href="#" onclick="return Dialog.PM({$idSchmopfi});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
			<td>Schmopfi</td>
		{else}
			<td></td>
			<td>Schmopfi</td>
		{/if}
		<td>Schmopfi</td>
		<td>{$LNG.teamCommunity}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		{if $idTimoKa != 0}
			<td><a href="#" onclick="return Dialog.PM({$idTimoKa});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
			<td>Timo_Ka</td>
		{else}
			<td></td>
			<td>Timo_Ka</td>
		{/if}
		<td>Timo_Ka</td>
		<td>{$LNG.teamDev}, {$LNG.teamEmergency}, {$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
	</tr>
</table>

<h3>{$LNG.teamAlumni}</h3>
<p>{$LNG.teamAlumniLong}: atain, ava, Axel auf dem Floß, Eichhorn, Captain Mrgl, Fionera, Klarname, Rosenreemann & Slippy</p>

<h4>{$LNG.teamLegend}:</h4>
<table>
	<tr>
		<td>
			{$LNG.teamMod}
		</td>
		<td>
			{$LNG.teamModLong}
		</td>
	</tr>
	<tr>
		<td>
			{$LNG.teamEmergency}
		</td>
		<td>
			{$LNG.teamEmergencyLong}
		</td>
	</tr>
	<tr>
		<td>
			{$LNG.teamCommunity}
		</td>
		<td>
			{$LNG.teamCommunityLong}
		</td>
	</tr>
	<tr>
		<td>
			{$LNG.teamDev}
		</td>
		<td>
			{$LNG.teamDevLong}
		</td>
	</tr>
	<tr>
		<td>
			{$LNG.teamAdmin}
		</td>
		<td>
			{$LNG.teamAdminLong}
		</td>
	</tr>
	<tr>
		<td>
			{$LNG.teamTickets}
		</td>
		<td>
			{$LNG.teamTicketsLong}
		</td>
	</tr>
	<tr>
		<td>
			{$LNG.teamScripte}
		</td>
		<td>
			{$LNG.teamScripteLong}
		</td>
	</tr>
</table>
{/block}
{/nocache}