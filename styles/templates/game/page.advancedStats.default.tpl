{block name="title" prepend}{$LNG.lm_records}{/block} {block name="content"}
<style>
    th { text-align: center; }
</style>
<table>
    <tbody>
        <tr onclick="toggle_rows('advanced-expo')">
            <th colspan="2">
                <span id="advanced-expo-toggle">▼</span>
                {$LNG.expedition}
            </th>
        </tr>
        {foreach $expo as $elementID => $elementRow}
            <tr id="{$elementID}" class="advanced-expo">
                <td width="50%">{$LNG.Advanced_Stats.$elementID}</td>
                <td width="50%">{$elementRow}</td>
            </tr>
        {/foreach}
        <tr onclick="toggle_rows('advanced-build')">
            <th colspan="2">
                <span id="advanced-build-toggle">▲</span>
                {$LNG.build}
            </th>
        </tr>
        {foreach $build as $elementID => $elementRow}
            <tr id="{$elementID}" class="hidden advanced-build">
                <td>{$LNG.Advanced_Stats.$elementID}</td>
                <td>{$elementRow}</td>
            </tr>
        {/foreach}
        <tr onclick="toggle_rows('advanced-lost')">
            <th colspan="2">
                <span id="advanced-lost-toggle">▲</span>
                {$LNG.lost}
            </th>
        </tr>
        {foreach $lost as $elementID => $elementRow}
            <tr id="{$elementID}" class="hidden advanced-lost">
                <td>{$LNG.Advanced_Stats.$elementID}</td>
                <td>{$elementRow}</td>
            </tr>
        {/foreach}
        <tr onclick="toggle_rows('advanced-destroyed')">
            <th colspan="2">
                <span id="advanced-destroyed-toggle">▲</span>
                {$LNG.destroyed}
            </th>
        </tr>
        {foreach $destroyed as $elementID => $elementRow}
            <tr id="{$elementID}" class="hidden advanced-destroyed">
                <td>{$LNG.Advanced_Stats.$elementID}</td>
                <td>{$elementRow}</td>
            </tr>
        {/foreach}
        <tr onclick="toggle_rows('advanced-repaired')">
            <th colspan="2">
                <span id="advanced-repaired-toggle">▲</span>
                {$LNG.repaired}
            </th>
        </tr>
        {foreach $repaired as $elementID => $elementRow}
            <tr id="{$elementID}" class="hidden advanced-repaired">
                <td>{$LNG.Advanced_Stats.$elementID}</td>
                <td>{$elementRow}</td>
            </tr>
        {/foreach}
        <tr onclick="toggle_rows('advanced-others')">
            <th colspan="2">
                <span id="advanced-others-toggle">▲</span>
                {$LNG.other}
            </th>
        </tr>
        {foreach $rest as $elementID => $elementRow}
            <tr id="{$elementID}" class="hidden advanced-others">
                <td>{$LNG.Advanced_Stats.$elementID}</td>
                <td>{$elementRow}</td>
            </tr>
        {/foreach}
    </tbody>
</table>
{/block}
{block name="script" append}
	<script>
		function toggle_rows(target) {
			const rows = document.querySelectorAll("." + target)
			if (rows[0].classList.contains("hidden")) {
				rows.forEach(r => r.classList.remove("hidden"))
				document.getElementById(target + "-toggle").innerText = "▼"
			} else {
				rows.forEach(r => r.classList.add("hidden"))
				document.getElementById(target + "-toggle").innerText = "▲"
			}
		}
	</script>
{/block}