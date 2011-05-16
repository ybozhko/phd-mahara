<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
 * Copyright (C) 2006-2009 Catalyst IT Ltd and others; see:
 *                         http://wiki.mahara.org/Contributors
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
 * @subpackage core
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2009 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

define('INTERNAL', 1);
define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PAGE', 'editaccess');
define('MENUITEM', 'myportfolio/concept');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('pieforms/pieform.php');
require_once('pieforms/pieform/elements/calendar.php');
require_once(get_config('libroot') . 'concept.php');
require_once(get_config('libroot') . 'group.php');

$mapid = param_integer('map', null);

$map = new ConceptMap($mapid);

define('TITLE', $map->get('name') . ': Edit Access');

if (!$USER->can_edit_map($map)) {
    throw new AccessDeniedException();
}

$form = array(
    'name' => 'editaccess',
    'renderer' => 'div',
    'plugintype' => 'core',
    'pluginname' => 'concept',
    'mapid' => $mapid,
    'usermap' => (int) $map->get('owner'),
    'elements' => array(
        'id' => array(
            'type' => 'hidden',
            'value' => $mapid,
        ),
        'allowmapcomments' => array(
            'type'         => 'checkbox',
            'title'        => 'Allow map comments',
            'description'  => 'If checked, users will be allowed to leave map comments.',
            'defaultvalue' => $map->get('allowmapcomments'), 
        ),
        'allowexamplecomments' => array(
            'type'         => 'checkbox',
            'title'        => 'Allow examples comments',
            'description'  => 'If checked, users will be allowed to leave example comments.',
            'defaultvalue' => $map->get('allowexamplecomments'), 
        ),
        'approvecomments' => array(
            'type'         => 'checkbox',
            'title'        => get_string('moderatecomments', 'artefact.comment'),
            'description'  => get_string('moderatecommentsdescription', 'artefact.comment'),
            'defaultvalue' => $map->get('approvecomments'), 
        ),
    )
);

$js = "function update_loggedin_access() {}\n";

if (!($allowmapcomments = $map->get('allowmapcomments')) || !($allowexamplecomments = $map->get('allowexamplecomments'))) { 
    $form['elements']['approvecomments']['class'] = 'hidden';
}

$allowcomments = json_encode((int) ($allowmapcomments || $allowexamplecomments));

$js .= <<<EOF
var allowcomments = {$allowcomments};
function update_comment_options() {
    allowcomments = ($('editaccess_allowmapcomments').checked || $('editaccess_allowexamplecomments').checked);
    if (allowcomments) {
        removeElementClass($('editaccess_approvecomments'), 'hidden');
        removeElementClass($('editaccess_approvecomments_container'), 'hidden');
        forEach(getElementsByTagAndClassName('tr', 'comments', 'accesslistitems'), function (elem) {
            addElementClass(elem, 'hidden');
        });
    }
    else {
        addElementClass($('editaccess_approvecomments_container'), 'hidden');
        forEach(getElementsByTagAndClassName('tr', 'comments', 'accesslistitems'), function (elem) {
            removeElementClass(elem, 'hidden');
        });
    }
}
addLoadEvent(function() {
    connect('editaccess_allowmapcomments', 'onchange', update_comment_options);
    connect('editaccess_allowexamplecomments', 'onchange', update_comment_options);
});
EOF;

$form['elements']['accesslist'] = array(
    'type'         => 'mapacl',
    'defaultvalue' => isset($map) ? $map->get_access(get_string('strftimedatetimeshort')) : null
);

$form['elements']['overrides'] = array(
    'type' => 'fieldset',
    'legend' => get_string('overridingstartstopdate', 'view'),
    'elements' => array(
        'startdate'        => array(
            'type'         => 'calendar',
            'title'        => get_string('startdate','view'),
            'description'  => get_string('datetimeformatguide'),
            'defaultvalue' => null,
            'caloptions'   => array(
                'showsTime'      => true,
                'ifFormat'       => get_string('strftimedatetimeshort'),
            ),
        ),
        'stopdate'  => array(
            'type'         => 'calendar',
            'title'        => get_string('stopdate','view'),
            'description'  => get_string('datetimeformatguide'),
            'defaultvalue' => null,
            'caloptions'   => array(
                'showsTime'      => true,
                'ifFormat'       => get_string('strftimedatetimeshort'),
            ),
        ),
    ),
);

$form['elements']['submit'] = array(
    'type'  => 'submitcancel',
    'value' => array(get_string('save'), get_string('cancel')),
    'confirm' => null,
);

if (!function_exists('strptime')) {
    // Windows doesn't have this, use an inferior version
    function strptime($date, $format) {
        $result = array(
            'tm_sec'  => 0, 'tm_min'  => 0, 'tm_hour' => 0, 'tm_mday'  => 1,
            'tm_mon'  => 0, 'tm_year' => 0, 'tm_wday' => 0, 'tm_yday'  => 0,
        );
        $formats = array(
            '%Y' => array('len' => 4, 'key' => 'tm_year'),
            '%m' => array('len' => 2, 'key' => 'tm_mon'),
            '%d' => array('len' => 2, 'key' => 'tm_mday'),
            '%H' => array('len' => 2, 'key' => 'tm_hour'),
            '%M' => array('len' => 2, 'key' => 'tm_min'),
        );
        while ($format) {
            $start = substr($format, 0, 2);
            switch ($start) {
            case '%Y': case '%m': case '%d': case '%H': case '%M':
                $result[$formats[$start]['key']] = substr($date, 0, $formats[$start]['len']);
                $format = substr($format, 2);
                $date = substr($date, $formats[$start]['len']);
            default:
                $format = substr($format, 1);
                $date = substr($date, 1);
            }
        }
        if ($result['tm_mon'] < 1 || $result['tm_mon'] > 12
            || $result['tm_mday'] < 1 || $result['tm_mday'] > 31
            || $result['tm_hour'] < 0 || $result['tm_hour'] > 23
            || $result['tm_min'] < 0 || $result['tm_min'] > 59) {
            return false;
        }
        return $result;
    }
}

function ptimetotime($ptime) {
    return mktime(
        $ptime['tm_hour'],
        $ptime['tm_min'],
        $ptime['tm_sec'],
        1,
        $ptime['tm_yday'] + 1,
        $ptime['tm_year'] + 1900
    );
}


function editaccess_validate(Pieform $form, $values) {
    global $SESSION, $map;

    $loggedinaccess = false;
    if ($values['accesslist']) {
        $dateformat = get_string('strftimedatetimeshort');
        foreach ($values['accesslist'] as &$item) {
            if (empty($item['startdate'])) {
                $item['startdate'] = null;
            }
            else if (!$item['startdate'] = strptime($item['startdate'], $dateformat)) {
                $SESSION->add_error_msg(get_string('unrecogniseddateformat', 'view'));
                $form->set_error('accesslist', '');
                break;
            }
            if (empty($item['stopdate'])) {
                $item['stopdate'] = null;
            }
            else if (!$item['stopdate'] = strptime($item['stopdate'], $dateformat)) {
                $SESSION->add_error_msg(get_string('unrecogniseddateformat', 'view'));
                $form->set_error('accesslist', '');
                break;
            }
            if ($item['type'] == 'loggedin' && !$item['startdate'] && !$item['stopdate']) {
                $loggedinaccess = true;
            }
            $now = strptime(date('Y/m/d H:i'), $dateformat);
            if ($item['stopdate'] && ptimetotime($now) > ptimetotime($item['stopdate'])) {
                $SESSION->add_error_msg(get_string('stopdatecannotbeinpast', 'view'));
                $form->set_error('accesslist', '');
                break;
            }
            if ($item['startdate'] && $item['stopdate'] && ptimetotime($item['startdate']) > ptimetotime($item['stopdate'])) {
                $SESSION->add_error_msg(get_string('startdatemustbebeforestopdate', 'view'));
                $form->set_error('accesslist', '');
                break;
            }
        }
    }
}

function editaccess_cancel_submit() {
    global $map;
    $map->post_edit_redirect();
}

function editaccess_submit(Pieform $form, $values) {
    global $SESSION, $map;

    if ($values['accesslist']) {
        $dateformat = get_string('strftimedatetimeshort');
        foreach ($values['accesslist'] as &$item) {
            if (!empty($item['startdate'])) {
                $item['startdate'] = ptimetotime(strptime($item['startdate'], $dateformat));
            }
            if (!empty($item['stopdate'])) {
                $item['stopdate'] = ptimetotime(strptime($item['stopdate'], $dateformat));
            }
        }
    }

    $map->set('allowmapcomments', (int) $values['allowmapcomments']);
    $map->set('allowexamplecomments', (int) $values['allowexamplecomments']);
    if ($values['allowmapcomments'] || $values['allowexamplecomments']) {
        $map->set('approvecomments', (int) $values['approvecomments']);
    }

    db_begin();
    
	$map->commit();
    $map->set_access($values['accesslist']);

    db_commit();

    $SESSION->add_ok_msg("Map was successfully edited");

	$map->post_edit_redirect();
}

$form = pieform($form);

$smarty = smarty(
    array('tablerenderer'),
    array(),
    array('mahara' => array('From', 'To', 'datetimeformatguide')),
    array('sidebars' => false)
);
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign('form', $form);
$smarty->display('concept/access.tpl');
?>