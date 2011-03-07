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
define('JSON', 1);

require(dirname(dirname(__FILE__)) . '/init.php');

if (param_variable('email')) {
	if (!$data = new_mail(param_variable('email'), param_integer('map'))) {
		json_reply(true, get_string('createviewtokenfailed', 'map'));
	} 
}else {
	if (!$data = new_token(param_integer('map'))) {
    	json_reply(true, get_string('createviewtokenfailed', 'map'));
	}
}
json_reply(false, array('message' => null, 'data' => $data));

function new_token($mapid) {
	$data = new StdClass;
	$data->map    = $mapid;
	$data->token   = get_random_key(20);
	while (record_exists('concept_access', 'token', $data->token)) {
		$data->token = get_random_key(20);
	}
	if (insert_record('concept_access', $data)) {
		return $data;
	}
	return false;
}

function new_mail($email, $mapid) {
	$data = new StdClass;
	$data->map    = $mapid;
	$data->accesstype = 'email';
	$data->token   = $email;
	if (insert_record('concept_access', $data)) {
		return $data;
	}
	return false;
}

?>
