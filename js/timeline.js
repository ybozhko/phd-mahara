function loadTimeline() {
	$('#timelineLimiter').css({
		width: $('#main-column-container').width() - 10
	});
	
	var tot=$('.event').length;

	$('.eventList li').click(function() {
		var elemid = '#_' + this.id;
		$(elemid).dialog({
			autoOpen: false,
			open: function(event, ui) { 
				var a = $('#cropbox' + this.id).attr('alt').split(',');
				$('#cropbox' + this.id).Jcrop({
					boxWidth: 370,
					boxHeight: 450,
					bgOpacity: 0.3,
					setSelect: [a[0], a[1], a[2], a[3]],
					allowResize: false,
					allowMove: false,
					allowSelect: false
				});	 
			},
			height: 500,
			width: 750,
			modal: true
		});
		$(elemid).dialog('open');
	});
	
	/* Each event section is 320 px wide */
	var timelineWidth = 320*tot;
	var screenWidth = $('#main-column-container').width();
	
	$('#timelineScroll').width(timelineWidth);
	
	/* If the timeline is wider than the screen show the slider: */
	if(timelineWidth > screenWidth) {
		$('#scroll,#slider').show();
		$('#centered,#slider').width(120*tot);

		/* Making the scrollbar draggable: */
		$('#bar').width((120/320)*screenWidth).draggable({			
			containment: 'parent',
			drag: function(e, ui) {
				if(!this.elem)
				{
					/* This section is executed only the first time the function is run for performance */
					
					this.elem = $('#timelineScroll');
					
					/* The difference between the slider's width and its container: */
					this.maxSlide = ui.helper.parent().width()-ui.helper.width();

					/* The difference between the timeline's width and its container */
					this.cWidth = this.elem.width()-this.elem.parent().width();
					this.highlight = $('#highlight');
				}
				
				/* Translating each movement of the slider to the timeline: */
				this.elem.css({marginLeft:'-'+((ui.position.left/this.maxSlide)*this.cWidth)+'px'});
				
				/* Moving the highlight: */
				this.highlight.css('left',ui.position.left)
			}
		});
		
		$('#highlight').width((120/320)*screenWidth-3);
		$('#highlight').width();
	}
}