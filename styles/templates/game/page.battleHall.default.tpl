{block name="title" prepend}{$LNG.lm_topkb}{/block}
{block name="content"}
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
        <td><a href="game.php?page=raport&amp;mode=battlehall&amp;raport={$row.rid}" target="_blank">
        {if $row.result == "a"}
        <span style="color:#00FF00">{$row.attacker}</span> VS <span style="color:#FF0000">{$row.defender}</span>
        {elseif $row.result == "r"}
        <span style="color:#FF0000">{$row.attacker}</span> VS <span style="color:#00FF00">{$row.defender}</span>
        {else}
        {$row.attacker} VS {$row.defender}
        {/if}
        </a></td>
        <td>{$row.date}</td>
        <td>{$row.units|number}</td>
    </tr>
{/foreach}
<tr>
<td colspan="4">{$LNG.tkb_legende}<span style="color:#00FF00">{$LNG.tkb_gewinner}</span><span style="color:#FF0000">{$LNG.tkb_verlierer}</span></td></tr>
</tbody>
</table>
{/block}
