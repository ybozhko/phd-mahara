{include file="header.tpl"}
<div class="rbuttons"><a href='{$WWWROOT}concept/map.php?id={$map}' class="btn-back">Back to map</a></div>
<div id="accordion">
	{foreach $examples as example}
	<h3><a href="#">{$example->title}</a></h3>
	<div>
	<table border='0'><tr>
		{if $example->type == 'image'}
			<td width='50%'><img id='cropbox_{$example->id}' alt='{$example->config}' src='{$WWWROOT}/artefact/file/download.php?file={$example->aid}' width='600px' /></td>
		{elseif $example->type == 'video'}
			{assign var=time value=","|explode:$example->config} 
			<td width='50%'>
		  	<video id="video_{$example->id}" oncanplay="startVideo({$example->id}, {$time[0]})" ontimeupdate="stopVideo({$example->id}, {$time[0]}, {$time[1]})" autobuffer="true" width="400px" height="300px">
    			<source src="{$WWWROOT}/artefact/file/download.php?file={$example->aid}" type='video/ogg; codecs="theora, vorbis"'>
    			<source src="{$WWWROOT}/artefact/file/download.php?file={$example->aid}" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
    			<source src='{$WWWROOT}/artefact/file/download.php?file={$example->aid}' type='video/3gpp; codecs="mp4v.20.8, samr"'>
  			</video>
			
  			<p><input type="button" value="Play" id="playpause_{$example->id}" onclick="playOrPause({$example->id})"></p>
  			<p><label>Fragment length: </label> {$time[1]-$time[0]}s</p>
  			</td>
		{elseif $example->type == 'file'}
			<td width='50%'><i>{$example->config|clean_html|safe}</i></td>
		{/if}
		<td>
			<h4>Reflection</h4>
			<p>{$example->reflection|clean_html|safe}</p>
			<p><i>{$example->ctime|date_format:"%d-%m-%Y"}</i></p> 
		</td></tr>
		</table>
	</div>
	{/foreach}
</div>
{include file="footer.tpl"}