{include file="overall_header.tpl"}
<table style="width:760px">
	<tr>
		<th>IP:</th>
    <th>Activity:</th>
		<th>{$LNG.mip_known}</th>
		<th>{$LNG.se_id_owner}</th>
		<th>{$LNG.se_name}</th>
		<th>{$LNG.se_email}</th>
		<th>{$LNG.ac_register_time}</th>
		<th>{$LNG.ac_act_time}</th>
	</tr>
  {foreach $multiGroups as $multiID => $multiEntry}
    <tr>
      <td rowspan="2" valign="top" style="vertical-align: top;">{$multiEntry.multi_ip}</td>
      <td rowspan="2" valign="top" style="vertical-align: top;">{$multiEntry.lastActivity}</td>
      <td rowspan="2" valign="top" style="vertical-align: top;">
        {if ($multiEntry.isKnown != 0)}
          <a href="admin.php?page=multiips&amp;action=unknown&amp;id={$multiID}"><img src="styles/resource/images/true.png"></a>
        {else}
          <a href="admin.php?page=multiips&amp;action=known&amp;id={$multiID}"><img src="styles/resource/images/false.png"></a>
        {/if}
      </td>
      {foreach $multiEntry.users as $ID => $User}
        <td class="left" style="padding:3px;">{$ID}</td>
        <td class="left" style="padding:3px;"><a href="admin.php?page=accountdata&id_u={$ID}">{$User.username} (?)</a></td>
        <td class="left" style="padding:3px;">{$User.email}</td>
        <td class="left" style="padding:3px;">{$User.register_time}</td>
        <td class="left" style="padding:3px;">{$User.onlinetime}</td>
        </tr>{if !$User@last}<tr>{/if}
      {/foreach}
  {/foreach}
</table>
{include file="overall_footer.tpl"}
