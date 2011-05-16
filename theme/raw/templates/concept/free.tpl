<p>Here you can choose free fragments as your concept examples.</p>
{if $free}
	{foreach $free as f}
		<input type='checkbox' name='db[]' class="db" value='{$f->id}' />{$f->title} ({$f->type}) <br/>
	{/foreach}
{else}
	<p>No free fragments available.</p>
{/if}