{include file="header.tpl"}

    <script type="text/javascript">pieformPath = "{$WWWROOT}lib/pieforms/static/core/";</script> 
    <script type="text/javascript" src="{$WWWROOT}lib/pieforms/static/core/pieforms.js"></script> 
    <link rel="stylesheet" type="text/css" media="all" href="{$WWWROOT}theme/raw/static/style/calendar.css"> 
    <script type="text/javascript" src="{$WWWROOT}js/jscalendar/calendar_stripped.js"></script> 
    <script type="text/javascript" src="{$WWWROOT}js/jscalendar/lang/calendar-en.js"></script> 
    <script type="text/javascript" src="{$WWWROOT}js/jscalendar/calendar-setup_stripped.js"></script>

{if $message}
	<div class="message delete">
		<p>{$message}</p>
		{$form|safe}
	</div>
{else}
	{$form|safe}
{/if}

{include file="footer.tpl"}