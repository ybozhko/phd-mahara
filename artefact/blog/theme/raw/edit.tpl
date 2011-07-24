{include file="header.tpl"}
{if $noposts}
	<div class="message">{$noposts|clean_html|safe}</div>
	<a class="btn" href="{$WWWROOT}artefact/blog/post.php?blog={$id}">New Post</a>
{else}
	{$form|safe}
{/if}
{include file="footer.tpl"}