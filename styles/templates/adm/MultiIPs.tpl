{include file="overall_header.tpl"}
<table style="width:760px">
	<tr>
		<th>IP:</th>
    <th>Activity:</th>
		<th colspan="2">{$LNG.mip_known}</th>
		<th>{$LNG.se_id_owner}</th>
		<th>{$LNG.se_name}</th>
		<th>{$LNG.se_email}</th>
		<th>{$LNG.ac_register_time}</th>
		<th>{$LNG.ac_act_time}</th>
		<th>IP:</th>
    <th>Activity:</th>
	</tr>
  {foreach $multiGroups as $multiID => $multiEntry}
    <tr>
      <td rowspan="{count($multiEntry.users)}" valign="center" style="vertical-align: center;">{$multiEntry.multi_ip}</td>
      <td rowspan="{count($multiEntry.users)}" valign="center" style="vertical-align: center;">{$multiEntry.lastActivity}</td>
      <td rowspan="{count($multiEntry.users)}" valign="center" style="vertical-align: center;">
        {if ($multiEntry.isKnown != 0)}
          <a href="admin.php?page=multiips&amp;action=unknown&amp;id={$multiID}"><img src="styles/resource/images/true.png"></a>
        {else}
          <a href="admin.php?page=multiips&amp;action=known&amp;id={$multiID}"><img src="styles/resource/images/false.png"></a>
        {/if}
      </td>
      {foreach $multiEntry.users as $ID => $User}
        <td style="padding:3px;">
          {if ($User.isKnown != 0)}
            <a href="admin.php?page=multiips&amp;action=unknown&amp;id={$multiID}&amp;userID={$ID}"><img src="styles/resource/images/true.png"></a>
          {else}
            <a href="admin.php?page=multiips&amp;action=known&amp;id={$multiID}&amp;userID={$ID}"><img src="styles/resource/images/false.png"></a>
          {/if}
        </td>
        <td class="left" style="padding:3px;">{$ID}</td>
        <td class="left" style="padding:3px;"><a href="admin.php?page=accountdata&id_u={$ID}">{$User.username} (?)</a></td>
        <td class="left" style="padding:3px;">{$User.email}</td>
        <td class="left" style="padding:3px;">{$User.register_time}</td>
        <td class="left" style="padding:3px;">{$User.onlinetime}</td>
        <td class="left" style="padding:3px;">{$User.user_lastip}</td>
        <td class="left" style="padding:3px;">{$User.lastActivity}</td>
        </tr>{if !$User@last}<tr>{/if}
      {/foreach}
  {/foreach}
</table>
{include file="overall_footer.tpl"}
