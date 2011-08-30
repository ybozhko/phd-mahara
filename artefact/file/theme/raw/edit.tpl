{include file="header.tpl"}
	{if $nosupport}<div class="message">{$nosupport|clean_html|safe}</div>{/if}
	{$form|safe}
{include file="footer.tpl"}