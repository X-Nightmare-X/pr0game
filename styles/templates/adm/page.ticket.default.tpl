{include file="overall_header.tpl"}
<table>
	<tr>
		<td style="vertical-align: top;">
			<table id="openTickets" width="70%" cellpadding="2" cellspacing="2">
				<tr>
					<th colspan="6">{$LNG.ti_header}</th>
				</tr>
				<tr>
					<td colspan="6" style="text-align: center;">
						Category: <select id="categoryID">{html_options options=$categories}</select>
					</td>
				</tr>
				<tr>
					<th style="width:10%">{$LNG.ti_id}</td>
					<th style="width:10%">{$LNG.ti_username}</td>
					<th style="width:40%">{$LNG.ti_subject}</td>
					<th style="width:10%">{$LNG.ti_answers}</td>
					<th style="width:15%">{$LNG.ti_date}</td>
					<th style="width:15%">{$LNG.ti_status}</td>
				</tr>
				{foreach $ticketList as $TicketID => $TicketInfo}	
				{if $TicketInfo.status < 2}
				<tr data-category="{$TicketInfo.categoryID}">
					<td><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">#{$TicketID}</a></td>
					<td><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.username}</a></td>
					<td><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.subject}</a></td>
					<td>{$TicketInfo.answer - 1}</td>
					<td>{$TicketInfo.time}</td>
					<td>{if $TicketInfo.status == 0}<span style="color:green">{$LNG.ti_status_open}</span>{elseif $TicketInfo.status == 1}<span style="color:orange">{$LNG.ti_status_answer}</span>{/if}</td>
				</tr>
				{/if}
				{/foreach}
			</table>
		</td>

		<td style="vertical-align: top;">
			<table width="70%" cellpadding="2" cellspacing="2">
				<tr>
					<th colspan="6">{$LNG.ti_status_closed}</th>
				</tr>
				<tr>
					<th style="width:10%">{$LNG.ti_id}</td>
					<th style="width:10%">{$LNG.ti_username}</td>
					<th style="width:40%">{$LNG.ti_subject}</td>
					<th style="width:10%">{$LNG.ti_answers}</td>
					<th style="width:15%">{$LNG.ti_date}</td>
					<th style="width:15%">{$LNG.ti_status}</td>
				</tr>
				{foreach $ticketList as $TicketID => $TicketInfo}	
				{if $TicketInfo.status == 2}
				<tr>
					<td><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">#{$TicketID}</a></td>
					<td><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.username}</a></td>
					<td><a href="admin.php?page=support&amp;mode=view&amp;id={$TicketID}">{$TicketInfo.subject}</a></td>
					<td>{$TicketInfo.answer - 1}</td>
					<td>{$TicketInfo.time}</td>
					<td><span class="colorNegative">{$LNG.ti_status_closed}</span></td>
				</tr>
				{/if}
				{/foreach}
			</table>
		</td>
	</tr>
</table>
<script>
	$(function() {
		$('#categoryID').on('change', function(e) {
			table = document.getElementById("openTickets");
  			tr = table.getElementsByTagName("tr");
			category = $(this).val();
			for (i = 0; i < tr.length; i++) {
				if (category == 0) {
					tr[i].style.display = "";
				} else if (tr[i].dataset.category != null && tr[i].dataset.category != category) {
					tr[i].style.display = "none";
				} else {
					tr[i].style.display = "";
				}
			}
		});
	});
</script>
{include file="overall_footer.tpl"}