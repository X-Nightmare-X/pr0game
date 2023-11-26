{block name="title" prepend}Team{/block}
{nocache}
{block name="content"}
<h1>{$LNG.team}</h1>
<h3>{$LNG.teamAktiv}</h3>
<table>
	<tr>
		<th colspan="2">{$LNG.teamIngameName}</th>
		<th>{$LNG.teamDiscordName}</th>
		<th>{$LNG.teamRole}</th>
		<th>{$LNG.teamPlayer}</th>
		<th>{$LNG.teamAdmin}</th>
		<th>{$LNG.teamDatabase}</th>
		<th>{$LNG.teamTickets}</th>
		<th>{$LNG.teamScripte}</th>
	</tr>
	<tr>
		<td><a href="#" onclick="return Dialog.PM({$idDawnOfTheUwe});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
		<td>DawnOfTheUwe</td>
		<td>DawnOfTheUwe</td>
		<td>{$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		<td colspan="2">Hackbrett</td>
		<td>Hackbrett</td>
		<td>{$LNG.teamMod}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		<td><a href="#" onclick="return Dialog.PM({$idAdman});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
		<td>Adman</td>
		<td>Hyman</td>
		<td>{$LNG.teamDev}, {$LNG.teamEmergency}, {$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
	</tr>
	<tr>
		<td><a href="#" onclick="return Dialog.PM({$idMasterspiel});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
		<td>Masterspiel</td>
		<td>Masterspiel</td>
		<td>{$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		<td><a href="#" onclick="return Dialog.PM({$idReflexrecon});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
		<td>reflexrecon</td>
		<td>reflexrecon</td>
		<td>{$LNG.teamDev}, {$LNG.teamEmergency}, {$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamNo}</td>
	</tr>
	<tr>
		<td><a href="#" onclick="return Dialog.PM({$idTimoKa});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
		<td>Timo_Ka</td>
		<td>Timo_Ka</td>
		<td>{$LNG.teamDev}, {$LNG.teamEmergency}, {$LNG.teamMod}</td>
		<td>{$LNG.teamNo}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
		<td>{$LNG.teamYes}</td>
	</tr>
</table>

<h3>{$LNG.teamAlumni}</h3>
<p>{$LNG.teamAlumniLong}: atain, ava, Axel auf dem Floß, Eichhorn, Captain Mrgl, Fionera, Klarname, klk & Slippy</p>

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