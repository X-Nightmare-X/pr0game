{block name="title" prepend}{$LNG.lm_records}{/block} {block name="content"}
<table>
    <tbody>
        <tr>
            <th colspan="4" style="text-align:center;">{$LNG.rec_last_update_on}: {$update}</th>
        </tr>
        <tr>
            <th width="25%">{$LNG.tech.0}</th>
            <th width="25%">{$LNG.rec_players}</th>
            <th width="25%">{$LNG.rec_level}</th>
            <th width="25%">{$LNG.own}</th>
        </tr>
        {foreach $buildList as $elementID => $elementRow}
        <tr>
            <td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.{$elementID}}</a>
            </td>
            {if !empty($elementRow) && count($elementRow) <= 3}
            <td>
                {$test = 0}
                {foreach $elementRow as $user}
                    {if $user.records_optIn == 1}
                    {if $test !== 0}<br>{/if}
                        <a href='#' onclick='return Dialog.Playercard({$user.userID});'>{$user.username}</a>
                        {$test = $test+1}
                    {/if}
                {/foreach}
                {if $test == 0}
                    <a href='#' onclick=''>-</a>
                    <br><br><br>
                {elseif $test == 1}
                    <br><br><br>
                {elseif $test == 2}
                    <br><br>
                {/if}
            </td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) < 10}
            <td>3+<br><br><br></td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) < 50}
            <td>10+<br><br><br></td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) < 100}
            <td>50+<br><br><br></td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) >= 100}
            <td>100+<br><br><br></td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {else}
            <td>-</td>
            <td>-</td>
            {/if}
            {if !empty($elementRow)}
                <td>{$userBuild[$elementRow[0]["name"]]}</td>
            {else}
                <td>-<br><br><br></td>
                
            {/if}
        </tr>
        {/foreach}
        <tr>
            <th>{$LNG.tech.100}</th>
            <th>{$LNG.rec_players}</th>
            <th>{$LNG.rec_level}</th>
            <th>{$LNG.own}</th>
        </tr>
        {foreach $researchList as $elementID => $elementRow}
        <tr>
            <td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.{$elementID}}</a></td>
            {if !empty($elementRow)}
            <td>
                {$test = 0}
                {foreach $elementRow as $user}
                    {if $user.records_optIn == 1}
                        {if $test !== 0}<br>{/if}
                        <a href='#' onclick='return Dialog.Playercard({$user.userID});'>{$user.username}</a>
                        {$test = $test+1}
                    {/if}
                {/foreach}
                {if $test == 0}
                    <a href='#' onclick=''>-</a>
                    <br><br><br>
                {elseif $test == 1}
                    <br><br><br>
                {elseif $test == 2}
                    <br><br>
                {/if}
            </td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) < 10}
            <td>3+</td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) < 50}
            <td>10+</td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) < 100}
            <td>50+</td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {elseif !empty($elementRow) && count($elementRow) >= 100}
            <td>100+</td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {else}
            <td>-</td>
            <td>-</td>
            {/if}
            {if !empty($elementRow)}
                <td>{$userTech[$elementRow[0]["name"]]}</td>
            {else}
                <td>-</td>
            {/if}
        </tr>
        {/foreach}
        <!--tr>
            <th>{$LNG.tech.200}</th>
            <th>{$LNG.rec_players}</th>
            <th>{$LNG.rec_count}</th>
        </tr-->
        {*foreach $fleetList as $elementID => $elementRow}
        <tr>
            <td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.{$elementID}}</a></td>
            {if !empty($elementRow)}
            <td>{foreach $elementRow as $user}<a href='#' onclick='return Dialog.Playercard({$user.userID});'>{$user.username}</a>{if !$user@last}<br>{/if}{/foreach}</td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {else}
            <td>-</td>
            <td>-</td>
            {/if}
        </tr>
        {/foreach}
        <tr>
            <th>{$LNG.tech.400}</th>
            <th>{$LNG.rec_players}</th>
            <th>{$LNG.rec_count}</th>
        </tr>
        {foreach $defenseList as $elementID => $elementRow}
        <tr>
            <td><a href='#' onclick='return Dialog.info({$elementID})' class='tooltip' data-tooltip-content="<table><tr><th>{$LNG.tech.{$elementID}}</th></tr><tr><table class='hoverinfo'><tr><td><img src='{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}'></td><td>{$LNG.shortDescription.$elementID}</td></tr></table></tr></table>">{$LNG.tech.{$elementID}}</a></td>
            {if !empty($elementRow)}
            <td>{foreach $elementRow as $user}<a href='#' onclick='return Dialog.Playercard({$user.userID});'>{$user.username}</a>{if !$user@last}<br>{/if}{/foreach}</td>
            <td>{number_format($elementRow[0].level, 0, ",", ".")}</td>
            {else}
            <td>-</td>
            <td>-</td>
            {/if}
        </tr>
        {/foreach*}
    </tbody>
</table>
{/block}
