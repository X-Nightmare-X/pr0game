{block name="title" prepend}{$LNG.siteTitleBattleHall}{/block}
{block name="content"}
{if $isMultiUniverse}<p>
{html_options options=$universeSelect selected=$UNI class="changeUni" id="universe" name="universe"}
</p>{/if}
<table>
<tr>
	<th class="colorPositive">{$LNG.tkb_platz}</th>
	<th class="colorPositive">{$LNG.tkb_owners}</th>
	<th class="colorPositive">{$LNG.tkb_datum}</th>
	<th class="colorPositive">{$LNG.tkb_units}</th>
</tr>
{foreach $hallList as $hallRow}
<tr>
	<td>{$hallRow@iteration}</td>
	<td><a href="game.php?page=raport&amp;raport={$hallRow.rid}" target="_blank">
	{if $hallRow.result == "a"}
	<span class="colorPositive">{$hallRow.attacker}</span><span style="color:#FFFFFF"><b> VS </b></span><span class="colorNegative">{$hallRow.defender}</span>
	{elseif $hallRow.result == "r"}
	<span class="colorNegative">{$hallRow.attacker}</span><span style="color:#FFFFFF"><b> VS </b></span><span class="colorPositive">{$hallRow.defender}</span>
	{else}
	{$hallRow.attacker}<b> VS </b>{$hallRow.defender}
	{/if}
	</a></td>
	<td>{$hallRow.time}</td>
	<td>{number_format($hallRow.units, 0, ",", ".")}</td>
</tr>
{/foreach}
<tr>
<td colspan="4"><p>{$LNG.tkb_legende}<span class="colorPositive">{$LNG.tkb_gewinner}</span><span class="colorNegative">{$LNG.tkb_verlierer}</span></p></td>
</tr>
</table>
{/block}