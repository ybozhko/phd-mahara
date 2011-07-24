{include file="header.tpl"}
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/blog/edit.php?id={$id}&new=1">New fragment</a>
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
                   		<li><a href="{$WWWROOT}artefact/blog/edit.php?bid={$fragment->id}&id={$id}" class="icon btn-edit">{str tag="edit"}</a></li>
                    	<li><a href="{$WWWROOT}artefact/blog/copy.php?bid={$fragment->id}&id={$id}" class="icon btn-copy">Copy</a></li>
                    	<li><a href="{$WWWROOT}artefact/blog/edit.php?delete={$fragment->id}&id={$id}" class="icon btn-del">{str tag="delete"}</a></li>
                	</ul>
            	</div>

            	<h3>{$fragment->title}</h3>

        		<div class="codesc"><label>Reflection:</label> {$fragment->reflection}</div>
        		<div class="fl">
					<ul>
						<li><label>Concept: </label>
						{if (isset($fragment->concept))}
							<a href="{$WWWROOT}concept/map.php?id={$fragment->map}">{$fragment->concept} </a>
						{else}
							Free fragment
						{/if}
						</li>
						<li><label>Fragment date: </label> {$fragment->cdate|date_format:"%d-%m-%Y"}</li>
						<li><label>Blogpost(s): </label> {$fragment->config|clean_html|safe}</li>
					</ul>
        		</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
	</table>
{/if}

{include file="footer.tpl"}