<ul id="menu">
  {nocache}
  <li class="menu-separator"></li>
  <li><a href="game.php?page=overview">{$LNG.lm_overview}</a></li>
  {if isModuleAvailable($smarty.const.MODULE_IMPERIUM)}
    <li><a href="game.php?page=imperium">{$LNG.lm_empire}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_BUILDING)}
    <li><a href="game.php?page=buildings">{$LNG.lm_buildings}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_SHIPYARD_FLEET)}
    <li><a href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipshard}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_SHIPYARD_DEFENSIVE)}
    <li><a href="game.php?page=shipyard&amp;mode=defense">{$LNG.lm_defenses}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_RESEARCH)}
    <li><a href="game.php?page=research">{$LNG.lm_research}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_GALAXY)}
    <li><a href="game.php?page=galaxy">{$LNG.lm_galaxy}</a></li>
  {/if}
  <li><a href="game.php?page=fleetTable">{$LNG.lm_fleet}</a></li>
  {if isModuleAvailable($smarty.const.MODULE_MESSAGES)}
    <li><a href="game.php?page=messages">{$LNG.lm_messages}
      {nocache}
        {if $new_message > 0}
          <span id="newmes"> (<span id="newmesnum">{$new_message}</span>)</span>
        {/if}
      {/nocache}
    </a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_TECHTREE)}
    <li><a href="game.php?page=techtree">{$LNG.lm_technology}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_RESSOURCE_LIST)}
    <li><a href="game.php?page=resources">{$LNG.lm_resources}</a></li>
  {/if}
    {if isModuleAvailable($smarty.const.MODULE_MISSION_TRADE)}
	<li><a href="game.php?page=marketPlace">{$LNG.lm_marketplace}</a></li>
  {/if}
  <li class="menu-separator"></li>
  {if isModuleAvailable($smarty.const.MODULE_ALLIANCE)}
    <li><a href="game.php?page=alliance">{$LNG.lm_alliance}
      {nocache}
        {if $new_allyrequests > 0}
          <span id="newmes"> (<span id="newmesnum">{$new_allyrequests}</span>)</span>
        {/if}
      {/nocache}
    </a></li>
  {/if}

  {if isModuleAvailable($smarty.const.MODULE_STATISTICS)}
    <li><a href="game.php?page=statistics">{$LNG.lm_statistics}</a></li>
  {/if}

  {if isModuleAvailable($smarty.const.MODULE_SEARCH)}
    <li><a href="game.php?page=search">{$LNG.lm_search}</a></li>
  {/if}

  <li><a href="https://discord.gg/jhYYN3yuat" target="copy">Discord</a></li>
  <!--<li><a href="https://ko-fi.com/pr0game" target="copy">{$LNG.donate}</a></li>-->
  {if isModuleAvailable($smarty.const.MODULE_SUPPORT)}
    <li><a href="game.php?page=ticket">{$LNG.lm_support}</a></li>
  {/if}
  <li><a href="game.php?page=questions">{$LNG.lm_faq}</a></li>
  {if isModuleAvailable($smarty.const.MODULE_BANLIST)}
    <li><a href="game.php?page=banList">{$LNG.lm_banned}</a></li>
  {/if}
  {if false}
    <li><a href="index.php?page=rules" target="rules">{$LNG.lm_rules}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_SIMULATOR)}
    <li><a href="game.php?page=battleSimulator">{$LNG.lm_battlesim}</a></li>
  {/if}
  <li><a href="game.php?page=battleHall">{$LNG.lm_topkb}</a></li>
  {if isModuleAvailable($smarty.const.MODULE_RECORDS)}
    <li><a href="game.php?page=records">{$LNG.lm_records}</a></li>
  {/if}

  <li class="menu-separator"></li>
  {if isModuleAvailable($smarty.const.MODULE_NOTICE)}
    <li><a href="javascript:OpenPopup('?page=notes', 'notes', 720, 300);">{$LNG.lm_notes}</a></li>
  {/if}
  {if isModuleAvailable($smarty.const.MODULE_BUDDYLIST)}
    <li><a href="game.php?page=buddyList">{$LNG.lm_buddylist}</a></li>
  {/if}
  <li><a href="game.php?page=settings">{$LNG.lm_options}</a></li>
  <li><a href="game.php?page=logout">{$LNG.lm_logout}</a></li>
  {if $authlevel > 0}
    <li><a href="./admin.php" class="colorPositive">{$LNG.lm_administration} ({$VERSION})</a></li>
  {/if}
  {/nocache}
</ul>
<div id="disclamer" class="no-mobile">

</div>
