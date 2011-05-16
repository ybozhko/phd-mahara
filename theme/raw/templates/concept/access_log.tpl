{include file="header.tpl"}

<p>{str tag=maphistorydsc section=concept}</p>
{if !$data}
	<p>{str tag=nohistorydsc section=concept}</p>
{else}
    <table id="history" class="fullwidth listing">
    	<thead>
    		<tr>
    			<td><h4>Access Type</h4></td>
    			<td><h4>Details</h4></td>
    			<td><h4>Start Date</h4></td>
    			<td><h4>Stop Date</h4></td>
    		</tr>
    	</thead>
        <tbody>
        {foreach from=$data item=rec}
        	<tr class="{cycle values='r0,r1'}">
				{if $rec->accesstype == 'public' }
					<td>Public access</td><td></td>
				{elseif $rec->accesstype == 'loggedin'}
					<td>Logged in users access</td><td></td>
				{elseif $rec->accesstype == 'email'}
					<td>Access by email</td>
					<td>{$rec->token}</td>
				{elseif $rec->accesstype == 'friends'}
					<td>Friends access</td><td></td>
				{elseif $rec->usr}
					<td>User access: <a href="{$WWWROOT}user/view.php?id={$rec->usr}">{$rec->token}</a></td>
					<td></td>
				{elseif $rec->group}
					<td>Group access: <a href="{$WWWROOT}group/view.php?id={$rec->group}">{$rec->token}</a></td>
					<td></td>
				{elseif $rec->role}
					<td>Role access</td><td></td>
				{else}
					<td>Access by secret URL</td>
					<td>{$rec->token}</td>
				{/if}
				<td>{$rec->startdate|date_format}</td>
				<td>{$rec->stopdate|date_format}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}

<a class="btn" href="{$WWWROOT}concept/access.php?map={$map}">{str tag="editaccess" section="concept"}</a>

{include file="footer.tpl"}