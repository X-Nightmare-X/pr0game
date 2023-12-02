{block name="title" prepend}{$LNG.lm_options}{/block}
{block name="content"}
<table class="table519">
	<tr>
		<th colspan="2">{$LNG.op_vacation_mode_active_message} {$vacationUntil}</th>
	</tr>
	<td>
		<form action="game.php?page=settings" method="post">
			<input type="hidden" name="mode" value="toggleVacation">
			<input name="vacation" type="submit" value="{$LNG.op_end_vacation_mode}" {if !$canVacationDisbaled}disabled{/if}> <img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.op_activate_vacation_mode_descrip}">
		</form>
	</td>
	<td>
		<form action="game.php?page=settings" method="post">
			<input type="hidden" name="mode" value="toggleDelete">
			<input name="delete" type="submit" value="{if $delete}{$LNG.op_stop_dlte_account}{else}{$LNG.op_dlte_account}{/if}"> <img src="./styles/resource/images/admin/i.gif" width="16" height="16" alt="" class="tooltip" data-tooltip-content="{$LNG.op_dlte_account_descrip}">
		</form>
	</td>
</table>
{/block}