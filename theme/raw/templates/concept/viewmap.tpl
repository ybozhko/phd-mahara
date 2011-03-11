{if $microheaders}{include file="viewmicroheader.tpl"}{else}{include file="header.tpl"}{/if}

{if !$microheaders && $mnethost}
<div class="rbuttons">
  <a href="{$mnethost.url}">{str tag=backto arg1=$mnethost.name}</a>
</div>
{/if}

<div id="view" class="cb">
        <div id="bottom-pane">
            <div id="column-container">
            		<div id="conceptmap"></div>
            		<div id="examples"></div>
                <div class="cb"></div>
            </div>
        </div>

  <div class="viewfooter">
    {if $feedback->count || $enablecomments}
    <table id="feedbacktable" class="fullwidth table">
      <thead><tr><th>{str tag="feedback" section="artefact.comment"}</th></tr></thead>
      <tbody>
        {$feedback->tablerows|safe}
      </tbody>
    </table>
    {$feedback->pagination|safe}
    {/if}
	<div id="viewmenu">
        {if $enablecomments}
  			<a id="add_feedback_link" class="feedback" href="">{str tag=placefeedback section=artefact.comment}</a> |
		{/if}
    </div>
    {if $addfeedbackform}<div>{$addfeedbackform|safe}</div>{/if}

  </div>
</div>

{if $microheaders}{include file="microfooter.tpl"}{else}{include file="footer.tpl"}{/if}