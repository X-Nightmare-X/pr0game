{block name="title" prepend}Translation{/block}
{nocache}
{block name="content"}
<table style="width:100%">
	<tr>
		<th>Translation</th>
	</tr>
	<tr>
		<td class="transparent" style="padding:0;">
			<div id="tabs">
				<ul>
					{foreach $categories as $category}
						<li><a href="#tabs-{$category}">{$category}</a></li>
					{/foreach}
				</ul>
				{foreach $translations as $category => $langs}
					<div id="tabs-{$category}">
						<form>
						<table>
							<tr>
								{foreach $languages as $language}
									<th>{$language}</th>
								{/foreach}
							</tr>
							{foreach $langs.Key as $key => $keyText}
								<tr>
									{foreach $languages as $language}
										<td style="min-width: 150px;">
											{if !empty($langs.$language.$key)}
												{if $language == 'Key'}
													{$langs.$language.$key}
												{else}
													<textarea name="{$language}.{$key}" rows="{ceil(strlen($langs.$language.$key)/30)}" cols="30">{$langs.$language.$key}</textarea>
												{/if}
											{else}
												-
											{/if}
										</td>
									{/foreach}
								</tr>
							{/foreach}
						</table>
						</form>
					</div>
				{/foreach}
			</div>
		</td>
	</tr>
</table>
{/block}
{/nocache}