{block name="title" prepend}{$LNG.siteTitleDisclamer}{/block}
{block name="content"}
<table id="disclamerTable">
	<tr>
		<td style="width:50%;text-align:left;">{$LNG.disclamerLabelAddress}</td><td style="width:50%;text-align:left;">{$disclamer_address}</td>
	</tr>
	<tr>
		<td style="width:50%;text-align:left;">{$LNG.disclamerLabelPhone}</td><td style="width:50%;text-align:left;">{$disclamer_phone}</td>
	</tr>
	<tr>
		<td style="width:50%;text-align:left;">{$LNG.disclamerLabelMail}</td><td style="width:50%;text-align:left;"><a href="{$disclamer_mail}">{$disclamer_mail}</a></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:left;"><p><br></p></td>
	</tr>
	<tr>
		<td colspan="2">{$LNG.disclamerLabelNotice}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:left;">{$disclamer_notice}</td>
	</tr>
</table>
{/block}