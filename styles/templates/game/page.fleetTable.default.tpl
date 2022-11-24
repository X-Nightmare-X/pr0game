{block name="title" prepend}{$LNG.lm_fleet}{/block}
{block name="content"}
  <table>
    <tr>
      <th colspan="9">
        <div class="transparent" style="text-align:left;float:left;">{$LNG.fl_fleets} {$activeFleetSlots}
          / {$maxFleetSlots}</div>
        <div class="transparent" style="text-align:right;float:right;">{$activeExpedition}
          / {$maxExpedition} {$LNG.fl_expeditions}</div>
      </th>
    </tr>
    <tr>
      <td>{$LNG.fl_number}</td>
      <td>{$LNG.fl_mission}</td>
      <td>{$LNG.fl_ammount}</td>
      <td>{$LNG.fl_beginning}</td>
      <td>{$LNG.fl_departure}</td>
      <td>{$LNG.fl_destiny}</td>
      <td>{$LNG.fl_arrival}</td>
      <td>{$LNG.fl_objective}</td>
      <td>{$LNG.fl_order}</td>
    </tr>
      {foreach name=FlyingFleets item=FlyingFleetRow from=$FlyingFleetList}
        <tr>
          <td>{$smarty.foreach.FlyingFleets.iteration}</td>
          <td>
            <a
              data-tooltip-content="<table style='width:200px'><tr><td style='width:50%;color:white'>{$LNG['tech'][901]}</td><td style='width:50%;color:white'>{$FlyingFleetRow.metal}</td></tr><tr><td style='width:50%;color:white'>{$LNG['tech'][902]}</td><td style='width:50%;color:white'>{$FlyingFleetRow.crystal}</td></tr><tr><td style='width:50%;color:white'>{$LNG['tech'][903]}</td><td style='width:50%;color:white'>{$FlyingFleetRow.deuterium}</td></tr></table>"
              class="tooltip">
                {$LNG["type_mission_{$FlyingFleetRow.mission}"]}
            </a>
              {if $FlyingFleetRow.state == 1}
                <br>
                <a title="{$LNG.fl_returning}">{$LNG.fl_r}</a>
              {else}
                <br>
                <a title="{$LNG.fl_onway}">{$LNG.fl_a}</a>
              {/if}
          </td>
          <td><a class="tooltip_sticky"
                 data-tooltip-content="<table><tr><th colspan='2' style='text-align:center;'>{$LNG.fl_info_detail}</th></tr>{foreach $FlyingFleetRow.FleetList as $shipID => $shipCount}<tr><td class='transparent'>{$LNG.tech.{$shipID}}:</td><td class='transparent'>{$shipCount}</td></tr>{/foreach}</table>">{$FlyingFleetRow.amount}</a>
          </td>
          <td><a
              href="game.php?page=galaxy&amp;galaxy={$FlyingFleetRow.startGalaxy}&amp;system={$FlyingFleetRow.startSystem}">[{$FlyingFleetRow.startGalaxy}
              :{$FlyingFleetRow.startSystem}:{$FlyingFleetRow.startPlanet}]</a></td>
          <td{if $FlyingFleetRow.state == 0} style="color:lime"{/if}>{$FlyingFleetRow.startTime}</td>
          <td><a
              href="game.php?page=galaxy&amp;galaxy={$FlyingFleetRow.endGalaxy}&amp;system={$FlyingFleetRow.endSystem}">[{$FlyingFleetRow.endGalaxy}
              :{$FlyingFleetRow.endSystem}:{$FlyingFleetRow.endPlanet}]</a></td>
            {if $FlyingFleetRow.mission == 4 && $FlyingFleetRow.state == 0}
              <td>-</td>
            {else}
              <td{if $FlyingFleetRow.state != 0} style="color:lime"{/if}>{$FlyingFleetRow.endTime}</td>
            {/if}
          <td id="fleettime_{$smarty.foreach.FlyingFleets.iteration}" class="fleets"
              data-fleet-end-time="{$FlyingFleetRow.returntime}"
              data-fleet-time="{$FlyingFleetRow.resttime}">{getRestTimeFormat({$FlyingFleetRow.resttime})}</td>
          <td>
              {if !$isVacation && $FlyingFleetRow.state != 1 && $FlyingFleetRow.no_returnable != 1}
                <form action="game.php?page=fleetTable&amp;action=sendfleetback" method="post">
                  <input name="fleetID" value="{$FlyingFleetRow.id}" type="hidden">
                  <input value="{$LNG.fl_send_back}" type="submit">
                </form>
                <span class="aborttime" starttime="{$FlyingFleetRow.startTimestamp}"></span>
                  {if $FlyingFleetRow.mission == 1}
                    <form action="game.php?page=fleetTable&amp;action=acs" method="post">
                      <input name="fleetID" value="{$FlyingFleetRow.id}" type="hidden">
                      <input value="{$LNG.fl_acs}" type="submit">

                    </form>
                  {/if}
              {else}
                &nbsp;-&nbsp;
              {/if}
          </td>
        </tr>
          {foreachelse}
        <tr>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
        </tr>
      {/foreach}
      {if $maxFleetSlots == $activeFleetSlots}
        <tr>
          <td colspan="9">{$LNG.fl_no_more_slots}</td>
        </tr>
      {/if}
  </table>
    {if !empty($acsData)}
        {include file="shared.fleetTable.acsTable.tpl"}
    {/if}
  <form action="?page=fleetStep1" method="post">
    <input type="hidden" name="galaxy" value="{$targetGalaxy}">
    <input type="hidden" name="system" value="{$targetSystem}">
    <input type="hidden" name="planet" value="{$targetPlanet}">
    <input type="hidden" name="type" value="{$targetType}">
    <input type="hidden" name="target_mission" value="{$targetMission}">
    <table class="table519">
      <tr>
        <th colspan="5">{$LNG.fl_new_mission_title}</th>
      </tr>
      <tr style="height:20px;">
        <td>{$LNG.fl_ship_type}</td>
        <td>{$LNG.fl_ship_available}</td>
        <td>-</td>
        <td>-</td>
        <td id="expocount" expocap="{$maxExpo}">-</td>
      </tr>
        {foreach $FleetsOnPlanet as $FleetRow}
          <tr style="height:20px;">
            <td>{if $FleetRow.speed != 0} <a class='tooltip'
                                             data-tooltip-content='<table><tr><td>{$LNG.fl_speed_title}</td><td>{$FleetRow.speed}</td></tr></table>'>{$LNG.tech.{$FleetRow.id}}</a>{else}{$LNG.tech.{$FleetRow.id}}{/if}
            </td>
            <td id="ship{$FleetRow.id}_value">{number_format($FleetRow.count, 0, ",", ".")}</td>
              {if $FleetRow.speed != 0}
                <td><a href="javascript:noShip('ship{$FleetRow.id}');">{$LNG.fl_null}</a></td>
                <td><a href="javascript:maxShip('ship{$FleetRow.id}');">{$LNG.fl_max}</a></td>
                <td><input type="number" name="ship{$FleetRow.id}" id="ship{$FleetRow.id}_input" size="10" value="0"
                           style="max-width: 10em;"></td>
              {else}
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              {/if}
          </tr>
        {/foreach}
      <tr style="height:20px;">
          {if count($FleetsOnPlanet) == 0}
            <td colspan="5">{$LNG.fl_no_ships}</td>
          {else}
            <td id="cargospace" colspan="2" data="{$LNG['in_capacity']}">&nbsp;</td>
            <td colspan="2"><a href="javascript:noShips();">{$LNG.fl_remove_all_ships}</a></td>
            <td><a href="javascript:maxShips();">{$LNG.fl_select_all_ships}</a></td>
          {/if}
      </tr>
      <tr id="customfleets"></tr>
        {if $maxFleetSlots != $activeFleetSlots}
      <tr style="height:20px;">
        <td colspan="5"><input type="submit" value="{$LNG.fl_continue}"></td>
          {/if}
    </table>
    <table class="table519">
      <tr>
        <td>{$LNG['tech'][901]}</td>
        <td>{$LNG['tech'][902]}</td>
        <td>{$LNG['tech'][903]}</td>
      </tr>
      <tr>
        <td><input name="met_storage"
                   type="number"
                   placeholder="{$LNG['tech'][901]}"></td>
        <td><input name="krist_storage" type="number"
                   placeholder="{$LNG['tech'][902]}"></td>
        <td><input name="deut_storage" type="number"
                   placeholder="{$LNG['tech'][903]}"></td>
      </tr>
      <tr >
       <td colspan="3" style="text-align: center">
         <button type="button" id="gt_select">{$LNG['tech'][203]}<span id="gt_amt"></span></button><button type="button" id="kt_select">{$LNG['tech'][202]}<span id="kt_amt"></span></button>
       </td>
      </tr>
    </table>

  </form>
  <br>
  <table class="table519">
    <tr>
      <th colspan="3">{$LNG.fl_bonus}</th>
    </tr>
    <tr>
      <th style="width:33%">{$LNG.fl_bonus_attack}</th>
      <th style="width:33%">{$LNG.fl_bonus_shield}</th>
      <th style="width:33%">{$LNG.fl_bonus_defensive}</th>
    </tr>
    <tr>
      <td>+{$bonusAttack} %</td>
      <td>+{$bonusShield} %</td>
      <td>+{$bonusDefensive} %</td>
    </tr>
    <tr>
      <th style="width:33%">{$LNG.tech.115}</th>
      <th style="width:33%">{$LNG.tech.117}</th>
      <th style="width:33%">{$LNG.tech.118}</th>
    </tr>
    <tr>
      <td>+{$bonusCombustion} %</td>
      <td>+{$bonusImpulse} %</td>
      <td>+{$bonusHyperspace} %</td>
    </tr>
  </table>
  <br>
  <table class="table519">
    <tr>
      <th colspan="3" onclick="toogle_custom_fleet()" style="border-spacing: 0px;cursor: pointer;"><span id="c_fleet_span">▼</span> Custom Flotten</th>
    </tr>
  </table>
  <table id="customfleet" class="table519" style="display: none">
    <tr>
      <th colspan=3><select id="cfleet_select" style="width:100%">

        </select></th>
    </tr>
    <tr>
      <th colspan=3 style="text-align: center"><input id="cfleet_name" placeholder="name" style="width:90%"></th>
    </tr>
    <tr>
      <td colspan=1>Expo points:</td>
      <td colspan=2 id="ship_expo_points"></td>
    </tr>
    <tr>
      <td colspan=1>{$LNG['in_capacity']}</td>
      <td colspan=2 id="ship_cargo_points"></td>
    </tr>

    <tr>
      <th colspan="2">{$LNG.tech.200}</th>
      <th colspan="1">{$LNG['rec_count']} </th>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.202}</td>
      <td colspan="1"><input id="ship_202" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.203}</td>
      <td colspan="1"><input id="ship_203" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.204}</td>
      <td colspan="1"><input id="ship_204" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.205}</td>
      <td colspan="1"><input id="ship_205" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.206}</td>
      <td colspan="1"><input id="ship_206" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.207}</td>
      <td colspan="1"><input id="ship_207" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.215}</td>
      <td colspan="1"><input id="ship_215" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.213}</td>
      <td colspan="1"><input id="ship_213" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.211}</td>
      <td colspan="1"><input id="ship_211" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.214}</td>
      <td colspan="1"><input id="ship_214" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.210}</td>
      <td colspan="1"><input id="ship_210" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.209}</td>
      <td colspan="1"><input id="ship_209" type="number" value="0"></td>
    </tr>
    <tr>
      <td colspan="2">{$LNG.tech.208}</td>
      <td colspan="1"><input id="ship_208" type="number" value="0"></td>
    </tr>

    <tr>
      <td colspan="3" style="text-align: center;">
        <button id="cf_save" style="width:25%">
          {$LNG['al_save']}
        </button>
        <button id="cf_del" style="width:25%">
          {$LNG['al_dlte']}
        </button>
      </td>
    </tr>


  </table>
{/block}
{block name="script" append}
  <script src="scripts/game/fleetTable.js"></script>
{/block}
