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
 * @subpackage artefact-competencies
 * @author     Yuliya Bozhko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2010 Yuliya Bozhko, yuliya.bozhko@gmail.com
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
define('TITLE', get_string('myconcepts', 'concept'));

$mapid = param_integer('id', 0);
$map = new ConceptMap($mapid);
$data = Concepts::get_concepts_timeline($mapid, 'M-Y');

$tf = get_records_sql_array('SELECT t.name, t.id FROM {concept_timeframe} t 
							INNER JOIN {concept_map_timeframe} m on m.timeframe=t.id WHERE m.map = ? ', array($mapid));

$stylesheet = array(
			'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/concept.css">',
			'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/jquery-ui.css">',
			);
			
$js = <<<EOF
	function changeTimeFrame(tf) {
		$.post('process.php', 
			'map=' + $mapid + '&tf=' + tf, 
			function (result) {
            	$('#main-timeline').html(result);
            	loadTimeline();
            });
    }; 

    $(document).ready(function(){
		loadTimeline();
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

$smarty = smarty(array('jquery', 'timeline', 'jquery-ui', 'jquery.jcrop'), $stylesheet); 
$smarty->assign('examples', $data);
$smarty->assign('id', $map->get('id'));
$smarty->assign('tf', $tf);
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('mapname', get_string('mapname', 'concept', $map->get('name')));
$smarty->display('concept/timeline.tpl');

?>