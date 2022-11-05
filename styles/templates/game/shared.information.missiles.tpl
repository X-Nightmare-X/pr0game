<table>
	<tr>
		<th>{$LNG.in_missilestype}</th><th>{$LNG.in_missilesamount}</th><th>{$LNG.in_destroy}</th>
	</tr>
	<tr>
		<td>{$LNG.tech.502}</td><td><span id="missile_502">{number_format($MissileList.502, 0, ",", ".")}</span></td><td><input class="missile" type="text" name="missile[502]" placeholder="0"></td>
	</tr>
	<tr>
		<td>{$LNG.tech.503}</td><td><span id="missile_503">{number_format($MissileList.503, 0, ",", ".")}</span></td><td><input class="missile" type="text" name="missile[503]" placeholder="0"></td>
	</tr>
	<tr>
		<td colspan="3"><input type="button" value="{$LNG.in_destroy}" onclick="DestroyMissiles();"></td>
	</tr>
</table>