<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
 * Copyright (C) 2006-2008 Catalyst IT Ltd (http://www.catalyst.net.nz)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mahara
 * @subpackage concept-map
 * @author     Yuliya Bozhko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2011 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/concept');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'index');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('pieforms/pieform.php');
require_once('concept.php');

$cid = param_integer('id', 0);

$concept = new Concepts($cid);

define('TITLE', "Examples of '" . $concept->get('name') . "'");

$examples = Concepts::get_examples($cid);

$num = count($examples) - 1;

$js = <<<EOF
	$(function(){
		$("#accordion").find('img').each(function() {
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

    	function nothing(e) {
        	e.stopPropagation();
        	e.preventDefault();
        	return false;
    	};
 	});

 	$(document).ready(function() {
 		$("#accordion").css({'overflow':'hidden', 'height':'0px'});
 	});
 	
	$(window).load(function(){
		$("#accordion").accordion({
			fillSpace: true, 
			clearStyle: true
		});
		$("#accordion").css('height', '100%');

		$("#accordion").accordion({ active: $num });
	});			
	
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
EOF;

$stylesheet = array('<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/jquery-ui.css">');

$smarty = smarty(array('jquery', 'jquery-ui', 'jquery.jcrop'), $stylesheet); 
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign('examples', $examples);
$smarty->assign('map', $concept->get('map'));
$smarty->display('concept/examples.tpl');

?>