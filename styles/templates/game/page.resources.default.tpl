{block name="title" prepend}{$LNG.lm_resources}{/block}
{block name="content"}
<form action="?page=resources" method="post">
<input type="hidden" name="mode" value="send">
<table>
<tbody>
<tr>
	<th colspan="5">{$header}</th>
</tr>
<tr style="height:22px">
	<td style="width:40%">&nbsp;</td>
    <td style="width:10%"><a href='#' onclick='return Dialog.info(901)' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.901}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/901.gif'></td><td>{$LNG.shortDescription.901}</td></tr></table></tr></table>">{$LNG.tech.901}</a></td>
    <td style="width:10%"><a href='#' onclick='return Dialog.info(902)' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.902}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/902.gif'></td><td>{$LNG.shortDescription.902}</td></tr></table></tr></table>">{$LNG.tech.902}</a></td>
    <td style="width:10%"><a href='#' onclick='return Dialog.info(903)' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.903}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/903.gif'></td><td>{$LNG.shortDescription.903}</td></tr></table></tr></table>">{$LNG.tech.903}</a></td>
    <td style="width:10%"><a href='#' onclick='return Dialog.info(911)' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.911}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/911.gif'></td><td>{$LNG.shortDescription.911}</td></tr></table></tr></table>">{$LNG.tech.911}</a></td>
</tr>
<tr style="height:22px">
	<td>{$LNG.rs_basic_income}</td>
	<td>{number_format($basicProduction.901, 0, ",", ".")}</td>
	<td>{number_format($basicProduction.902, 0, ",", ".")}</td>
	<td>{number_format($basicProduction.903, 0, ",", ".")}</td>
	<td>{number_format($basicProduction.911, 0, ",", ".")}</td>
</tr>
{foreach $productionList as $productionID => $productionRow}
<tr style="height:22px">
	<td><a href='#' onclick='return Dialog.info({$productionID});' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$productionID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$productionID}.{if $productionID >=600 && $productionID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.{$productionID}}</td></tr></table></tr></table>">{$LNG.tech.$productionID }</a> ({if $productionID  > 200}{$LNG.rs_amount}{else}{$LNG.rs_lvl}{/if} {$productionRow.elementLevel})</td>
	<td><span style="color:{if $productionRow.production.901 > 0}lime{elseif $productionRow.production.901 < 0}red{else}white{/if}">{number_format($productionRow.production.901, 0, ",", ".")}</span></td>
	<td><span style="color:{if $productionRow.production.902 > 0}lime{elseif $productionRow.production.902 < 0}red{else}white{/if}">{number_format($productionRow.production.902, 0, ",", ".")}</span></td>
	<td><span style="color:{if $productionRow.production.903 > 0}lime{elseif $productionRow.production.903 < 0}red{else}white{/if}">{number_format($productionRow.production.903, 0, ",", ".")}</span></td>
	<td><span style="color:{if $productionRow.production.911 > 0}lime{elseif $productionRow.production.911 < 0}red{else}white{/if}">{number_format($productionRow.production.911, 0, ",", ".")}</span></td>
	<td style="width:10%">
		{html_options name="prod[{$productionID}]" options=$prodSelector selected=$productionRow.prodLevel}
	</td>
</tr>
{/foreach}
<tr style="height:22px">
	<td>{$LNG.rs_ress_bonus}</td>
	<td><span style="color:{if $bonusProduction.901 > 0}lime{elseif $bonusProduction.901 < 0}red{else}white{/if}">{number_format($bonusProduction.901, 0, ",", ".")}</span></td>
	<td><span style="color:{if $bonusProduction.902 > 0}lime{elseif $bonusProduction.902 < 0}red{else}white{/if}">{number_format($bonusProduction.902, 0, ",", ".")}</span></td>
	<td><span style="color:{if $bonusProduction.903 > 0}lime{elseif $bonusProduction.903 < 0}red{else}white{/if}">{number_format($bonusProduction.903, 0, ",", ".")}</span></td>
	<td><span style="color:{if $bonusProduction.911 > 0}lime{elseif $bonusProduction.911 < 0}red{else}white{/if}">{number_format($bonusProduction.911, 0, ",", ".")}</span></td>
	<td><input value="{$LNG.rs_calculate}" type="submit"></td>
</tr>
<tr style="height:22px">
	<td>{$LNG.rs_storage_capacity}</td>
	<td><span style="color:lime;">{$storage.901}</span></td>
	<td><span style="color:lime;">{$storage.902}</span></td>
	<td><span style="color:lime;">{$storage.903}</span></td>
	<td>-</td>
</tr>
<tr style="height:22px">
	<td>{$LNG.rs_sum}:</td>
	<td><span style="color:{if $totalProduction.901 > 0}lime{elseif $totalProduction.901 < 0}red{else}white{/if}">{number_format($totalProduction.901, 0, ",", ".")}</span></td>
	<td><span style="color:{if $totalProduction.902 > 0}lime{elseif $totalProduction.902 < 0}red{else}white{/if}">{number_format($totalProduction.902, 0, ",", ".")}</span></td>
	<td><span style="color:{if $totalProduction.903 > 0}lime{elseif $totalProduction.903 < 0}red{else}white{/if}">{number_format($totalProduction.903, 0, ",", ".")}</span></td>
	<td><span style="color:{if $totalProduction.911 > 0}lime{elseif $totalProduction.911 < 0}red{else}white{/if}">{number_format($totalProduction.911, 0, ",", ".")}</span></td>
</tr>
<tr style="height:22px">
	<td>{$LNG.rs_daily}</td>
	<td><span style="color:{if $dailyProduction.901 > 0}lime{elseif $dailyProduction.901 < 0}red{else}white{/if}">{number_format($dailyProduction.901, 0, ",", ".")}</span></td>
	<td><span style="color:{if $dailyProduction.902 > 0}lime{elseif $dailyProduction.902 < 0}red{else}white{/if}">{number_format($dailyProduction.902, 0, ",", ".")}</span></td>
	<td><span style="color:{if $dailyProduction.903 > 0}lime{elseif $dailyProduction.903 < 0}red{else}white{/if}">{number_format($dailyProduction.903, 0, ",", ".")}</span></td>
	<td><span style="color:{if $dailyProduction.911 > 0}lime{elseif $dailyProduction.911 < 0}red{else}white{/if}">{number_format($dailyProduction.911, 0, ",", ".")}</span></td>
</tr>
<tr style="height:22px">
	<td>{$LNG.rs_weekly}</td>
	<td><span style="color:{if $weeklyProduction.901 > 0}lime{elseif $weeklyProduction.901 < 0}red{else}white{/if}">{number_format($weeklyProduction.901, 0, ",", ".")}</span></td>
	<td><span style="color:{if $weeklyProduction.902 > 0}lime{elseif $weeklyProduction.902 < 0}red{else}white{/if}">{number_format($weeklyProduction.902, 0, ",", ".")}</span></td>
	<td><span style="color:{if $weeklyProduction.903 > 0}lime{elseif $weeklyProduction.903 < 0}red{else}white{/if}">{number_format($weeklyProduction.903, 0, ",", ".")}</span></td>
	<td><span style="color:{if $weeklyProduction.911 > 0}lime{elseif $weeklyProduction.911 < 0}red{else}white{/if}">{number_format($weeklyProduction.911, 0, ",", ".")}</span></td>
</tr>
</tbody>
</table>
</form>
{/block}
