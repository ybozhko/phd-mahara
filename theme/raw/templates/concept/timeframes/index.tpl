{include file="header.tpl"}
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}concept/timeframes/edit.php?new=1">New Timeframe</a>
    </div>
<p>On this page you can manage you custom timeframes that can be used in the timeline</p>
{if $frames}
<table class="fullwidth listing">
	<tbody>
		{foreach from=$frames item=frame}
		<tr><td>
		    <div class="fr">
                <ul class="groupuserstatus">
                    <li><a href="{$WWWROOT}concept/timeframes/edit.php?id={$frame.id}" class="icon btn-edit">{str tag="edit"}</a></li>
                    <li><a href="{$WWWROOT}concept/timeframes/edit.php?del={$frame.id}" class="icon btn-del">{str tag="delete"}</a></li>
                </ul>
            </div>
		<h3>{$frame.name}</h3></td></tr>
		<tr><td>
			<strong>Used in maps:</strong>
			{if $frame.used} 
				{foreach from=$frame.used item=u name=maps}
				<a href='{$WWWROOT}concept/map.php?id={$u->id}'>{$u->name}</a>
				{if !$.foreach.maps.last}, {/if} {/foreach}
			{else}
				None
			{/if}
		</td></tr>
		<tr><td>
			<table width='50%'><tbody>
				{foreach from=$frame.fs item=f}
				<tr class="{cycle values='r0,r1'}">
					<td>{$f->name}</td>
					<td>{$f->start}</td>
					<td>{$f->end}</td>
				</tr>
				{/foreach}
			</tbody></table>
			<hr/>
		</td></tr>
		{/foreach}
	</tbody>
</table>
{else}
No custom timeframes yet
{/if}

{include file="footer.tpl"}