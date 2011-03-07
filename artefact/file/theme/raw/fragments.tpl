{include file="header.tpl"}
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/file/edit.php?id={$id}&new=1">New fragment</a>
    </div>
{if !$fragments}
	<div class="message">There are no fragments for this item yet.</div>
{else}
	<table class="fullwidth listing">
	<tbody>
	{foreach from=$fragments item=fragment}
		<tr class="{cycle values='r0,r1'}">
			<td>
				<div class="fr">
                	<ul class="groupuserstatus">
                   		<li><a href="{$WWWROOT}artefact/file/edit.php?fid={$fragment->id}&id={$id}" class="icon btn-edit">{str tag="edit"}</a></li>
                    	<li><a href="{$WWWROOT}artefact/file/edit.php?delete={$fragment->id}&id={$id}" class="icon btn-del">{str tag="delete"}</a></li>
                	</ul>
            	</div>

            	<h3>{$fragment->title}</h3>

        		<div class="codesc"><label>Reflection:</label> {$fragment->reflection}</div>
        		<div class="fl">
					<ul>
						<li><label>Concept: </label><a href="{$WWWROOT}concept/map.php?id={$fragment->map}">{$fragment->concept} </a></li>
						<li><label>Fragment: </label> {$fragment->config|clean_html|safe}</li>
						<li><label>Time tag: </label> none</li>
					</ul>
        		</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
	</table>
{/if}

{include file="footer.tpl"}