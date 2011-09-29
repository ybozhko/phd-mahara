<p>Create new fragments for your concept examples:</p>
<ul>
<li><a href="{$WWWROOT}artefact/file">Files</a></li>
<li><a href="{$WWWROOT}artefact/blog">Blogs</a></li>
<li><a href="{$WWWROOT}concept/bookmark">Bookmarks</a></li>
</ul>
<p>Choose free fragments as your concept examples:</p>
{if $free}
	{foreach $free as f}
		<input type='checkbox' name='db[]' class="db" value='{$f->id}' />{$f->title} ({$f->type}) <br/>
	{/foreach}
{else}
	<p>No free fragments available.</p>
{/if}