{block name="title" prepend}{$LNG.lm_buildings}{/block}
{block name="content"}
  {include file='shared.messages.tpl'}
  {if !empty($Queue)}
    <div id="buildlist" class="infos1">
      {$ctime=0}
      {foreach $Queue as $List}
        {$ID = $List.element}
        <div class="buildb">
          {$List@iteration}.:
          {if !($isBusy.research && ($ID == 6 || $ID == 31)) && !($isBusy.shipyard && ($ID == 15 || $ID == 21)) && !($isBusy.repairdock && $ID == 35) && $RoomIsOk && $CanBuildElement && $BuildInfoList[$ID].buyable}
            <form class="build_form" action="game.php?page=buildings" method="post">
              <input type="hidden" name="cmd" value="insert">
              <input type="hidden" name="building" value="{$ID}">
              <button type="submit" class="build_submit onlist">{$LNG.tech.{$ID}} {$List.level}{if $List.destroy} {$LNG.bd_dismantle}{/if}</button>
            </form>
          {else}
            {$LNG.tech.{$ID}} {$List.level} {if $List.destroy}{$LNG.bd_dismantle}{/if}
          {/if}
        {if $List@first}
          {$ctime = $List.resttime+ $ctime}
          <br><br>
          <div id="progressbar" data-time="{$List.resttime}"></div>
        </div>
          <div class="bulida">
            <div id="time" data-time="{$List.time}"><br></div>
            {if $umode == 0}
              <form action="game.php?page=buildings" method="post" class="build_form">
                <input type="hidden" name="cmd" value="cancel">
                <button type="submit" class="build_submit onlist">{$LNG.bd_cancel}</button>
              </form>
            {else}
              -
            {/if}
            <br><span class="colorPositive timer" data-time="{$List.endtime}" data-umode="{$umode}">{if $umode == 0}{$List.display}{else}{$LNG.bd_paused}{/if}</span>
          </div>
        {else}
        </div>
          <div class="bulida">{$ctime = $List.time+ $ctime}
            <div class="countdown" data-time="{$ctime}"></div>
            {if $umode == 0}
              <form action="game.php?page=buildings" method="post" class="build_form">
                <input type="hidden" name="cmd" value="remove">
                <input type="hidden" name="listid" value="{$List@iteration}">
                <button type="submit" class="build_submit onlist">{$LNG.bd_cancel}</button>
              </form>
            {else}
              -
            {/if}
            <br><span class="colorPositive timer" data-time="{$List.endtime}" data-umode="{$umode}">{if $umode == 0}{$List.display}{else}{$LNG.bd_paused}{/if}</span>
          </div>
        {/if}
      {/foreach}
    </div>
  {/if}

  <div>
    <div class="planeto"> <button id="btn1">{$LNG.fm_mining}</button> | <button id="btn2">{$LNG.fm_storage}</button> | <button id="btn3">{$LNG.fm_other}</button> | <button id="btn4" class="selected">{$LNG.fm_all}</button></div>
    {foreach $BuildInfoList as $ID => $Element}
      <div class="infos {$Element.filterClass} {if $Element.fade}unavailable{/if}" >
        <div class="buildn">
          <a href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}</a>{if $Element.level > 0} ({$LNG.bd_lvl} {$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if}){/if}
        </div>
        <div class="buildl">
          <a href="#" onclick="return Dialog.info({$ID})">
            <img style="float: left;" src="{$dpath}gebaeude/{$ID}.gif" alt="{$LNG.tech.{$ID}}" width="120" height="120">
          </a>
          {if !empty($Element.requirements)}
            {$LNG.tt_requirements}: </br>
            {foreach $Element.requirements as $requireID => $NeedLevel}
              <a href="#" onclick="return Dialog.info({$requireID})">
                <span class="{if $NeedLevel.own < $NeedLevel.count}colorNeutral{else}colorPositive{/if}">
                  {$LNG.tech.$requireID} ({$LNG.tt_lvl} {$NeedLevel.own}/{$NeedLevel.count})
                </span>
              </a>{if !$NeedLevel@last}<br>{/if}
            {/foreach}
          {else}
            {$LNG.bd_remaining}:<br>
            {foreach $Element.costOverflow as $ResType => $ResCount}
              <a href='#' onclick="return Dialog.info({$ResType});" class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$ResType}}</th></tr><tr><table class='hoverinfo'><tr><td>{$LNG.shortDescription.$ResType}</td></tr></table></tr></table>">{$LNG.tech.{$ResType}}</a>: <span style="font-weight:700">{number_format($ResCount, 0, ",", ".")}</span><br>
            {/foreach}
            {if $Element.timetobuild!= 0}<div>{$LNG['whenbuildable']}: <span style="font-weight: bold" class="buildcountdown" timestamp="{$Element.timetobuild}"></span></div>{/if}
            <br>
            {if !empty($Element.infoEnergy)}
              {$LNG.bd_next_level}<br>
              {$Element.infoEnergy}<br>
            {/if}
          {/if}
        </div>

        <div class="buildl">
          <span>
            {foreach $Element.costResources as $RessID => $RessAmount}
              <a href='#' onclick="return Dialog.info({$RessID});" class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$RessID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$RessID}.{if $RessID >=600 && $RessID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$RessID}</td></tr></table></tr></table>">{$LNG.tech.{$RessID}}</a>: <b><span class="{if $Element.costOverflow[$RessID] == 0}colorPositive{else}colorNeutral{/if}">{number_format($RessAmount, 0, ",", ".")}</span></b>
            {/foreach}
          </span>
          <br><br>
          {if $vacation}
            <span class="colorNeutral">{$LNG.op_options_vacation_activated}</span>
          {elseif !empty($Element.requirements)}
            <span class="colorNeutral">{$LNG.bd_requirements}</span>
          {elseif $Element.maxLevel == $Element.levelToBuild}
            <span class="colorNeutral">{$LNG.bd_maxlevel}</span>
          {elseif !$CanBuildElement}
            <span class="colorNeutral">{$LNG.bd_buildlist_full}</span>
          {elseif !$RoomIsOk}
            <span class="colorNeutral">{$LNG.bd_no_more_fields}</span>
          {elseif !$Element.buyable}
            <span class="colorNeutral">{$LNG.bd_remaining}</span>
          {elseif ($isBusy.research && ($ID == 6 || $ID == 31)) || ($isBusy.shipyard && ($ID == 15 || $ID == 21)) || ($isBusy.repairdock && ($ID == 35))}
            <span class="colorNeutral">{$LNG.bd_working}</span>
          {else}
            <form action="game.php?page=buildings" method="post" class="build_form">
              <input type="hidden" name="cmd" value="insert">
              <input type="hidden" name="building" value="{$ID}">
              <button type="submit" class="colorPositive build_submit" {if $ID==34}onclick="window.open('https://www.youtube.com/watch?v=u3dahwW0njk');" {/if}>{if $Element.level == 0 && $Element.levelToBuild == 0}{$LNG.bd_build}{else}{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}{/if}</button>
            </form>
          {/if}

          <br>
          {$LNG.fgf_time}:<span class="statictime" timestamp="{$Element.elementTime}"></span>
          {if $Element.level > 0}
            {if $ID == 43}<a href="#" onclick="return Dialog.info({$ID})">{$LNG.bd_jump_gate_action}</a>{/if}
            {if $Element.destroyable}<br><a class="tooltip_sticky" data-tooltip-content="
            {* Start Destruction Popup *}
            <table style='width:300px'>
              <tr>
                <th colspan='2'>{$LNG.bd_price_for_destroy} {$LNG.tech.{$ID}} {$Element.level}</th>
              </tr>
              {foreach $Element.destroyResources as $ResType => $ResCount}
              <tr>
                <td>{$LNG.tech.{$ResType}}</td>
                <td><span class='{if empty($Element.destroyOverflow[$ResType])}colorPositive{else}colorNeutral{/if}'>{number_format($ResCount, 0, ",", ".")}</span></td>
              </tr>
              {/foreach}
              <tr>
                <td>{$LNG.bd_destroy_time}</td>
                <td><span class='statictime' timestamp='{$Element.elementTime}'></span><script>showtimes()</script></td>
              </tr>
              <tr>
                <td colspan='2'>
                  <form action='game.php?page=buildings' method='post' class='build_form'>
                    <input type='hidden' name='cmd' value='destroy'>
                    <input type='hidden' name='building' value='{$ID}'>
                    <button type='submit' class='build_submit onlist'>{$LNG.bd_dismantle}</button>
                  </form>
                </td>
              </tr>
            </table>
            {* End Destruction Popup *}
            ">{$LNG.bd_dismantle}</a>{/if}
          {else}
            &nbsp;
          {/if}
        </div>
      </div>
    {/foreach}
  </div>
{/block}
{block name="script"}
  <script>
    $(function() {
      $("#btn1").on('click', function() {
        $(".prod").show();
        $(".storage").hide();
        $(".other").hide();
        $("#btn2, #btn3, #btn4").removeClass("selected");

        $(this).addClass("selected");
      });
    });
    $(function() {
      $("#btn2").on('click', function() {
        $(".prod").hide();
        $(".storage").show();
        $(".other").hide();
        $("#btn1, #btn3, #btn4").removeClass("selected");

        $(this).addClass("selected");
      });
    });
    $(function() {
      $("#btn3").on('click', function() {
        $(".prod").hide();
        $(".storage").hide();
        $(".other").show();
        $("#btn1, #btn2, #btn4").removeClass("selected");

        $(this).addClass("selected");
      });
    });
    $(function() {
      $("#btn4").on('click', function() {
        $(".prod").show();
        $(".storage").show();
        $(".other").show();
        $("#btn1, #btn2, #btn3").removeClass("selected");

        $(this).addClass("selected");
      });
    });
  </script>
{/block}
