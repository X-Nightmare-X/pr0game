{include file="overall_header.tpl"}
<table style="width:760px">
	<tr>
		<th>Spieler-ID</th>
        <th>Name</th>
        <th>Allianz</th>
		<th>Kommentar</th>
		<th>Zeitstempel</th>
	</tr>
	{foreach $comments as $Users}
	<tr>
		<td class="left" style="padding:3px;">{$Users.id}</td>
		<td class="left" style="padding:3px;"><a href="admin.php?page=accountdata&id_u={$ID}">{$Users.username}</a></td>
		<td class="left" style="padding:3px;">{$Users.ally_name}</td>
		<td class="left" style="padding:3px;">{$Users.comment}</td>
		<td class="left" style="padding:3px;">{$Users.created_at}</td>
		</tr>{if !$User@last}<tr>{/if}
	{/foreach}
</table>
{include file="overall_footer.tpl"}