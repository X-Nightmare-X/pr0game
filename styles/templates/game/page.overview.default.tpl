{block name="title" prepend}{$LNG.lm_overview}{/block}
{block name="script" append}
    <script>
        $(function() {
            $("#chkbtn").on('click', function() {
                $(this).hide();
                $("#hidden-div").show();
            });
        });

        $(function() {
            $("#chkbtn2").on('click', function() {
                $("#chkbtn").show();
                $("#hidden-div").hide();
            });
        });
        $(function() {
            $("#chkbtn1").on('click', function() {
                $(this).hide();
                $("#hidden-div2").hide()
                $("#tn3").show();
            });
        });
        $(function() {
            $("#chkbtn3").on('click', function() {
                $("#chkbtn1").show();
                $("#hidden-div2").show();
                $("#tn3").hide();

            });
        });
    </script>

{/block}
{block name="content"}
    <style>
        .hidden-div {
            display: none;
        }

        .colorMission2friend{
            color:{$colors.colorMission2friend}
        }
        .colorMission1Own{
            color:{$colors.colorMission1Own}
        }
        .colorMission2Own{
            color:{$colors.colorMission2Own}
        }
        .colorMission3Own{
            color:{$colors.colorMission3Own}
        }
        .colorMission4Own{
            color:{$colors.colorMission4Own}
        }
        .colorMission5Own{
            color:{$colors.colorMission5Own}
        }
        .colorMission6Own{
            color:{$colors.colorMission6Own}
        }
        .colorMission7Own{
            color:{$colors.colorMission7Own}
        }
        .colorMission7OwnReturn{
            color:{$colors.colorMission7OwnReturn}
        }
        .colorMission8Own{
            color:{$colors.colorMission8Own}
        }
        .colorMission9Own{
            color:{$colors.colorMission9Own}
        }
        .colorMission10Own{
            color:{$colors.colorMission10Own}
        }
        .colorMission15Own{
            color:{$colors.colorMission15Own}
        }
        .colorMission16Own{
            color:{$colors.colorMission16Own}
        }
        .colorMission17Own{
            color:{$colors.colorMission17Own}
        }
        .colorMissionReturnOwn{
            color:{$colors.colorMissionReturnOwn}
        }
        .colorMission1Foreign{
            color:{$colors.colorMission1Foreign}
        }
        .colorMission2Foreign{
            color:{$colors.colorMission2Foreign}
        }
        .colorMission3Foreign{
            color:{$colors.colorMission3Foreign}
        }
        .colorMission4Foreign{
            color:{$colors.colorMission2friend}
        }
        .colorMission5Foreign{
            color:{$colors.colorMission5Foreign}
        }
        .colorMission6Foreign{
            color:{$colors.colorMission6Foreign}
        }
        .colorMission7Foreign{
            color:{$colors.colorMission7Foreign}
        }
        .colorMission8Foreign{
            color:{$colors.colorMission8Foreign}
        }
        .colorMission9Foreign{
            color:{$colors.colorMission9Foreign}
        }
        .colorMission10Foreign{
            color:{$colors.colorMission10Foreign}
        }
        .colorMission15Foreign{
            color:{$colors.colorMission15Foreign}
        }
        .colorMission16Foreign{
            color:{$colors.colorMission16Foreign}
        }
        .colorMission17Foreign{
            color:{$colors.colorMission17Foreign}
        }
        .colorMissionReturnForeign{
            color:{$colors.colorMissionReturnForeign}
        }
        .statictimer{
        color:{$colors.colorStaticTimer}
        }
    </style>
    <div>
        {if $messages}
            <div class="message">
                <a href="?page=messages">{$messages}</a>
            </div>
        {/if}
        <div class="infos">
            <div class="planeto">
                <a href="#" onclick="return Dialog.PlanetAction();" title="{$LNG.ov_planetmenu}">{$LNG["type_planet_{$planet_type}"]} {$planetname}</a> ({$username})
            </div>
            {$LNG.ov_server_time}: <span class="servertime">{$servertime}</span>
            <br>
            {$LNG.ov_admins_online}&nbsp;
            {foreach $AdminsOnline as $ID => $Name}
                {if !$Name@first}
                    &nbsp;&bull;&nbsp;
                {/if}
                <a class="colorPositive" href="#" onclick="return Dialog.PM({$ID})">{$Name}</a>
            {/foreach}
            <br><br>
            {$LNG.ov_points} {$rankInfo}
            <br><br>
            <span style="font-weight:bold;">
                {$LNG.ov_rulesnotify}&nbsp;<a href="/index.php?page=rules" target="_blank" style="color:#008FFF;">[{$LNG.ov_rules}]</a>
            </span>
            {if $is_news}
                <div class="hidden-div" id="hidden-div">
                    <span style="font-size:20px;font-weight:bold;text-decoration:underline;">
                        {$LNG.ov_news}:
                    </span>
                    <br><br>
                    {$news}
                    <br>
                    <span style="display:block; margin-top:10px;text-align:center;">
                        <button id="chkbtn2">{$LNG.ov_checknews_hide}</button>
                    </span>
                </div>
                <span style="display:block; margin-top:10px;">
                    <button id="chkbtn">{$LNG.ov_checknews_show}</button>
                </span>
            {/if}
        </div>

        <div class="infos">
            <div class="planeto">
                {$LNG.ov_events} <button id="chkbtn1">{$LNG.ov_fleetbutton_hide}</button>
                <span style="display:none" id="tn3">
                    <button id="chkbtn3">{$LNG.ov_fleetbutton_show}</button>
                </span>
            </div>

            <ul style="list-style-type:none;" id="hidden-div2">
                {foreach $fleets as $index => $fleet}
                    <li style=" padding: 3px; ">
                      <span data-time="{$fleet.returntime}" class="statictimer"></span> |
                      <span id="fleettime_{$index}" class="fleets" data-fleet-end-time="{$fleet.returntime}" data-fleet-time="{$fleet.resttime}">
                            {getRestTimeFormat({$fleet.resttime})}
                        </span>
                        <td id="fleettime_{$index}">{$fleet.text}</td>
                    </li>
                {/foreach}
            </ul>
            &nbsp;
        </div>

        <div class="infos">
            <div class="planeto">
                {$LNG.lm_overview}
            </div>
            {if $Moon}
                <div class="moon">
                    <a href="game.php?page=overview&amp;cp={$Moon.id}&amp;re=0" title="{$Moon.name}">
                        <img src="{$dpath}planeten/{$Moon.image}.jpg" height="100" width="100" style="margin: 20% 0px 5px 0px;" alt="{$Moon.name} {if $Moon.planet_type == 3}({$LNG.fcm_moon}){else}({$LNG.fcm_planet}){/if}">
                    </a>
                    <br>
                    {$Moon.name} {if $Moon.planet_type == 3}({$LNG.ov_moon}){else}({$LNG.ov_planet}){/if}
                    <br>
                    {if $Moon.build}
                        {$LNG.tech[$Moon.build['id']]} ({$Moon.build['level']})
                        <br>
                        <div class="timershort" data-umode="{$umode}" data-time="{$Moon.build['timeleft']}" style="color:#7F7F7F;">
                            {$Moon.build['starttime']}
                        </div>
                    {else}
                        {$LNG.ov_free}
                    {/if}
                </div>
            {else}
                &nbsp;
            {/if}
            <div class="planeth">
                <img style="float: left;" src="{$dpath}planeten/{$planetimage}.jpg" height="200" width="200" alt="{$planetname}">
            </div>
            <div class="planeth">
                {$planetname}
                <br>
                {if $buildInfo.buildings}
                    <a href="game.php?page=buildings">{$LNG.lm_buildings}:</a> {$LNG.tech[$buildInfo.buildings['id']]} ({$buildInfo.buildings['level']})
                    <br>
                    <div class="timer" data-umode="{$umode}" data-time="{$buildInfo.buildings['timeleft']}">
                        {$buildInfo.buildings['starttime']}
                    </div>
                {else}
                    <a href="game.php?page=buildings">{$LNG.lm_buildings}: {$LNG.ov_free}</a>
                    <br>
                {/if}
                {if $buildInfo.tech}
                    <a href="game.php?page=research">{$LNG.lm_research}:</a> {$LNG.tech[$buildInfo.tech['id']]} ({$buildInfo.tech['level']})
                    <br>
                    <div class="timer" data-umode="{$umode}" data-time="{$buildInfo.tech['timeleft']}">
                        {$buildInfo.tech['starttime']}
                    </div>
                {else}
                    <a href="game.php?page=research">{$LNG.lm_research}: {$LNG.ov_free}</a>
                    <br>
                {/if}
                {if $buildInfo.fleet}
                    <a href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipshard}: </a>{$LNG.tech[$buildInfo.fleet['id']]} ({$buildInfo.fleet['level']})
                    <br>
                    <div class="timer" data-umode="{$umode}" data-time="{$buildInfo.fleet['timeleft']}">
                        {$buildInfo.fleet['starttime']}
                    </div>
                {else}
                    <a href="game.php?page=shipyard&amp;mode=fleet">{$LNG.lm_shipshard}: {$LNG.ov_free}</a>
                    <br>
                {/if}
                {nocache}
                {if isModuleAvailable($smarty.const.MODULE_REPAIR_DOCK)}
                    {if $buildInfo.repair}
                        {if $buildInfo.repair.repairing}
                            <a href="game.php?page=repairdock">{$LNG.lm_repairdock}: </a><span id="repairtext" data-alttext="{$buildInfo.repair['deploy_text']}">{$buildInfo.repair['text']}</span>
                        {else}
                            <a href="game.php?page=repairdock">{$LNG.lm_repairdock}: </a><span id="repairtext">{$buildInfo.repair['deploy_text']}</span>
                        {/if}
                        <br>
                        <div class="timerrepair" data-umode="{$umode}" data-time="{$buildInfo.repair['timeleft']}" data-repairing={$buildInfo.repair['repairing']}>
                            {$buildInfo.repair['starttime']}
                        </div>
                    {else}
                        <a href="game.php?page=repairdock">{$LNG.lm_repairdock}: {$LNG.ov_free}</a>
                        <br>
                    {/if}
                {/if}
                {/nocache}
                <br>
                {$LNG.ov_diameter}: {$planet_diameter} {$LNG.ov_distance_unit} (<a title="{$LNG.ov_developed_fields}">{$planet_field_current}</a> / <a title="{$LNG.ov_max_developed_fields}">{$planet_field_max}</a> {$LNG.ov_fields})
                <br>
                {$LNG.ov_temperature}: {$LNG.ov_aprox} {$planet_temp_min}{$LNG.ov_temp_unit} {$LNG.ov_to} {$planet_temp_max}{$LNG.ov_temp_unit}
                <br>
                {$LNG.ov_position}: <a href="game.php?page=galaxy&amp;galaxy={$galaxy}&amp;system={$system}">[{$galaxy}:{$system}:{$planet}]</a>
            </div>
            &nbsp;<br>
        </div>

        <div class="infos">
            {if $AllPlanets}
                <div class="planeto">
                    {$LNG.ov_planets}
                </div>
                {foreach $AllPlanets as $PlanetRow}
                    {if ($PlanetRow@iteration % $themeSettings.PLANET_ROWS_ON_OVERVIEW) === 1}{/if}
                    <div class="planetl">
                        <a href="game.php?page=overview&amp;cp={$PlanetRow.id}" title="{$PlanetRow.name}"><img style="margin: 5px;" src="{$dpath}planeten/{$PlanetRow.image}.jpg" width="100" height="100" alt="{$PlanetRow.name}"></a>
                        <br>
                        {$PlanetRow.name}
                        <br>
                        [{$PlanetRow.galaxy}:{$PlanetRow.system}:{$PlanetRow.planet}]
                        <br>
                        {if $PlanetRow.build}
                            {$LNG.tech[$PlanetRow.build['id']]} ({$PlanetRow.build['level']})
                            <br>
                            <div class="timershort" data-umode="{$umode}" data-time="{$PlanetRow.build['timeleft']}" style="color:#7F7F7F;">
                                {$PlanetRow.build['starttime']}
                            </div>
                        {else}
                            {$LNG.ov_free}
                        {/if}
                    </div>
                    {if $PlanetRow@last && $PlanetRow@total > 1 && ($PlanetRow@iteration % $themeSettings.PLANET_ROWS_ON_OVERVIEW) !== 0}
                        {$to = $themeSettings.PLANET_ROWS_ON_OVERVIEW - ($PlanetRow@iteration % $themeSettings.PLANET_ROWS_ON_OVERVIEW)}
                        {for $foo=1 to $to}

                        {/for}
                    {/if}
                    {if ($PlanetRow@iteration % $themeSettings.PLANET_ROWS_ON_OVERVIEW) === 0}</tr>{/if}
                {/foreach}
            {else}
                &nbsp;
            {/if}
        </div>
    </div>
{/block}
{block name="script" append}
    <script src="scripts/game/overview.js"></script>
{/block}
