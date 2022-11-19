{block name="title" prepend}{$LNG.lm_topkb}{/block}
{block name="content"}
<form name="stats" id="stats" method="post" action="">
	<table class="table569">
		<tr>
    <th colspan="4">{$LNG.tkb_top}</th>
		</tr>
		<tr>
			<td>
				<label for="memorial">{$LNG.tkb_memorial}</label> <select name="memorial" id="memorial" onchange="$('#stats').submit();">{html_options options=$Selectors.memorial selected=$memorial}</select>
				<label for="timeframe">{$LNG.tkb_timeframe}</label> <select name="timeframe" id="timeframe" onchange="$('#stats').submit();">{html_options options=$Selectors.timeframe selected=$timeframe}</select>
				<label for="diplomacy">{$LNG.tkb_diplomacy}</label> <select name="diplomacy" id="diplomacy" onchange="$('#stats').submit();">{html_options options=$Selectors.diplomacy selected=$diplomacy}</select>
				<label for="galaxy">{$LNG.tkb_galaxy}</label> <select name="galaxy" id="galaxy" onchange="$('#stats').submit();">{html_options options=$Selectors.galaxy selected=$galaxy}</select>
			</td>
		</tr>
	</table>
</form>
<table class="table569">
<tbody>
<tr>
    <th colspan="4">{$LNG.tkb_top}</th>
</tr>
<tr>
    <td colspan="4">{$LNG.tkb_gratz}</td>
</tr>
<tr>
    <td>{$LNG.tkb_platz}</td>
	<td>{$LNG.tkb_owners}</td>
    <td>{$LNG.tkb_datum}</td>
	<td>{$LNG.tkb_units}</td>
</tr>
{foreach $TopKBList as $row}
    <tr>
        <td>{$row@iteration}</td>
        <td><a href="game.php?page=raport&amp;raport={$row.rid}" target="_blank">
        {if $row.result == "a"}
        <span class="colorPositive">{$row.attacker}</span> VS <span class="colorNegative">{$row.defender}</span>
        {elseif $row.result == "r"}
        <span class="colorNegative">{$row.attacker}</span> VS <span class="colorPositive">{$row.defender}</span>
        {else}
        {$row.attacker} VS {$row.defender}
        {/if}
        </a></td>
        <td>{$row.date}</td>
        <td>{number_format($row.units, 0, ",", ".")}</td>
    </tr>
{/foreach}
<tr>
<td colspan="4">{$LNG.tkb_legende}<span class="colorPositive">{$LNG.tkb_gewinner}</span><span class="colorNegative">{$LNG.tkb_verlierer}</span></td></tr>
</tbody>
</table>
{/block}
