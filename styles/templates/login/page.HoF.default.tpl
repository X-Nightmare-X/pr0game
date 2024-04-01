{block name="title" prepend}{$LNG.hof_header}{/block}


  {block name="content"}

    <style>
      .HOF-Table th, .HOF-Table td { border: solid 1px black }
      .HOF-Table td { padding: 7px !important }
  </style>
  <table class="HOF-Table">
    <tr>
      <th>
        {$LNG.theme}
      </th>
      <th>
        {$LNG.players}
      </th>
      <th>
        {$LNG.points_amount}
      </th>
    </tr>
    <tr>
      <td>
        {$LNG.st_points}
      </td>
      <td>
          <span class="gold" style="color: gold;">1.</span> Horus
          <br><span class="silver" style="color: silver;">2.</span> Abstaupbaer 
          <br><span class="bronze" style="color: #8C7853;">3.</span> Leichtmatrose
      </td>
      <td>
        792.260<br>
        544.536<br>
        433.687
      </td>
    </tr>
    <tr>
      <td>
        {$LNG.st_fleets}
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> Helli
        <br><span class="silver" style="color: silver;">2.</span> Thorv 
        <br><span class="bronze" style="color: #8C7853;">3.</span> crenox-
      </td>
      <td>
        272.007<br>
        247.984<br>
        234.567
      </td>
    </tr>
    <tr>
      <td>
        {$LNG.st_researh}
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> Abstaupbaer
        <br><span class="silver" style="color: silver;">2.</span> onizuka31 
        <br><span class="bronze" style="color: #8C7853;">3.</span> Ballermann6
      </td>
      <td>
        107.078<br>
        98.003<br>
        81.741
      </td>
    </tr>
    <tr>
      <td>
        {$LNG.st_buildings}
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> Horus
        <br><span class="silver" style="color: silver;">2.</span> Krambimbambambuli 
        <br><span class="bronze" style="color: #8C7853;">3.</span> Oconna
      </td>
      <td>
        383.710<br>
        313.506<br>
        312.270
      </td>
    </tr>
    <tr>
      <td>
        {$LNG.st_defenses}
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> Horus
        <br><span class="silver" style="color: silver;">2.</span> Suicide Emotion 
        <br><span class="bronze" style="color: #8C7853;">3.</span> Stevy
      </td>
      <td>
        237.231<br>
        105.545<br>
        98.581
      </td>
    </tr>



    <tr>
      <td>
        {$LNG.biggest_solo}
      </td>
      <td>
      <span class="gold" style="color: gold;">1.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=9a24baa6c5d58374a52b7a641f4ebb6b">
          <span class="colorPositive">Helli</span> vs <span class="colorNegative">der MondInspekt0r</span>
        </a>
        <br><span class="silver" style="color: silver;">2.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=42405f1c8f9ae9485bcec63fd795832f">
          <span class="colorPositive">Thorv</span> vs <span class="colorNegative">Erdnussallergie</span>
        </a>
        <br><span class="bronze" style="color: #8C7853;">3.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=bb6166d62ca68111cd3223526e86ce46">
          <span class="colorPositive">Thorv</span> vs <span class="colorNegative">MuliPark</span>
        </a>
      </td>
      <td>
        83.031.000 Units
        <br>56.367.000 Units
        <br>31.870.000 Units
      </td>
    </tr>
    <tr>
      <!-- 
select * from uni1_raports ur where time > 1701556855 and attacker != '' and defender != '' and raport like '%i:0;d:100;i:1;d:100;i:2;d:100;%' order by time asc limit 10 
      -->
      <td>
        {$LNG.first_fights}
      </td>
      <td>
      <span class="gold" style="color: gold;">1.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=6f6160db5bb35df0fc8b1c64fd5ce57e">
          <span class="colorNegative">Kenna</span> vs <span class="colorPositive">Quitsche3nTe</span>
        </a>
        <br><span class="silver" style="color: silver;">2.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=bfe3aef0c26384946f5308a841ca480a">
          <span class="colorPositive">cheesus</span> vs <span class="colorNegative">Purple Turtle</span>
        </a>
        <br><span class="bronze" style="color: #8C7853;">3.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=7af745ebff83c3f119cc438bfee3a703">
          <span class="colorPositive">blam</span> vs <span class="colorNegative">KarltoffelD</span>
        </a>
      </td>
      <td>
        03. Dez 2023, 00:04:15
        <br>03. Dez 2023, 03:09:28
        <br>03. Dez 2023, 03:14:08
      </td>
    </tr>
    <tr>
    <!--
select SUM(destroyed_212) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        {$LNG.destroyed_sats}
      </td>
      <td colspan="2">
        58.233
      </td>
    </tr>
    <tr>
    <!--
SELECT
(SELECT username FROM uni1_users WHERE id = (SELECT id FROM uni1_users WHERE universe = 3 ORDER BY wons DESC LIMIT 1)) AS player_with_most_wins,
(SELECT username FROM uni1_users WHERE id = (SELECT id FROM uni1_users WHERE universe = 3 ORDER BY loos DESC LIMIT 1)) AS player_with_most_losses,
(SELECT username FROM uni1_users WHERE id = (SELECT id FROM uni1_users WHERE universe = 3 ORDER BY draws DESC LIMIT 1)) AS player_with_most_draws;
    -->
      <td>
        {$LNG.most_fights}
      </td>
      <td>
        HaRdCoR3
        <br>Eckbert
        <br>Ballermann6
      </td>
      <td>
        1.153
        <br>770
        <br>13
      </td>
    </tr>
    <tr>
    <!--
select uni1_advanced_stats.moons_destroyed, uni1_users.username from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
WHERE moons_destroyed != 0
    -->
      <td>
        {$LNG.most_destroyed_moons}
      </td>
      <td>
      <span class="gold" style=" color: gold;">1.</span> HaRdCoR3
      <br><span class="gold" style=" color: gold;">1.</span> Tsaranoga
      <br><span class="silver" style=" color: silver;">3.</span> knapp20cm
      </td>
      <td>
        5
        <br>5
        <br>2
      </td>
    </tr>


    <tr>
    <!--
SELECT uni1_users.username, COUNT(uni1_users_to_achievements.achievementID) AS achievement_count
FROM uni1_users_to_achievements
JOIN uni1_users ON uni1_users_to_achievements.userID = uni1_users.id
WHERE uni1_users.universe = 3
GROUP BY uni1_users_to_achievements.userID
ORDER BY achievement_count DESC
    -->
      <td>
        {$LNG.most_achievements}
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> Tsaranoga
        <br><span class="silver" style=" color: silver;">2.</span> HaRdCoR3
        <br><span class="bronze" style=" color: #8C7853;">3.</span> Helli
        <br><span class="bronze" style=" color: #8C7853;">3.</span> Nanix
      </td>
      <td>
        29
        <br>21
        <br>17
        <br>17
      </td>
    </tr>
    <tr>
    <!--
SELECT uni1_achievements.name, uni1_users_to_achievements.achievementID, COUNT(*) AS achievement_count
FROM uni1_users_to_achievements
JOIN uni1_achievements ON uni1_users_to_achievements.achievementID = uni1_achievements.id
JOIN uni1_users ON uni1_users_to_achievements.userID = uni1_users.id AND uni1_users.universe = 3
GROUP BY uni1_users_to_achievements.achievementID
ORDER BY achievement_count DESC
LIMIT 5;
    -->
      <td>
        {$LNG.most_reached_ach}
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> #34 Edelfarm
        <br><span class="silver" style=" color: silver;">2.</span> #19 Dicke Haut 1
        <br><span class="bronze" style=" color: #8C7853;">3.</span> #51 Ich weiß das nicht das kommt von alleine
        <br><span class="bronze" style=" color: #8C7853;">3.</span> #20 Dicke Haut 2
      </td>
      <td>
        224
        <br>132
        <br>99
        <br>99
      </td>
    </tr>
    <tr>
    <!--
SELECT uni1_achievements.name, uni1_users_to_achievements.achievementID, COUNT(*) AS achievement_count
FROM uni1_users_to_achievements
JOIN uni1_achievements ON uni1_users_to_achievements.achievementID = uni1_achievements.id
JOIN uni1_users ON uni1_users_to_achievements.userID = uni1_users.id AND uni1_users.universe = 3
GROUP BY uni1_users_to_achievements.achievementID
ORDER BY achievement_count ASC
LIMIT 5;

SELECT u.username
FROM uni1_users_to_achievements ua
JOIN uni1_users u ON ua.userID = u.id
WHERE ua.achievementID = 10 and u.universe = 3;
    -->
      <td>
      {$LNG.least_reached_ach}
      </td>
      <td colspan="2">
        <span class="gold" style=" color: gold;">1.</span> Because I can erreicht von Tsaranoga
        <br><span class="gold" style=" color: gold;">1.</span> Dicke Haut 5 erreicht von Horus  
        <br><span class="gold" style=" color: silver;">2.</span> Größenwahn erreicht von Tsaranoga, SpaceLord    
        <br><span class="gold" style=" color: silver;">2.</span> Betrunken am Saven erreicht von unnamed_1, Dschibuti  
        <br><span class="silver" style=" color: silver;">2.</span> Jack the Ripper erreicht von Tsaranoga, HaRdCoR3
      </td>
    </tr>
    <tr>
    <!--
SELECT uni1_records.userID, uni1_users.username, COUNT(*) AS entry_count
FROM uni1_records
INNER JOIN uni1_users ON uni1_records.userID = uni1_users.id
WHERE uni1_records.universe = 3
GROUP BY uni1_records.userID
ORDER BY entry_count DESC 
Limit 7
    -->
      <td>
        {$LNG.most_records}
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> onizuka31
        <br><span class="silver" style=" color: silver;">2.</span> SpaceLord
        <br><span class="silver" style=" color: bronze;">3.</span> p3t
        <br><span class="silver" style=" color: bronze;">3.</span> Abstaupbaer
      </td>
      <td>
        10
        <br>8
        <br>5
        <br>5
      </td>
    </tr>
    <tr>
    <!--
SELECT uni1_users.username, uni1_session.userID, MAX(uni1_session.lastonline) AS max_lastonline
FROM uni1_session
JOIN uni1_users ON uni1_session.userID = uni1_users.id AND uni1_users.universe = 3
WHERE FROM_UNIXTIME(uni1_session.lastonline+60*60*1,'%H:%i:%s %a %d %b %Y') < FROM_UNIXTIME(UNIX_TIMESTAMP()+60*60*1-(24*60*60), '%H:%i:%s %a %d %b %Y')
GROUP BY uni1_session.userID
ORDER BY uni1_session.userID ASC;
    -->
      <td>
        {$LNG.oldest_active_acc}
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> Helli
        <br><span class="silver" style=" color: silver;">2.</span> Putzvarruckt
        <br><span class="silver" style=" color: #8C7853;">3.</span> Leichtmatrose
      </td>
      <td>
        Sat Nov 11 2023 09:40:34
        <br>Sat Nov 11 2023 09:42:23
        <br>Sat Nov 11 2023 09:45:39
      </td>
    </tr>
    <tr>
    <!--
select SUM(uni1_advanced_stats.build_202 + uni1_advanced_stats.build_203 + uni1_advanced_stats.build_204 + uni1_advanced_stats.build_205 + uni1_advanced_stats.build_206 + uni1_advanced_stats.build_207 + uni1_advanced_stats.build_208 + uni1_advanced_stats.build_209 + uni1_advanced_stats.build_210 + uni1_advanced_stats.build_211 + uni1_advanced_stats.build_212 + uni1_advanced_stats.build_213 + uni1_advanced_stats.build_214  + uni1_advanced_stats.build_215  + uni1_advanced_stats.build_216  + uni1_advanced_stats.build_217  + uni1_advanced_stats.build_218  + uni1_advanced_stats.build_219 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3

select SUM(uni1_advanced_stats.build_204 + uni1_advanced_stats.build_205 + uni1_advanced_stats.build_206 + uni1_advanced_stats.build_207 + uni1_advanced_stats.build_211 + uni1_advanced_stats.build_213 + uni1_advanced_stats.build_214  + uni1_advanced_stats.build_215  + uni1_advanced_stats.build_216  + uni1_advanced_stats.build_217  + uni1_advanced_stats.build_218  + uni1_advanced_stats.build_219 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3

select SUM(uni1_advanced_stats.build_202 + uni1_advanced_stats.build_203 + uni1_advanced_stats.build_208 + uni1_advanced_stats.build_209 + uni1_advanced_stats.build_212 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3

select SUM(uni1_advanced_stats.build_210 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        {$LNG.build_ships}
      </td>
      <td>
      {$LNG.overall}
      <br> {$LNG.civil}
      <br> {$LNG.military}
      <br> {$LNG.spy}
    </td>
    <td>
      597.660
      <br>205.001
      <br>385.556
      <br>7.103
      </td>
    </tr>


   
    <tr>
    <!--
select SUM(uni1_advanced_stats.found_202 + uni1_advanced_stats.found_203 + uni1_advanced_stats.found_204 + uni1_advanced_stats.found_204 + uni1_advanced_stats.found_205 + uni1_advanced_stats.found_206 + uni1_advanced_stats.found_207 + uni1_advanced_stats.found_208 + uni1_advanced_stats.found_209 + uni1_advanced_stats.found_210 + uni1_advanced_stats.found_211 + uni1_advanced_stats.found_212 + uni1_advanced_stats.found_213 + uni1_advanced_stats.found_214  + uni1_advanced_stats.found_215  + uni1_advanced_stats.found_216  + uni1_advanced_stats.found_217  + uni1_advanced_stats.found_218  + uni1_advanced_stats.found_219 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3

select SUM(uni1_advanced_stats.found_204 + uni1_advanced_stats.found_204 + uni1_advanced_stats.found_205 + uni1_advanced_stats.found_206 + uni1_advanced_stats.found_207 + uni1_advanced_stats.found_211 + uni1_advanced_stats.found_213 + uni1_advanced_stats.found_214  + uni1_advanced_stats.found_215  + uni1_advanced_stats.found_216  + uni1_advanced_stats.found_217  + uni1_advanced_stats.found_218  + uni1_advanced_stats.found_219 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3

select SUM(uni1_advanced_stats.found_202 + uni1_advanced_stats.found_203 +  uni1_advanced_stats.found_208 + uni1_advanced_stats.found_209 + uni1_advanced_stats.found_212 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3

select SUM(uni1_advanced_stats.found_210) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        {$LNG.found_ships}
      </td>
      <td>
        {$LNG.overall}
        <br> {$LNG.civil}
        <br> {$LNG.military}
        <br> {$LNG.spy}
      </td>
      <td>
        199.748
        <br>32.921
        <br>60.089
        <br>106.738
      </td>
    </tr>
    <tr>
    <!--
select SUM(uni1_advanced_stats.found_901) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
WHERE found_901 != 0
select SUM(uni1_advanced_stats.found_902) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
WHERE found_902 != 0
select SUM(uni1_advanced_stats.found_903) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
WHERE found_903 != 0
    -->
      <td>
        {$LNG.found_res}
      </td>
      <td>
        Metal
        <br>Kristal
        <br>Deuterium
      </td>
      <td>
        575.323.490
        <br>286.100.176
        <br>190.827.070
      </td>
    </tr>
    <tr>
    <!--
select uni1_raports.attacker, uni1_users.username, COUNT(*) AS count_attacks
from uni1_raports 
inner Join uni1_users ON uni1_users.id = uni1_raports.attacker
WHERE uni1_users.universe = 3 /*AND 
uni1_raports.time > 1701385200 And 
uni1_raports.time < 1701795600 
ORDER BY uni1_raports.time ASC*/
AND defender = ''
GROUP BY uni1_raports.attacker
ORDER BY count_attacks DESC
LIMIT 3;
      -->
      <td>
        {$LNG.most_expo_fights}
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> der MondInspekt0r
        <br><span class="silver" style=" color: silver;">2.</span> Horus
        <br><span class="bronze" style=" color: #8C7853;">3.</span> Helli
      </td>
      <td>
        132
        <br>126
        <br>119
      </td>
    </tr>
    <tr>
    <!--
select uni1_advanced_stats.expo_black_hole, uni1_users.username from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
WHERE expo_black_hole != 0
ORDER by expo_black_hole DESC 
    -->
      <td>
        {$LNG.most_black_holes}
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> Horus
        <br><span class="silver" style=" color: silver;">2.</span> Nordwing
        <br><span class="silver" style=" color: silver;">2.</span> Helli
        <br><span class="silver" style=" color: silver;">2.</span> HaRdCoR3
      </td>
      <td>
        9
        <br>7
        <br>7
        <br>7
      </td>
    </tr>
    <tr>
    <!--
select SUM(expo_count) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        {$LNG.flown_expos}
      </td>
      <td colspan="2">
        34.668
      </td>
    </tr>
  <table>

  {/block}
