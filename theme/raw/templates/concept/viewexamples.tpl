<div class="rbuttons" id='backBtn'><a href='javascript:showMap();' class="btn-back">Back to map</a></div>

<h3>{$title}</h3>
<div id="accordion">

	{foreach from=$examples item=example name=smth}
	<fieldset id='f_{$example->id}' class="collapsible">
	<legend><a href='javascript:toggle_fieldset({$example->id});'>{$example->title}</a></legend>
	<div>
	<table border='0'><tr>
		{if $example->type == 'image'}
			<td width='50%'><img id='cropbox_{$example->id}' alt='{$example->config}' src='{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}' width='600px' /></td>
		{elseif $example->type == 'video'}
			{assign var=time value=","|explode:$example->config} 
			<td width='50%'>
		  	<video id="video_{$example->id}" oncanplay="startVideo({$example->id}, {$time[0]})" ontimeupdate="stopVideo({$example->id}, {$time[0]}, {$time[1]})" autobuffer="true" width="400px" height="300px">
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
                		<td class="c2"><a class="blog-title" href="javascript:;" onclick='javascript:toggle_visibility({$post->id});'>{$post->title} ({$post->ctime})</a>
                		<div class="blog-desc hidden" id="blog-desc-{$post->id}">{$post->description|clean_html|safe}</div></td>
            		</tr>
        		{/foreach}
  				</table>
  			</td>
		{elseif $example->type == 'file'}
			<td width='50%'><i>{$example->config|clean_html|safe}</i></td>
		{/if}
		<td width='50%'>
			<h4>Reflection</h4>
			<p>{$example->reflection|clean_html|safe}</p>
			{if $example->complete == 1}
				<p><a href="{$WWWROOT}artefact/file/download.php?file={$example->aid}&map={$map}">Download entire file</a></p>
			{/if}
			<p><i>{$example->ctime|date_format:"%d-%m-%Y"}</i></p> 
		</td></tr>
		</table>
	</div>
	</fieldset>
	{/foreach}
</div>

<script>
{literal}
$(function(){	
	$("#accordion").find('img').each(function() {
		$(this).load(function() {
			var a = $(this).attr('alt').split(',');
			$(this).Jcrop({
				boxWidth: 600,
				boxHeight: 400,
				bgOpacity: 0.3,
				setSelect: [a[0], a[1], a[2], a[3]],
				allowResize: false,
				allowMove: false,
				allowSelect: false
			});	
		});
	});

	function nothing(e) {
    	e.stopPropagation();
    	e.preventDefault();
    	return false;
	};
});

function toggle_fieldset(example) {
	$('#f_' + example).addClass(function(index, currentClass) {
		var addedClass;

	    if (currentClass === "collapsible collapsed") {
			$('#f_' + example).removeClass('collapsed');
	    }
	    else {
	    	addedClass = "collapsed";
	    	$('#f_' + example).css({'padding' : '0 0 1em 1.5em', 'border' : '1px solid #95A8B7'});
	    }  
	    return addedClass;
	 });
}

function stopVideo(id, startTime, stopTime) {
    if ($('#video_' + id).attr('currentTime') > stopTime) {
        $('#video_' + id).trigger('pause');
		$('#video_' + id).attr('currentTime', startTime);
		$('#playpause_' + id).val('Replay');
    }
};

function startVideo(id, startTime) {
	$('#video_' + id).attr('currentTime', startTime);
};

function playOrPause(id) {
	if ($('#video_' + id).attr('paused')) {
    	$('#video_' + id).trigger('play');
    	$('#playpause_' + id).val('Pause');
  	} else {
    	$('#video_' + id).trigger('pause');
   		$('#playpause_' + id).val('Play');
  	}
};

function toggle_visibility(id) {
	$("#blog-desc-" + id).addClass(function(index, currentClass) {
		var addedClass;

	    if (currentClass === "blog-desc hidden") {
			$("#blog-desc-" + id).removeClass('hidden');
	    }
	    else {	    	
	    	addedClass = "hidden";
	    }  
	    return addedClass;
	 });
}
{/literal}
</script>