{block name="title" prepend}{$LNG.lm_info}{/block}
{block name="content"}
<table>
	<tbody>
	<tr>
		<th>{$LNG.Achievement_names[$id]}</th>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					{$LNG.Description}: 
    				</br>
    				<span style="max-width: 42%; display: inline-block;">{$LNG.Achievement_text[$id]}</span>			
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</tbody>
</table>
{/block}