{include file="header.tpl"}

	<h2>{$mapname}</h2><a href="{$WWWROOT}concept/timeline.php?id={$id}">{str tag="switch" section="concept"}{str tag="timeline" section="concept"}</a>
		<form id="mainform" name="mainform">
			<div>
			<label>{str section="concept" tag="search"}:</label>
			<input id="search" value="" size="10" maxlength="20"></input>
			<a href="javascript:SearchTree();"><img border=0 align="bottom" alt="" src="{theme_url filename='images/btn-search.gif'}"></a>						
			<br/>	
			<a href="javascript:t.collapseAll();">{str section="concept" tag="collapse"}</a> | <a href="javascript:t.expandAll();">{str section="concept" tag="expand"}</a>
			</div>
		</form>
		<div id="sample1"></div>
		
	<div class="contextMenu" id="menu">
    	<ul>
			<li id="nconcept">{str section="concept" tag="newconcept"}</li>
			<li id="rconcept">{str section="concept" tag="rconcept"}</li>
			<li id="ndef">{str section="concept" tag="newdef"}</li>
			<li id="rdef">{str section="concept" tag="rdef"}</li>
			<li id="rename">{str section="concept" tag="rename"}</li>
			<li id="nexample">{str section="concept" tag="newexample"}</li>
			<li id="vexample">{str section="concept" tag="viewexample"}</li>
    	</ul>
	</div>
	
	<div style="display: none" id="cdialog" title="Create new concept">
	<form id='newc'>
		<input type='hidden' id="map" name="map" value="{$id}"/>
		<label for='name'>Name</label><br/>
		<input type='text' id='name' name="name" width='300px' value=''/><br/>
		<label for='description'>Description</label><br/>
		<textarea cols='35' rows='10' id="description" name="description" ></textarea>
	</form>
	</div>  
	
	<div style="display: none" id="rendialog" title="Rename">
	<form id='renameform'>
		<input type='hidden' id="map" name="map" value="{$id}"/>
		<label for='newname'>Name</label><br/>
		<input type='text' id='newname' name="newname" width='300px' value=''/>
	</form>
	</div> 
	
	<div style="display: none" id="rdialog" title="Are you sure that you want remove this item?">
		<p>This action will delete all related definitions and examples.</p>
	</div> 
	
	<div id="edialog" title="Free fragments">

	</div> 
{include file="footer.tpl"}