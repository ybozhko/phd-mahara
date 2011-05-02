<ul class="colnav">
  <li{if $layout == 1} class="selected"{/if}><a href="{$WWWROOT}concept/viewmap.php?id={$id}">Map</a></li>
  <li{if $layout == 0} class="selected"{/if}><a href="{$WWWROOT}concept/viewtimeline.php?id={$id}">Timeline</a></li>
</ul>

<script>{literal}
addLoadEvent(function() {
    connect('colnav-more', 'onclick', function(e) {
        e.stop();
        forEach (getElementsByTagAndClassName('div', 'colnav-extra', null), partial(toggleElementClass, 'hidden'));
    });
});{/literal}
</script>

