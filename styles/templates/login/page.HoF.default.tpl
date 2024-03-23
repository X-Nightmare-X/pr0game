{block name="title" prepend}{$LNG.buttonHoF}{/block}


  {block name="content"}

    <style>
      .HOF-Table th, .HOF-Table td { border: solid 1px black }
      .HOF-Table td { padding: 7px !important }
  </style>
  <h1>
    Hier sind die Legenden von Uni 3 verewigt!
  </h1>
  <table class="HOF-Table">
    <tr>
      <th>
        Thema
      </th>
      <th>
        Spieler
      </th>
      <th>
        Punkte / Anzahl
      </th>
    </tr>
    <tr>
      <td>
        Gesammtpunkte
      </td>
      <td>
          <span class="gold" style="color: gold;">1.</span> foobar
          <br><span class="silver" style="color: silver;">2.</span> foobar 
          <br><span class="bronze" style="color: #8C7853;">3.</span> foobar
      </td>
      <td>
        1234
      </td>
    </tr>
    <tr>
      <td>
        Flotte
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> foobar
        <br><span class="silver" style="color: silver;">2.</span> foobar 
        <br><span class="bronze" style="color: #8C7853;">3.</span> foobar
      </td>
      <td>
        1234
      </td>
    </tr>
    <tr>
      <td>
        Forschung
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> foobar
        <br><span class="silver" style="color: silver;">2.</span> foobar 
        <br><span class="bronze" style="color: #8C7853;">3.</span> foobar
      </td>
      <td>
        1234
      </td>
    </tr>
    <tr>
      <td>
        Gebäude
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> foobar
        <br><span class="silver" style="color: silver;">2.</span> foobar 
        <br><span class="bronze" style="color: #8C7853;">3.</span> foobar
      </td>
      <td>
        1234
      </td>
    </tr>
    <tr>
      <td>
        Verteidigung
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> foobar
        <br><span class="silver" style="color: silver;">2.</span> foobar 
        <br><span class="bronze" style="color: #8C7853;">3.</span> foobar
      </td>
      <td>
        1234
      </td>
    </tr>
    <tr>
      <td>
        Größter solo KB
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> foobar
        <br><span class="silver" style="color: silver;">2.</span> foobar 
        <br><span class="bronze" style="color: #8C7853;">3.</span> foobar
      </td>
      <td>
        foobar
        <br>foobar
        <br>foobar
      </td>
    </tr>
    <tr>
      <td>
        Meisten Ress bei einem Raid
      </td>
      <td>
        <span class="gold" style="color: gold;">1.</span> foobar
        <br><span class="silver" style="color: silver;">2.</span> foobar 
        <br><span class="bronze" style="color: #8C7853;">3.</span> foobar
      </td>
      <td>
        foobar
        <br>foobar
        <br>foobar
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
        Meisten Archivemnts
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> foobar
        <br><span class="silver" style=" color: silver;">2.</span> foobar
        <br><span class="bronze" style=" color: #8C7853;">3.</span> foobar
        <br><span class="bronze" style=" color: #8C7853;">3.</span> foobar
        <br><span class="bronze" style=" color: #8C7853;">3.</span> foobar
        <br><span class="bronze" style=" color: #8C7853;">3.</span> foobar
        <br><span class="bronze" style=" color: #8C7853;">3.</span> foobar
      </td>
      <td>
        19
        <br>17
        <br>16
        <br>16
        <br>16
        <br>16
        <br>16
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
        die meistvergebenen Archivments
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> #34 Edelfarm
        <br><span class="silver" style=" color: silver;">2.</span> #19 Dicke Haut 1
        <br><span class="bronze" style=" color: #8C7853;">3.</span> #51 Ich weiß das nicht das kommt von alleine
      </td>
      <td>
        141
        <br>108
        <br>107
      </td>
    </tr>
    <tr>
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
      die seltensten Archivments
      </td>
      <td colspan="2">
        <span class="gold" style=" color: gold;">1.</span> #17 erreicht von foobar
        <br><span class="gold" style=" color: gold;">1.</span> #23 erreicht von foobar  
        <br><span class="gold" style=" color: gold;">1.</span> #37 erreicht von foobar    
        <br><span class="gold" style=" color: gold;">1.</span> #47 erreicht von foobar      
        <br><span class="gold" style=" color: gold;">1.</span> #48 erreicht von foobar    
        <br><span class="gold" style=" color: gold;">1.</span> #52 erreicht von foobar    
        <br><span class="silver" style=" color: silver;">2.</span> #3 erreicht von foobar, foobar    
        <br><span class="bronze" style=" color: #8C7853;">3.</span> #9 erreicht von foobar, foobar, foobar    
        <br><span class="bronze" style=" color: #8C7853;">3.</span> #10 erreicht von foobar, foobar, foobar      
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
        Meisten Rekorde
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> foobar
        <br><span class="silver" style=" color: silver;">2.</span> foobar
        <br><span class="silver" style=" color: silver;">2.</span> foobar
        <br><span class="silver" style=" color: silver;">2.</span> foobar
      </td>
      <td>
        8
        <br>7
        <br>7
        <br>7
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
        Meiste kämpfe gegen Piraten/Aliens
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> foobar
        <br><span class="silver" style=" color: silver;">2.</span> foobar
        <br><span class="bronze" style=" color: #8C7853;">3.</span> foobar
      </td>
      <td>
        127
        <br>119
        <br>106
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
        Erste KB's im Uni
      </td>
      <td>
      <span class="gold" style="color: gold;">1.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=foobar">
          <span class="colorNegative">foobar</span> vs <span class="colorPositive">foobar</span>
        </a>
        <br><span class="silver" style="color: silver;">2.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=foobar">
          <span class="colorPositive">foobar</span> vs <span class="colorNegative">foobar</span>
        </a>
        <br><span class="bronze" style="color: #8C7853;">3.</span> <a href="https://pr0game.com/uni3/game.php?page=raport&raport=foobar">
          <span class="colorPositive">foobar</span> vs <span class="colorNegative">foobar</span>
        </a>
      </td>
      <td>
        foobar
        <br>foobar
        <br>foobar
      </td>
    </tr>
    <tr>
    <!--
select uni1_advanced_stats.moons_destroyed, uni1_users.username from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
WHERE moons_destroyed != 0
    -->
      <td>
        Die meisten Zerstörten Monde
      </td>
      <td>
      <span class="gold" style=" color: gold;">1.</span> foobar
      <br><span class="silver" style=" color: silver;">2.</span> foobar
      {* <br><span class="bronze" style=" color: #8C7853;">3.</span> Helli *}
      </td>
      <td>
        foobar
        <br>foobar
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
        Die ältesten noch aktiven Accounts
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> foobar
        <br><span class="silver" style=" color: silver;">2.</span> foobar
        <br><span class="silver" style=" color: #8C7853;">3.</span> foobar
      </td>
      <td>
        8
        <br>7
        <br>6
      </td>
    </tr>
    <tr>
    <!--
select SUM(uni1_advanced_stats.build_202 + uni1_advanced_stats.build_203 + uni1_advanced_stats.build_204 + uni1_advanced_stats.build_204 + uni1_advanced_stats.build_205 + uni1_advanced_stats.build_206 + uni1_advanced_stats.build_207 + uni1_advanced_stats.build_208 + uni1_advanced_stats.build_209 + uni1_advanced_stats.build_210 + uni1_advanced_stats.build_211 + uni1_advanced_stats.build_212 + uni1_advanced_stats.build_213 + uni1_advanced_stats.build_214  + uni1_advanced_stats.build_215  + uni1_advanced_stats.build_216  + uni1_advanced_stats.build_217  + uni1_advanced_stats.build_218  + uni1_advanced_stats.build_219 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        Insgesammt gebauten Schiffe
      </td>
      <td>
      Gesammt
      <br> Zivil
      <br> Militär
      <br> Spiosonden
    </td>
    <td>
      865.834
      <br>104.088
      <br>755.283
      <br>6.549
      </td>
    </tr>
    <tr>
    <tr>
    <!--
select SUM(uni1_advanced_stats.found_202 + uni1_advanced_stats.found_203 + uni1_advanced_stats.found_204 + uni1_advanced_stats.found_204 + uni1_advanced_stats.found_205 + uni1_advanced_stats.found_206 + uni1_advanced_stats.found_207 + uni1_advanced_stats.found_208 + uni1_advanced_stats.found_209 + uni1_advanced_stats.found_210 + uni1_advanced_stats.found_211 + uni1_advanced_stats.found_212 + uni1_advanced_stats.found_213 + uni1_advanced_stats.found_214  + uni1_advanced_stats.found_215  + uni1_advanced_stats.found_216  + uni1_advanced_stats.found_217  + uni1_advanced_stats.found_218  + uni1_advanced_stats.found_219 ) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        Insgesammt gefundene Schiffe in Expos
      </td>
      <td>
        Gesammt
        <br> Zivil
        <br> Militär
        <br> Spiosonden
      </td>
      <td>
        208.495
        <br>34.267
        <br>63.074
        <br>111.156
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
        Insgesammt gefundene Ressourcen in Expos
      </td>
      <td>
        Metal
        <br>Kristal
        <br>Deuterium
      </td>
      <td>
        600.459.699
        <br>299.903.958
        <br>199.500.354
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
        meisten Siege, Unentschieden & Niederlagen
      </td>
      <td>
        foobar
        <br>foobar
        <br>foobar
      </td>
      <td>
        8
        <br>7
        <br>6
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
        Meisten Schwarzen Löcher
      </td>
      <td>
        <span class="gold" style=" color: gold;">1.</span> foobar
        <br><span class="silver" style=" color: silver;">2.</span> foobar
        <br><span class="silver" style=" color: #8C7853;">3.</span> foobar
      </td>
      <td>
        8
        <br>7
        <br>6
      </td>
    </tr>
    <tr>
    <!--
select SUM(expo_count) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        geflogene Expos
      </td>
      <td colspan="2">
        38.133
      </td>
    </tr>
    <tr>
    <!--
select SUM(destroyed_212) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        Yerstörte Solasateliten
      </td>
      <td colspan="2">
        44.581
      </td>
    </tr>
    <tr>
    <!--
select SUM(destroyed_212) from uni1_advanced_stats
JOIN uni1_users ON uni1_advanced_stats.userId = uni1_users.id AND uni1_users.universe = 3
    -->
      <td>
        Yerstörte Solasateliten
      </td>
      <td colspan="2">
        44.581
      </td>
    </tr>
  <table>

  {/block}
