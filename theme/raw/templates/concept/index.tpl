{include file="header.tpl"}
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}concept/edit.php?new=1">{str section="concept" tag="newmap"}</a>
    </div>
<p>{str tag=conceptmapsdescription section=concept}</p>
{if $maps}
    <table id="myviews" class="fullwidth listing">
        <tbody>
        {foreach from=$maps item=map}
        	<tr class="{cycle values='r0,r1'}">
            	<td><div class="rel">
            		<h3>{$map->name}</a></h3>
            		
            		<div class="concept-controls">
            			<a href="{$WWWROOT}concept/edit.php?id={$map->id}" class="btn-edit"></a><a href="{$WWWROOT}concept/delete.php?id={$map->id}" class="btn-del"></a>
            		</div>
            		
					<label>{str tag="open" section="concept"}</label> 
						<strong><a href="{$WWWROOT}concept/map.php?id={$map->id}">{str tag="conceptmap" section="concept"}</a></strong> | 
						<strong><a href="{$WWWROOT}concept/timeline.php?id={$map->id}">{str tag="timeline" section="concept"}</a></strong>
					<br/>
                    	<label>{str tag="timeframe" section="concept"}</label> {$map->frames}
					<br/>
                    	<label>{str tag="access" section="concept"}</label><a href="{$WWWROOT}concept/access.php?map={$map->id}" id="editmapaccess">{str tag="editaccess" section="concept"}</a>
                    
                    <div class="videsc">{$map->description}</div>
                    </div></td>
                 </tr>
        {/foreach}
        </tbody>
    </table>
    {$pagination|safe}
{else}
	<div class="message">{$strnomapsaddone|safe}</div>
{/if}
{include file="footer.tpl"}