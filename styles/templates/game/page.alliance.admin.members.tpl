{block name="title" prepend}{$LNG.lm_alliance}{/block}
{block name="content"}
    <form action="game.php?page=alliance&amp;mode=admin&amp;action=membersSave" method="post">
        <table id="memberList" class="tablesorter">
            <thead>
            <tr>
                <th colspan="9">{$al_users_list}</th>
            </tr>
            <tr>
                <th>{$LNG.al_num}</th>
                <th>{$LNG.al_member}</th>
                <th>{$LNG.al_message}</th>
                <th>{$LNG.al_position}</th>
                <th>{$LNG.al_points}</th>
                <th>{$LNG.al_coords}</th>
                <th>{$LNG.al_member_since}</th>
                <th>{$LNG.al_estate}</th>
                <th>{$LNG.al_actions}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $memberList as $userID => $memberListRow}
                <tr>
                    <td>{$memberListRow@iteration}</td>
                    <td><a href="#" onclick="return Dialog.Playercard({$userID},'{$memberListRow.username}');">{$memberListRow.username}</a> {if !empty($memberListRow.class)}{foreach $memberListRow.class as $class}{if !$class@first}&nbsp;{/if}<span class="galaxy-short-{$class} galaxy-short">{$ShortStatus.$class}</span>{/foreach}{/if}</td>
                    <td><a href="#" onclick="return Dialog.PM({$userID});"><img src="{$dpath}img/m.gif" border="0" title="{$LNG.write_message}"></a></td>
                    <td>{if $memberListRow.rankID == -1}{$founder}{elseif !empty($rankSelectList)}{html_options class="rankSelect" name="rank[{$userID}]" options=$rankSelectList selected=$memberListRow.rankID}{else}{$rankList[$memberListRow.rankID]}{/if}</td>
                    <td><span title="{number_format($memberListRow.points, 0, ",", ".")}">{shortly_number($memberListRow.points)}</span></td>
                    <td><a href="game.php?page=galaxy&amp;galaxy={$memberListRow.galaxy}&amp;system={$memberListRow.system}">[{$memberListRow.galaxy}:{$memberListRow.system}:{$memberListRow.planet}]</a></td>
                    <td class="registerTime" data-time="{$memberListRow.register_time}">{$memberListRow.register_time}</td>
                    <td>{if $rights.ONLINESTATE}{if $memberListRow.onlinetime < 4}<span class="colorPositive">{$LNG.al_memberlist_on}</span>{elseif $memberListRow.onlinetime >= 4 && $memberListRow.onlinetime <= 60}<span style="color:yellow">{$memberListRow.onlinetime} {$LNG.al_memberlist_min}</span>{else}<span class="colorNegative">{$LNG.al_memberlist_off}</span>{/if}{else}-{/if}</td>
                    <td>{if $memberListRow.rankID != -1}
                            {if $canKick}<a href="game.php?page=alliance&amp;mode=admin&amp;action=membersKick&amp;id={$userID}" onclick="return confirm('{$memberListRow.kickQuestion}');" style="border: 1px solid #212121;vertical-align:top;width:16px;height:16px;display:inline-block;margin:2px;"><img src="{$dpath}pic/abort.gif" border="0" alt=""></a>{/if}{else}-{/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
            <tr>
                <th colspan="9"><a href="game.php?page=alliance&amp;mode=admin">{$LNG.al_back}</a></th>
            </tr>
        </table>
    </form>
{/block}
{block name="script" append}
    <script src="scripts/base/jquery.tablesorter.js"></script>
    <script>$(function() {
        $.tablesorter.addParser({ 
            id: 'nopoint', 
            is: function(s) { 
                // return false so this parser is not auto detected 
                return false; 
                    }, 
                format: function(s) { 
                // format your data for normalization
                var lNumber = parseFloat(s.replace('.',''));
                return lNumber;
	    	}, 
	    	// set type, either numeric or text 
	    	type: 'numeric' 
	    });
        $("#memberList").tablesorter({
            headers: {
                0: { sorter: false } ,
                3: { sorter: false } ,
                4: { sorter: "nopoint"} ,
                9: { sorter: false }
            },
            debug: false
        });

        $('.rankSelect').on('change', function () {
            $.post('game.php?page=alliance&mode=admin&action=rank&ajax=1', $(this).serialize(), function (data) {
                NotifyBox(data);
            }, 'json');
        });

        var elements = document.getElementsByClassName('registerTime');
        for (var i = 0, l = elements.length; i < l; i++) {
            const date = new Date(elements[i].innerHTML * 1000);
            const day = date.getDate();
            const months = {$LNG.months|@json_encode}
            const monthIndex = date.getMonth();
            const month = months[monthIndex];
            const year = date.getFullYear();
            const formated = date.getDate() + ". " + month + " " + date.getFullYear() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
            elements[i].innerHTML = formated;
        }
    });
    </script>
{/block}
