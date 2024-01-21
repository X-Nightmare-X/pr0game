{block name="title" prepend}
{$LNG.Achievements}{/block}

{block name="content"}
	
<style>
    .minus { display:none;	}
</style>
{if $messages}
	<div class="message"><a href="?page=messages">{$messages}</a></div>
{/if}
<div>
<div class="infos"> 
{foreach $allAchievements as $achievement}
  {if !in_array($achievement['id'], $notImplemented)}
    <div class="techi" id="h{$achievement['id']}">
      <span style="max-width: 42%; display: inline-block;"><a href="#" onclick="return Dialog.ach({$achievement['id']})">{$LNG.Achievement_names[$achievement['id']]}</a></span>
      </br>
      <a href="#" onclick="return Dialog.ach({$achievement['id']})"><img src="{$dpath}achievements/achievement_{$achievement['id']}.jpg" width="90" class="achImage"></a>
      </br>
    </div>
  {/if}
{/foreach}</div>
</table>
{/block}
