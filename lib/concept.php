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
 * @subpackage concept
 * @author     Yuliya Bozhko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2010 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */

defined('INTERNAL') || die();

class ConceptMap {
	
	private $id;
    private $name;
    private $description;
    private $owner;
    private $mtime;
    private $ctime;
    private $allowmapcomments;
    private $allowexamplecomments;
    private $approvecomments;
	
    public function __construct($id=0, $data=null) {
    	global $USER;
        $userid = $USER->get('id');
        
        if (!empty($id)) {
        	$tempdata = get_record('concept_maps', 'id', $id);
        	if (empty($tempdata)) {
        		throw new MapNotFoundException("Map with id $id not found");
        	}
        	if (!empty($data)) {
        		$data = array_merge((array)$tempdata, $data);
        	}
            else {
                $data = $tempdata; // use what the database has
            }
            $this->id = $id;
        }
        else {
            $this->ctime = time();
            $this->mtime = time();
            $this->owner = $userid;
            $this->allowmapcomments = 1;
            $this->allowexamplecomments = 1;
            $this->approvecomments = 0;
        }
        
        if (empty($data)) {
            $data = array();
        }
        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }

    public function get($field) {
        if (!property_exists($this, $field)) {
            throw new InvalidArgumentException("Field $field wasn't found in class " . get_class($this));
        }
        return $this->{$field};
    }

    public function set($field, $value) {
        if (property_exists($this, $field)) {
            $this->{$field} = $value;
            $this->mtime = time();
            return true;
        }
        throw new InvalidArgumentException("Field $field wasn't found in class " . get_class($this));
    }    

    public static function save($data) {
        $map = new ConceptMap(0, $data);
        $map->commit();

        return $map;    	
    }
    
    public function delete() {
        db_begin();

        //@TODO: delete all examples!!!
        delete_records('concepts', 'map', $this->id);
        delete_records('concept_maps', 'id', $this->id);

        db_commit();    
    }
    
    public function commit() {

        $fordb = new StdClass;
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
            if (in_array($k, array('mtime', 'ctime')) && !empty($v)) {
                $fordb->{$k} = db_format_timestamp($v);
            }
        }

        db_begin();

        // if id is not empty we are editing an existing map
        if (!empty($this->id)) {
            update_record('concept_maps', $fordb, 'id');
            
            $data = (object)array(
            	'map' => $this->get('id'),
            	'parent' => -1,
        		'type' => 1,
        		'name' => $this->get('name'),
         		'description' => $this->get('description'),
        		'link' => null
        	);
            update_record('concepts', $data, array('map' => $this->get('id'), 'parent' => -1));
        }
        else {
            $id = insert_record('concept_maps', $fordb, 'id', true);
            
            $data = (object)array(
            	'map' => $id,
            	'parent' => -1,
        		'type' => 1,
        		'name' => $this->get('name'),
         		'description' => $this->get('description'),
        		'link' => null
        	);            
            insert_record('concepts', $data);
            
            if ($id) {
                $this->set('id', $id);
            }
        }

        db_commit();    	
    }
    
    public function display_title() {
        $wwwroot = get_config('wwwroot');
        $owner = get_record('usr', 'id', $this->get('owner'));
        $ownername = $owner->firstname . ' ' . $owner->lastname;
        $ownerlink = $wwwroot . 'user/view.php?id=' . $this->owner;
        $title = '<strong>' . hsc($this->name) . '</strong>';

        return get_string('viewtitleby', 'view', $title, $ownerlink, $ownername);
    }
        
	public function get_access($timeformat=null) {

        $data = get_records_sql_array("
            SELECT ca.*, g.grouptype
            FROM {concept_access} ca
                LEFT OUTER JOIN {group} g ON (ca.group = g.id AND g.deleted = 0)
            WHERE ca.map = ?",
            array($this->id)
        );
        if ($data) {
            foreach ($data as &$item) {
                $item = (array)$item;
                if ($item['usr']) {
                    $item['type'] = 'user';
                    $item['id'] = $item['usr'];
                }
                else if ($item['group']) {
                    $item['type'] = 'group';
                    $item['id'] = $item['group'];
                }
                else if ($item['token'] && $item['accesstype']) {
                    $item['type'] = 'email';
                    $item['id'] = $item['token'];
                }
                else if ($item['token']) {
                    $item['type'] = 'token';
                    $item['id'] = $item['token'];
                }
                else {
                    $item['type'] = $item['accesstype'];
                    $item['id'] = null;
                }

                if ($item['role']) {
                    $item['roledisplay'] = get_string($item['role'], 'grouptype.'.$item['grouptype']);
                }
                if ($timeformat) {
                    if ($item['startdate']) {
                        $item['startdate'] = strftime($timeformat, strtotime($item['startdate']));
                    }
                    if ($item['stopdate']) {
                        $item['stopdate'] = strftime($timeformat, strtotime($item['stopdate']));
                    }
                }
            }
        }
        else {
            $data = array();
        }
        return $data;
    }

    public function set_access($accessdata) {
        global $USER;
        require_once('activity.php');

        $beforeusers = activity_get_mapaccess_users($this->get('id'), $USER->get('id'), 'mapaccess');

        db_begin();
        delete_records('concept_access', 'map', $this->get('id'));

        // View access
        if ($accessdata) {
            /*
             * There should be a cleaner way to do this
             * $accessdata_added ensures that the same access is not granted twice because the profile page
             * gets very grumpy if there are duplicate access rules
             *
             * Additional rules:
             * - Don't insert records with stopdate in the past
             * - Remove startdates that are in the past
             * - If view allows comments, access record comment permissions, don't apply, so reset them.
             * @todo: merge overlapping date ranges.
             */
            $accessdata_added = array();
            $time = time();
            foreach ($accessdata as $item) {

                if (!empty($item['stopdate']) && $item['stopdate'] < $time) {
                    continue;
                }
                if (!empty($item['startdate']) && $item['startdate'] < $time) {
                    unset($item['startdate']);
                }
                if ($this->get('allowmapcomments') || $this->get('allowexamplecomments')) {
                	unset($item['allowexamplecomments']);
                    unset($item['allowmapcomments']);
                    unset($item['approvecomments']);
                }

                $accessrecord = new StdClass;

                switch ($item['type']) {
                case 'user':
                    $accessrecord->usr = $item['id'];
                    break;
                case 'group':
                    $accessrecord->group = $item['id'];
                    if (isset($item['role']) && strlen($item['role'])) {
                        // Don't insert a record for a role the group doesn't have
                        $roleinfo = group_get_role_info($item['id']);
                        if (!isset($roleinfo[$item['role']])) {
                            break;
                        }
                        $accessrecord->role = $item['role'];
                    }
                    break;
                case 'token':
                    $accessrecord->token = $item['id'];
                    break;
                case 'email':
                    $accessrecord->token = $item['id'];
                    $accessrecord->accesstype = $item['type'];
                    break;
                case 'friends':
                    if (!$this->owner) {
                        continue; // Don't add friend access to group, institution or system views
                    }
                case 'public':
                case 'loggedin':
                    $accessrecord->accesstype = $item['type'];
                }

                $accessrecord->map = $this->get('id');
                if (isset($item['allowmapcomments'])) {
                    $accessrecord->allowmapcomments = (int) !empty($item['allowmapcomments']);
                    if ($accessrecord->allowmapcomments) {
                        $accessrecord->approvecomments = (int) !empty($item['approvecomments']);
                    }
                }
                if (isset($item['allowexamplecomments'])) {
                    $accessrecord->allowexamplecomments = (int) !empty($item['allowexamplecomments']);
                    if ($accessrecord->allowexamplecomments) {
                        $accessrecord->approvecomments = (int) !empty($item['approvecomments']);
                    }
                }
                if (isset($item['startdate'])) {
                    $accessrecord->startdate = db_format_timestamp($item['startdate']);
                }
                if (isset($item['stopdate'])) {
                    $accessrecord->stopdate  = db_format_timestamp($item['stopdate']);
                }

                if (array_search($accessrecord, $accessdata_added) === false) {

                    insert_record('concept_access', $accessrecord);
                    $accessdata_added[] = $accessrecord;
                }
            }
        }

        $data = new StdClass;
        $data->map = $this->get('id');
        $data->owner = $USER->get('id');
        $data->oldusers = $beforeusers;
        
        activity_occurred('mapaccess', $data);

        db_commit();
    }

    public static function user_access_records($mapid, $userid) {
        static $mapaccess = array();
        $userid = (int) $userid;

        if (!isset($mapaccess[$mapid][$userid])) {

            $mapaccess[$mapid][$userid] = get_records_sql_array("
                SELECT va.*
                FROM {concept_access} va
                    LEFT OUTER JOIN {group_member} gm
                    ON (va.group = gm.group AND gm.member = ?
                        AND (va.role = gm.role OR va.role IS NULL))
                WHERE va.map = ?
                    AND (va.startdate IS NULL OR va.startdate < current_timestamp)
                    AND (va.stopdate IS NULL OR va.stopdate > current_timestamp)
                    AND (va.accesstype IN ('public', 'loggedin', 'friends', 'objectionable')
                         OR va.usr = ? OR va.token IS NOT NULL OR gm.member IS NOT NULL)
                ORDER BY va.token IS NULL DESC, va.accesstype != 'friends' DESC",
                array($userid, $mapid, $userid)
            );
        }

        return $mapaccess[$mapid][$userid];
    }
    
    public function user_comments_allowed(User $user) {
        global $SESSION;

        if (!$user->is_logged_in() && !get_config('anonymouscomments')) {
            return false;
        }

        if ($this->get('allowmapcomments')) {
            return $this->get('approvecomments') ? 'private' : true;
        }

        $userid = $user->get('id');
        $access = self::user_access_records($this->id, $userid);

        $allowmapcomments = false;
        $approvecomments = true;

        $mnettoken = get_cookie('mmapaccess:'.$this->id);
        $usertoken = get_cookie('mapaccess:'.$this->id);

        foreach ($access as $a) {
            if ($a->accesstype == 'public') {
                    continue;
            }
            else if ($a->token && $a->token != $mnettoken && $a->token != $usertoken) {
                continue;
            }
            else if (!$user->is_logged_in()) {
                continue;
            }
            else if ($a->accesstype == 'friends') {
                $owner = $this->get('owner');
                if (!get_field_sql('
                    SELECT COUNT(*) FROM {usr_friend} f WHERE (usr1=? AND usr2=?) OR (usr1=? AND usr2=?)',
                    array($owner, $userid, $userid, $owner)
                )) {
                    continue;
                }
            }

            if ($a->allowmapcomments) {
                $allowmapcomments |= $a->allowmapcomments;
                $approvecomments &= $a->approvecomments;
            }
            if (!$approvecomments) {
                return true;
            }
        }

        if ($allowmapcomments) {
            return $approvecomments ? 'private' : true;
        }

        return false;
    }
    
    public static function get_mymaps_data($offset=0, $limit=10) {
        global $USER;

        ($data = get_records_sql_array("
            SELECT c.id, c.description, c.name, '' as frames
                FROM {concept_maps} c
                WHERE c.owner = ?
            ORDER BY c.name, c.ctime ASC
            LIMIT ? OFFSET ?", array($USER->get('id'), $limit, $offset)))
            || ($data = array());

        foreach($data as $elem) {
        	$elem->frames = implode(',', array_keys(get_records_sql_menu('SELECT t.name FROM {concept_timeframe} t 
							INNER JOIN {concept_map_timeframe} m on m.timeframe=t.id WHERE m.map = ? ', array($elem->id))));
        }   
         
        $result = (object) array(
            'count'  => count_records('concept_maps', 'owner', $USER->get('id')),
            'data'   => $data,
            'offset' => $offset,
            'limit'  => $limit,
        );

        return $result;    	
    }
    
    /**
    * Gets the fields for the new/edit map form
    * - populates the fields with map data if it is an edit
    *
    * @param array map
    * @return array $elements
    */
    public static function get_mapform_elements($data=null) {
        $elements = array(
            'name' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('name', 'concept'),
                'size' => 30,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'description' => array(
                'type'  => 'textarea',
                'rows' => 10,
                'cols' => 50,
                'resizable' => false,
                'defaultvalue' => null,
                'title' => get_string('description', 'concept'),
            ),
        );

        // populate the fields with the existing values if any
        if (!empty($data)) {
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $data->$k;
            }
            $elements['id'] = array(
                'type' => 'hidden',
                'value' => $data->id,
            );
            $elements['owner'] = array(
                'type' => 'hidden',
                'value' => $data->owner,
            );
        }

        return $elements;    	
    }
    
     /**
     * after editing map description, redirect back to the appropriate place
     */
    public function post_edit_redirect($new=false) {
    	if ($new) {
            $redirecturl = '/concept/map.php?id=' . $this->get('id') . '&new=1';
        }
        else {
            $redirecturl = '/concept/index.php';
        }
        redirect($redirecturl);
    }
}

class Concepts {
	
	private $id;
    private $name;
    private $description;
    private $map;
    private $type;
    private $parent;
    private $link;
    
    public function __construct($id=0, $data=null) {
    	if (!empty($id)) {
        	$tempdata = get_record('concepts', 'id', $id);
        	if (empty($tempdata)) {
        		throw new MapNotFoundException("Map with id $id not found");
        	}
        	if (!empty($data)) {
        		$data = array_merge((array)$tempdata, $data);
        	}
            else {
                $data = $tempdata;
            }
            $this->id = $id;
        }
        
        if (empty($data)) {
            $data = array();
        }
        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }    	
    }
    
    public function get($field) {
        if (!property_exists($this, $field)) {
            throw new InvalidArgumentException("Field $field wasn't found in class " . get_class($this));
        }
        return $this->{$field};
    }

    public function set($field, $value) {
        if (property_exists($this, $field)) {
            $this->{$field} = $value;
            return true;
        }
        throw new InvalidArgumentException("Field $field wasn't found in class " . get_class($this));
    } 
        
    public static function save($data) {
    	$concept = new Concepts(0, $data);
        $concept->commit();

        return $concept; 
    }

    public static function remove_all($data) {
		$parent = get_records_array('concepts', 'parent', $data);
		
		if (!$parent) {
			db_begin();
			
			delete_records('concept_example', 'cid', $data);
			delete_records('concepts', 'id', $data);
			
			db_commit(); 
		} 
		else {
			foreach ($parent as $p) {
				self::remove_all($p->id);
				
				db_begin();
				
				delete_records('concept_example', 'cid', $data);
				delete_records('concepts', 'id', $data);
				
				db_commit(); 
			}
		}
    }    
    
    public function commit() {
        $fordb = new StdClass;
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }

        db_begin();

        // if id is not empty we are editing an existing concept
        if (!empty($this->id)) {
            update_record('concepts', $fordb, 'id');
        }
        else {
            $id = insert_record('concepts', $fordb, 'id', true);
            if ($id) {
                $this->set('id', $id);
            }
        }

        db_commit();      	
    }
    
    public static function get_concepts($mapid) {
    	($records = get_records_sql_array("SELECT id, parent, type, name 
    										FROM {concepts}
    										WHERE map = ? 
    										ORDER BY id ASC", array($mapid)))
    		|| ($records = array());
    	
    	$concepts = array();
    	if (!empty($records)) {
    		foreach ($records as $item) {
    			if ($item->type == 1) {
    				$concepts[] = array($item->id, $item->parent, $item->type, $item->name);
    			}
    			else {
    				$examples = count_records('concept_example', 'cid', $item->id);
    				$concepts[] = array($item->id, $item->parent, $item->type, $item->name . "<hr width=85px /> Examples : " . $examples, 100, 75);
    			}
    		}
    	}
    	
    	$map = get_record('concept_maps', 'id', $mapid);
    	$result = array(
    		'name' => $map->name,
    		'concepts' => $concepts
    	);
    	
    	return $result;
    }
    
    public static function get_examples($cid) {
    	$idlist = self::get_allnodesids($cid);
    	
    	$examples = get_records_sql_array("SELECT e.*, a.ctime
    									FROM {concept_example} e 
    									INNER JOIN {artefact} a ON a.id = e.aid
    									WHERE e.cid IN (" . $idlist . ") ORDER BY a.ctime DESC", array());
		return $examples;
    }

    public static function get_allnodesids($cid) {
    	$concepts = get_records_array('concepts', 'parent', $cid);
    	
    	if(!empty($concepts)) {
    		foreach($concepts as $concept) 
	    		$cid = $cid . ",". self::get_allnodesids($concept->id);	    		
    	} 
    	return $cid;
    }    
    
    public static function get_concepts_timeline($mapid, $timeframe) {
    	($records = get_records_sql_array("SELECT a.id, a.aid, a.title, a.reflection, a.config, a.type, d.ctime 
    									FROM {concept_example} a 
    									INNER JOIN {concepts} b ON
    									b.id = a.cid
    									INNER JOIN {artefact} d ON
    									d.id = a.aid
    									WHERE b.map = ? 
    									ORDER BY d.ctime ASC", array($mapid)))
    	|| ($records = array());
    	
    	if (!in_array($timeframe, array('Y', 'M-Y'))) {

    		$frames = get_records_array('concept_timeframe','parent', $timeframe, 'start ASC');
    		
    		foreach($frames as $frame) {
    			$s = strtotime($frame->start);
    			$e = strtotime($frame->end);
    			foreach($records as $record) {
    				if (strtotime($record->ctime) >= $s && strtotime($record->ctime) <= $e) {
    					$dates[$frame->name][] = $record;
    					unset($record);
    				}
    			}
    		}    		
    	}
    	else {
    		$dates = array();
    		foreach($records as $record) {
    			$dates[date($timeframe, strtotime($record->ctime))][] = $record;
    		}
    	}
    	return $dates;
    }
}