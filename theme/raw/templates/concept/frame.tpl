    <div id="timelineLimiter">
	    <div id="timelineScroll"> 
	    
	    {foreach from=$examples key=year item=example}
	    	<div class="event">
                <div class="eventHeading green">{$year}</div>
                <ul class="eventList">
                	{foreach from=$example item=event}
                		<li class="{$event->type}" id="{$event->id}">
                		<span class="icon"></span>
                		{$event->title}
 						<div style="display:none;" id="_{$event->id}" title="{$event->title}">
 						
							<table border='0' width='100%'>
								<tr><td colspan='2'><label>Concept: </label>{$event->concept}</td></tr>
								<tr>
								{if $event->type == 'image'}
									<td width='50%'><img id='cropbox_{$event->id}' alt='{$event->config}' src='{$WWWROOT}artefact/file/download.php?file={$event->aid}&map={$id}' width='600px'/></td>
								{elseif $event->type == 'video'}
									{assign var=time value=","|explode:$event->config} 
									<td width='50%'>
								  	<video id="video_{$event->id}" oncanplay="startVideo({$event->id}, {$time[0]})" ontimeupdate="stopVideo({$event->id}, {$time[0]}, {$time[1]})" autobuffer="true" width="400px" height="300px">
						    			<source src="{$WWWROOT}artefact/file/download.php?file={$event->aid}&map={$id}" type='video/ogg'>
						    			<source src="{$WWWROOT}artefact/file/download.php?file={$event->aid}&map={$id}" type='video/mp4'>
						    			<source src='{$WWWROOT}artefact/file/download.php?file={$event->aid}&map={$id}' type='video/3gpp'>
						    			<source src="{$WWWROOT}artefact/file/download.php?file={$event->aid}&map={$id}" type='video/webm'>
						    			<source src="{$WWWROOT}artefact/file/download.php?file={$event->aid}&map={$id}" type='video/x-matroska'>
						  			</video>
									
						  			<p><input type="button" value="Play" id="playpause_{$event->id}" onclick="playOrPause({$event->id})"></p>
						  			<p><label>Fragment length: </label> {$time[1]-$time[0]}s</p>
						  			</td>
								{elseif $event->type == 'blogpost'}
  									<td width='50%'>
  										<table id="blogtable" class="fullwidth listing">
  										{foreach from=$event->config item=post}
            								<tr class="{cycle values='r0,r1'}">
                								<td class="c2"><a class="blog-title" href="">{$post->title} ({$post->ctime})</a>
                								<div class="blog-desc hidden" id="blog-desc-{$post->id}">{$post->description|clean_html|safe}</div></td>
            								</tr>
        								{/foreach}
  										</table>
  									</td>
  								{elseif $event->type == 'bookmark'}
						  			<td width='50%'>
						  				<table>
						  					<tr><td><a href='{$event->config->title}'>{$event->config->title}</a></td></tr>
						  					<tr><td><strong>Last Accessed:</strong> {$event->config->note|date_format:"%d-%m-%Y"}</td></tr>
						  					<tr><td>{$event->config->description|clean_html|safe}</td></tr>
						  				</table>
						  			</td>
								{elseif $event->type == 'file'}
									<td width='50%'><i>{$event->config|clean_html|safe}</i></td>
								{else}
									<td width='50%'>{$event->config|safe}</td>
								{/if}
								<td>
									<h4>Reflection</h4>
									<p>{$event->reflection|nl2br}</p>
									<p id="date">{$event->ctime|date_format:"%d-%m-%Y"}</p>
								</td></tr>
							</table> 	

						</div>               		
                		</li>
                	{/foreach}
                </ul>
            </div>	    
	    {/foreach}
    		<div class="clear"></div>    
    	</div>   
    
        <div id="scroll"> <!-- The year time line -->
            <div id="centered"> <!-- Sized by jQuery to fit all the years -->
	            <div id="highlight"></div> <!-- The light blue highlight shown behind the years -->
	            	{foreach from=$examples key=year item=example}
	            	<div class="scrollPoints">{$year}</div>
	            	{/foreach}
                <div class="clear"></div>
            </div>
        </div>
        
        <div id="slider"> <!-- The slider container -->
        	<div id="bar"> <!-- The bar that can be dragged -->
            	<div id="barLeft"></div>  <!-- Left arrow of the bar -->
                <div id="barRight"></div>  <!-- Right arrow, both are styled with CSS -->
            </div>
        </div>
     </div>