{include file="overall_header.tpl"}
<form action="" method="POST">
<table width="90%">
<tr>
<td class="transparent left">
<div onClick="javascript:$('#seeker').slideToggle();" style="cursor:pointer;">
<img src="./styles/resource/images/admin/GO.png" >{$ac_minimize_maximize}
</div>
</td>
</tr>
</table>
<div id="seeker">
<table width="90%">
	<tr>
		<th colspan="8">
			{$se_search_title}
		</td>
	</tr>
	<tr>
		<td>
			{$se_intro}
		</td>
		<td>
			{$se_type_typee}
		</td>
		<td>
			{$se_search_in}
		</td>
		<td>
			{$se_filter_title}
		</td>
		<td>
			{$se_limit}
		</td>
		<td>
			{$se_asc_desc}
		</td>
		{if $OrderBYParse}
		<td>
			{$se_search_order}
		</td>
		{/if}
		<td>
			&nbsp;
		</td>
	</tr>
<tr>
	<td>
		<input type="text" name="key_user" value="{$search}">
	</td>
	<td>
		{html_options name=search options=$Selector.list selected=$SearchFile}
	</td>
	<td>
		{html_options name=search_in options=$Selector.search selected=$SearchFor}
	</td>
	<td>
		{html_options name=fucki options=$Selector.filter selected=$SearchMethod}
	</td>
	<td>
		{html_options name=limit options=$Selector.limit selected=$limit}
	</td>
	<td>
		{html_options name=key_acc options=$Selector.order selected=$OrderBY}
	</td>
	{if $OrderBYParse}
	<td>
		{html_options name=key_order options=$OrderBYParse selected=$Order}
	</td>
	{/if}
	<td>
		<input type="submit" value="{$se_search}">
	</td>
</tr>
{if !empty($error)}
<tr>
	<td colspan="8">
		<span class="colorNegative">{$error}</span>
	</td>
</tr>
{/if}
</table>
</div>
<br>
<table width="90%" border="0px">
{$PAGES}
</table>
{$LIST}
<br>
<table width="90%" border="0px">
{$PAGES}
</table>
</form>
