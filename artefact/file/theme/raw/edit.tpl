{include file="header.tpl"}
{if $nosupport}
	<div class="message">{$nosupport|clean_html|safe}</div>
	<a class="btn" href="{$WWWROOT}artefact/file">Back</a>
{else}
	{$form|safe}
{/if}
{include file="footer.tpl"}