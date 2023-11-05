{block name="title" prepend}{$LNG.lm_repairdock}{/block}
{block name="content"}
  {if $messages}
    <div class="message"><a href="?page=messages">{$messages}</a></div>
  {/if}
  {if $buisy}
    <table width="70%" id="infobox" style="border: 2px solid red; text-align:center;background:transparent">
      <tr>
        <td>{$LNG.bd_repairdock_buisy}</td>
      </tr>
    </table><br><br>
  {/if}
  {if !empty($BuildList)}
    <div id="buildlist" class="infos1">
      {$ctime=0}
      <div class="buildb">
      {foreach $BuildList.ships as $shipID => $amount}
        {$LNG.tech.{$shipID}} {$amount}<br>
      {/foreach}
      <div class="bulida">
        <div id="time" data-time="{$BuildList.time}"><br></div>
        {if $umode == 0}
          <form action="game.php?page=repairdock" method="post" class="build_form">
            <input type="hidden" name="cmd" value="deploy">
            <button type="submit" class="build_submit onlist">{$LNG.bd_deploy}</button>
          </form>
        {else}
          -
        {/if}
        <br><span class="colorPositive" data-time="{$List.endtime}" data-umode="{$umode}" class="timer">{if $umode == 0}{$List.pretty_end_time}{else}{$LNG.bd_paused}{/if}</span>
      </div>
      <div id="buildlist" class="infos1">
        {$ctime = $List.resttime+ $ctime}
        <br><br>
        <div id="progressbar" data-time="{$List.resttime}"></div>
      </div>
    </div>
  {/if}

  <div>
    <div class="planeto">
      <button id="ship1">{$LNG.fm_civil}</button> | <button id="ship2">{$LNG.fm_military}</button> | <button id="ship3">{$LNG.fm_all}</button>
    </div>
    <form action="game.php?page=repairdock" method="post">
      {foreach $elementList as $ID => $Element}
        <div class="infos" id="s{$ID}">
          <div class="buildn">
            <a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
            <span id="val_{$ID}">{if $Element.available != 0} ({$LNG.bd_available} {number_format($Element.available, 0, ",", ".")}){/if}</span>
          </div>
          <div class="buildl">
            <a href="#" onclick="return Dialog.info({$ID})">
              <img style="float: left;" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
            </a>
            {$LNG.bd_remaining}<br>
            <p>{$LNG.bd_max_ships_long}:<span style="font-weight:700"><br>{number_format($Element.maxBuildable, 0, ",", ".")}</p>
          </div>
          <div class="buildl">
            {if $ID==212} +{$SolarEnergy} {$LNG.tech.911}<br>{/if}
            <span>
              {if !$buisy}
                <input type="number" name="fmenge[{$ID}]" id="input_{$ID}" size="3" value="0" tabindex="{$smarty.foreach.FleetList.iteration}">
                <input type="button" value="{$LNG.bd_max_ships}" onclick="$('#input_{$ID}').val('{$Element.maxBuildable}')">
              {/if}
              {$LNG.fgf_time}
              <span class="statictime" timestamp="{$Element.elementTime}"></span>
            </span>
          </div>
        </div>
      {/foreach}
      <div class="planeto">
        {if !$buisy}
          <input class="b colorPositive" type="submit" value="{$LNG.bd_build_ships}">
        {/if}
      </div>
    </form>
    {if $buisy}<div class="planeto"></div>{/if}
  </div>
{/block}
{block name="script" append}
  <script type="text/javascript">
    data			= {$BuildList|json_encode};
    bd_operating	= '{$LNG.bd_operating}';
    bd_available	= '{$LNG.bd_available}';
  </script>

  {if !empty($BuildList)}
    <script src="scripts/base/bcmath.js"></script>
    <script src="scripts/game/shipyard.js"></script>
    <script type="text/javascript">
      $(function() {
        ShipyardInit();
      });
    </script>
  {/if}
{/block}
