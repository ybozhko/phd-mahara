{include file="header.tpl"}
<div class="rbuttons"><a href='{$WWWROOT}concept/map.php?id={$map}' class="btn-back">Back to map</a></div>
<div id="accordion">
	{foreach $examples as example}
	<h3><a href="#">{$example->title}</a></h3>
	<div>
	<table border='0' width='100%' id='main_table'><tr>
		{if $example->type == 'image'}
			<td width='50%'><img id='cropbox_{$example->id}' alt='{$example->config}' src='{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}' width='600px' /></td>
		{elseif $example->type == 'video'}
			{assign var=time value=","|explode:$example->config} 
			<td width='50%'>
		  	<video id="video_{$example->id}" oncanplay="startVideo({$example->id}, {$time[0]})" ontimeupdate="stopVideo({$example->id}, {$time[0]}, {$time[1]})" autobuffer="true" width="javascript:$('#main_table').width() / 2" max-height="400px">
    			<source src="{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}" type='video/ogg'>
    			<source src="{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}" type='video/webm'>
    			<source src="{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}" type='video/mp4'>
    			<source src='{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}' type='video/3gpp'>
    			<source src="{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}" type='video/x-matroska'>
  			</video>
			
  			<p><input type="button" value="Play" id="playpause_{$example->id}" onclick="playOrPause({$example->id})"></p>
  			<p><label>Fragment length: </label> {$time[1]-$time[0]}s</p>
  			</td>
  		{elseif $example->type == 'blogpost'}
  			<td width='50%'>
  				<table id="blogtable" class="fullwidth listing">
  				{foreach from=$example->config item=post}
            		<tr class="{cycle values='r0,r1'}">
                		<td class="c2"><a class="blog-title" href="">{$post->title} ({$post->ctime})</a>
                		<div class="blog-desc hidden" id="blog-desc-{$post->id}">{$post->description|clean_html|safe}</div></td>
            		</tr>
        		{/foreach}
  				</table>
  			</td>
  		{elseif $example->type == 'bookmark'}
  			<td width='50%'>
  				<table>
  					<tr><td><a href='{$example->config->title}'>{$example->config->title}</a></td></tr>
  					<tr><td><strong>Last Accessed:</strong> {$example->config->note|date_format:"%d-%m-%Y"}</td></tr>
  					<tr><td>{$example->config->description|clean_html|safe}</td></tr>
  				</table>
  			</td>
		{elseif $example->type == 'file'}
			<td width='50%'><i>{$example->config|clean_html|safe}</i></td>
		{else}
			<td width='50%'>{$example->config|safe}</td> 
		{/if}
		<td width='50%'>
			<h4>Reflection</h4>
			{if $example->type == 'blogpost'}
				<a href="{$WWWROOT}artefact/blog/edit.php?bid={$example->id}&id={$example->aid}" class="icon btn-edit">{str tag="edit"}</a> &nbsp;|<a href="{$WWWROOT}artefact/blog/edit.php?delete={$example->id}&id={$example->aid}" class="icon btn-del">{str tag="delete"}</a>
			{elseif $example->type == 'bookmark'}
				<a href="{$WWWROOT}concept/bookmark/edit.php?edit={$example->id}&id={$example->aid}" class="icon btn-edit">{str tag="edit"}</a> &nbsp;|<a href="{$WWWROOT}concept/bookmark/edit.php?delete={$example->id}&id={$example->aid}" class="icon btn-del">{str tag="delete"}</a>	
			{else}
				<a href="{$WWWROOT}artefact/file/edit.php?fid={$example->id}&id={$example->aid}" class="icon btn-edit">{str tag="edit"}</a> &nbsp;|<a href="{$WWWROOT}artefact/file/edit.php?delete={$example->id}&id={$example->aid}" class="icon btn-del">{str tag="delete"}</a>
			{/if}
			<p>{$example->reflection|clean_html|safe}</p>
			{if $example->complete == 1}
				<p><a href="{$WWWROOT}/artefact/file/download.php?file={$example->aid}">Download entire file</a></p>
			{/if}
			<p><i>{$example->cdate|date_format:"%d-%m-%Y"}</i></p> 
		</td></tr>
		</table>
	</div>
	{/foreach}
</div>
{include file="footer.tpl"}