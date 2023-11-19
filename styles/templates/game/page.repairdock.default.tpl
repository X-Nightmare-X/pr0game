{block name="title" prepend}{$LNG.lm_repairdock}{/block}
{block name="content"}
  {if $messages}
    <div class="message"><a href="?page=messages">{$messages}</a></div>
  {/if}
  {if $busy}
    <table width="70%" id="infobox" style="border: 2px solid red; text-align:center;background:transparent">
      <tr>
        <td>{$LNG.bd_repairdock_busy}</td>
      </tr>
    </table><br><br>
  {/if}
  {if !empty($List)}
    <div id="repairlist" class="infos1">
      {$ctime=0}
      <div class="repairb">
        {foreach $List.ships as $shipID => $amount}
          {$LNG.tech.{$shipID}} {$amount}<br>
        {/foreach}
        {$ctime = $List.resttime+ $ctime}
        <span id="progress">
          <br>
          <div id="progressbar" data-time="{$List.resttime}"></div>
        </span>
      </div>
      <div class="repaira">
        <div id="time" data-time="{$List.time}"><br></div>
        {if $umode == 0}
          <form action="game.php?page=repairdock" method="post" class="build_form">
            <input type="hidden" name="cmd" value="deploy">
            <button type="submit" class="build_submit onlist" id="deployRepaired" style="display:none">{$LNG.bd_deploy}</button>
          </form>
        {else}
          -
        {/if}
        <br><span class="colorPositive timer" data-time="{$List.endtime}" data-umode="{$umode}">{if $umode == 0}{$List.pretty_end_time}{else}{$LNG.bd_paused}{/if}</span>
      </div>
    </div><br>
  {/if}

  <div>
    <div class="planeto">
      <button id="ship1">{$LNG.fm_civil}</button> | <button id="ship2">{$LNG.fm_military}</button> | <button id="ship3">{$LNG.fm_all}</button>
    </div>
    <form action="game.php?page=repairdock" method="post">
      {if !$busy}
        <br>
        <div class="planeto">
          <input type="button" value="{$LNG.bd_max_ships}" onclick="repairAll()">
          <input class="b colorPositive" type="submit" value="{$LNG.bd_repair_ships}">
        </div>
      {/if}
      {foreach $elementList as $ID => $Element}
        <div class="infos" id="s{$ID}">
          <div class="repairShipHeader">
            <a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>
            <span id="val_{$ID}">{if $Element.available != 0} ({$LNG.bd_available} {number_format($Element.available, 0, ",", ".")}){/if}</span>
          </div>
          <div class="repairShipContainer">
            <a style="padding: 10px;" href="#" onclick="return Dialog.info({$ID})">
              <img style="float: left;" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
            </a>
            <div class="repairShipContainerContent">
              <span>
                <p>{$LNG.bd_max_ships_repair}:<br><span style="font-weight:700">{number_format($Element.maxBuildable, 0, ",", ".")}</span></p>
              </span>
            </div>
            <div class="repairShipContainerContent">
              <span>
                {if $ID==212} +{$SolarEnergy} {$LNG.tech.911}<br>{/if}
                {if !$busy}
                  <input type="number" class="numfield" data-max="{$Element.maxBuildable}" name="fmenge[{$ID}]" id="input_{$ID}" size="3" value="0" tabindex="{$smarty.foreach.FleetList.iteration}">
                  <input type="button" value="{$LNG.bd_max_ships}" onclick="$('#input_{$ID}').val('{$Element.maxBuildable}')">
                  <br>
                {/if}
                {$LNG.fgr_time}
                <span class="statictime" timestamp="{$Element.elementTime}"></span>
              </span>
            </div>
          </div>
        </div>
      {/foreach}
      {if !$busy}
        <br>
        <div class="planeto">
          <input type="button" value="{$LNG.bd_max_ships}" onclick="repairAll()">
          <input class="b colorPositive" type="submit" value="{$LNG.bd_repair_ships}">
        </div>
      {/if}
    </form>
  </div>
{/block}
