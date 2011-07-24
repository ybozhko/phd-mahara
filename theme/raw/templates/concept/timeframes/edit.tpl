{include file="header.tpl"}

{if $message}
	<div class="message delete">
		<p>{$message}</p>
		{$form|safe}
	</div>
{else}
	{$form|safe}
{/if}

{include file="footer.tpl"}