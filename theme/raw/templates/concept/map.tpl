{include file="header.tpl"}

	<h2>{$mapname}</h2><a href="{$WWWROOT}concept/timeline.php?id={$id}">{str tag="switch" section="concept"}{str tag="timeline" section="concept"}</a>
		<form id="mainform" name="mainform">
			<div>
			<label>{str section="concept" tag="search"}:</label>
			<input id="search" value="" size="10" maxlength="20"></input>
			<a href="javascript:SearchTree();"><img border=0 align="bottom" alt="" src="{theme_url filename='images/btn-search.gif'}"></a>						
			<br/>	
			<a href="javascript:t.collapseAll(); ContextMenu();">{str section="concept" tag="collapse"}</a> | <a href="javascript:t.expandAll(); ContextMenu();">{str section="concept" tag="expand"}</a>
			</div>
		</form>
		<div id="sample1"></div>
	
	<ul id="myMenu" class="contextMenu">
		<li class="newc"><a href="#newc">New Concept</a></li>
		<li class="newd"><a href="#newd">New Definition</a></li>
		<li class="edit separator"><a href="#edit">Edit</a></li>
		<li class="delete"><a href="#delete">Delete</a></li>
		<li class="change"><a href="#change">Change Type</a></li>
		<li class="nexample separator"><a href="#nexample">Add Example(s)</a></li>
		<li class="vexample"><a href="#vexample">View Example(s)</a></li>
	</ul>
	
	<div style="display: none" id="cdialog" title="New ...">
	<form id='newc'>
		<input type='hidden' id="map" name="map" value="{$id}"/>
		<label for='name'>Name</label><br/>
		<input type='text' id='name' name="name" style="width:285px" value=''/>
	</form>
	</div>  
	
	<div style="display: none" id="rendialog" title="Edit">
	<form id='renameform'>
		<input type='hidden' id="map" name="map" value="{$id}"/>
		<label for='newname'>Name</label><br/>
		<input type='text' id='newname' name="newname" style="width:285px"/>
	</form>
	</div> 
	
	<div style="display: none" id="rdialog" title="Are you sure that you want delete this item?">
		<p>This action will delete all related definitions and examples.</p>
	</div> 

	<div style="display: none" id="changedialog" title="Change node type?">
		<p>Changing node type might result in detaching all the examples referred to this node.</p>
	</div> 
	
	<div id="edialog" title="Free fragments">

	</div> 
{include file="footer.tpl"}