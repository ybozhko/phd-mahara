{include file="header.tpl"}
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}concept/bookmark/edit.php?new=1">New Bookmark</a>
    </div>
<p>On this page you can manage your bookmarks that can be used in the concept maps</p>
{if $bookmarks}
<table class="fullwidth listing">
	<tbody>
		{foreach from=$bookmarks item=b}
		<tr><td>
		    <div class="fr">
                <ul class="groupuserstatus">
                    <li><a href="{$WWWROOT}concept/bookmark/edit.php?id={$b->id}" class="icon btn-edit">{str tag="edit"}</a></li>
                    <li><a href="{$WWWROOT}concept/bookmark/edit.php?del={$b->id}" class="icon btn-del">{str tag="delete"}</a></li>
                </ul>
            </div>
		<h3><a href='{$b->title}'>{$b->title}</a></h3></td></tr>
		<tr><td>{$b->description|clean_html|safe}</td></tr>
		<tr><td><strong>Last accessed: </strong>{$b->note}</td></tr>
		<tr><td>
			<input type="submit" onclick="javascript:toggle_visibility('examplestable-{$b->id}');" class="icon btn-fragments s" value="Fragments({$b->parent})" />
			<a href='javascript:;' id='new-{$b->id}' class='btn-add s' onclick='javascript:showhide_form("show",{$b->id});'>New Fragment</a>
			<table id='examplestable-{$b->id}' class='hidden' width='100%'><tbody>
				{if !$b->container}
					<tr><td>There are no examples with this bookmark yet.</td></tr>
				{else}
				{foreach from=$b->container item=f}
				<tr class="{cycle values='r0,r1'}">
					<td>{$f->cdate|date_format:"%d-%m-%Y"}</td>
					<td>{$f->title}<a href='javascript:;' onclick='javascript:toggle_visibility("reflection-{$f->id}");'>[+]</a></td>
					<td>{$f->config}</td>
					<td width='50px'><a href="{$WWWROOT}concept/bookmark/edit.php?id={$f->id}" class="icon btn-edit s">{str tag="edit"}</a></td>
					<td width='50px'><a href="{$WWWROOT}concept/bookmark/edit.php?delete={$f->id}&id={$f->aid}" class="icon btn-del s">{str tag="delete"}</a></td>
				</tr>
				<tr><td colspan='5' id='reflection-{$f->id}' class='hidden'>{$f->reflection}</td></tr>
				{/foreach}
				{/if}
			</tbody></table>
			<div id='form-{$b->id}' class="hidden">
				<div>{$b->form|safe}</div>
				<button class="cancel" onclick="javascript:showhide_form('hide',{$b->id});">{str tag='cancel'}</button>
			</div>
			<hr/>
		</td></tr>
		{/foreach}
	</tbody>
</table>
{else}
You haven't added any bookmarks yet.
{/if}
{include file="footer.tpl"}

<script>
{literal}
function toggle_visibility(id) {
	$("#" + id).addClass(function(index, currentClass) {
		var addedClass;

	    if (currentClass === "hidden") {
			$("#" + id).removeClass('hidden');
	    }
	    else {	    	
	    	addedClass = "hidden";
	    }  
	    return addedClass;
	 });
}

function showhide_form(type, id) {
	if (type == 'show') {
		$("#new-" + id).addClass(function(index, currentClass) { return "hidden"; });
		$("#form-" + id).addClass(function(index, currentClass) { $("#form-" + id).removeClass('hidden'); });
	}
	else {
		$("#form-" + id).addClass(function(index, currentClass) { return "hidden"; });
		$("#new-" + id).addClass(function(index, currentClass) { $("#new-" + id).removeClass('hidden'); });
	}
}
{/literal}
</script>