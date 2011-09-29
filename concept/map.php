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

$wwwroot = get_config('wwwroot');
$mapid = param_integer('id', 0);

/*
$nodes = array(
			array('C', -1, 1, 'Complex Thinking'), 
			array(1, 'C', 1, 'Classification'),
			array(4, 1, 0, 'Ability to specify useful categories to which items will be sorted <hr width=85px> Examples: 3', 100, 75),
			array(5, 1, 0, 'Ability to specify important defining characteristics of the categories <hr width=85px> Examples: 1', 100, 75),
		);
*/

$map = new ConceptMap($mapid);

$records = Concepts::get_concepts($mapid);
$jnodes = json_encode($records['concepts']);

$js = <<<EOF
	var t = null;		
	document.onkeypress = stopRKey; 

	$(document).ready(function() {
		CreateTree($jnodes);
	});		
		
	function ContextMenu() {
		$('#myMenu').appendTo('body');
		
		$("#sample1 DIV").contextMenu(
			{ menu: 'myMenu' }, 
			function(action, el, pos) {
				var el_id = $(el).attr('id');
				node = t.getNode(el_id);

				switch(action) {
					case 'newc':
  						dialogOpen(ECOTree.T_CON, el_id);
  						break;
					case 'newd':
  						dialogOpen(ECOTree.T_DEF, el_id);
  						break;
					case 'vexample':
  						window.location.href = "{$wwwroot}concept/examples.php?id="+el_id;
  						break;
  					case 'edit':
  						dialogRename(el_id);
  						break;
  					case 'nexample':
  						dialogChoose(el_id);
  						break;
  					case 'delete':
  						dialogDelete(el_id);
  						break;
  					case 'change':
  						dialogChange(node.type, el_id);
  						break;
				}
			}
		);		
	}
		
	function stopRKey(evt) {
		var evt = (evt) ? evt : ((event) ? event : null);
		var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
	}			

		function CreateTree(nodes) {
			t = new ECOTree('t','sample1');	
			t.config.useTarget = false;
					
			for (i in nodes) {
				if (nodes[i][2] == 1) {
					t.add(nodes[i][0], nodes[i][1], nodes[i][2], nodes[i][3]);	
				}
				else {
					t.add(nodes[i][0], nodes[i][1], nodes[i][2], nodes[i][3], 100, 75);
				}			
			}
			t.UpdateTree();
			ContextMenu();
		};	
			
			function dialogDelete(event_id) {
			    $('#rdialog').dialog({
					resizable: false,
					height: 160,
					width: 400,
					modal: true,
					buttons: {
						"Delete": function() {
							$.post('process.php', 
								'id=' + event_id + '&delete=1&map=' + $('#map').val(), 
								function (result) {
                    				$('#rdialog').dialog('close');
                    				$('sample1').empty();
                    				CreateTree(result);
                    			}, 'json'); 
						},
						Cancel: function() {
							$(this).dialog('close');
						}
					}
				});
				$('#rdialog').dialog('open');			
			}

			function dialogChange(t, event_id) {
			    $('#changedialog').dialog({
					resizable: false,
					height: 160,
					width: 400,
					modal: true,
					buttons: {
						"Change": function() {
							$.post('process.php', 
								'id=' + event_id + '&change=' + t + '&map=' + $('#map').val(), 
								function (result) {
                    				$('#changedialog').dialog('close');
                    				$('sample1').empty();
                    				CreateTree(result);
                    			}, 'json'); 
						},
						Cancel: function() {
							$(this).dialog('close');
						}
					}
				});
				$('#changedialog').dialog('open');			
			}			
			
			function dialogRename(rename_id) {
				var name = $('#newname');
				var form = $('#renameform');
				name.val($('#'+rename_id).children().text().split(' Examples : ')[0]);
			    
				$('#rendialog').dialog({
					autoOpen: false,
					height: 200,
					width: 320,
					modal: true,
					buttons: {
						"Save": function() {
							
							$.post('process.php', 
									form.serialize() + '&ren_id=' + rename_id, 
									function (result) {
                    					$('#rendialog').dialog('close');
                    					$('sample1').empty();
                    					CreateTree(result);
                    				}, 'json'
                    		);
						},
						Cancel: function() {
							$(this).dialog('close');
						}
					},
					close: function() { name.val(''); }
				});
				
				$('#rendialog').dialog('open');
			};
			
			function dialogOpen(item_type, event_id){
				var name = $('#name');
				var form = $('#newc');

				item_type == 1 ? $("label[for='name']").text("Concept") : $("label[for='name']").text("Definition"); 
				
			    $('#cdialog').dialog({
					autoOpen: false,
					height: 200,
					width: 320,
					modal: true,
					buttons: {
						"Save": function() {										
							$.post('process.php', 
									form.serialize() + '&type=' + item_type + '&parent=' + event_id, 
									function (result) {
                    					$('#cdialog').dialog('close');
                    					$('sample1').empty();
                    					CreateTree(result);
                    				}, 'json'
                    		);
						},
						Cancel: function() {
							$(this).dialog('close');
						}
					},
					close: function() { name.val(''); }
				});
				
				$('#cdialog').dialog('open');
			}
			
			function dialogChoose(def_id) {
				$('#edialog').load('free.php', function() { 
  					$(this).dialog({ 
						height: 350,
						width: 320,
    					modal:true, 
    					buttons: { 
    						"Add" : function() { 
    							var db = $('.db:checked').map(function(i,n) {
        							return $(n).val();
    							}).get();
    							
    							$.post('process.php', 
									{'db[]': db, 'def': def_id, 'map': $('#map').val()}, 
									function (result) {
                    					$('#edialog').dialog('close');
                    					$('sample1').empty();
                    					CreateTree(result);
                    				}, 'json'
                    			);
    							
    						}, 
      						"Close": function() { $(this).dialog("close"); } 
    					} 
  					}); 
				}) ;			
			}
			
	function SearchTree() {
		t.config.searchMode = 0;
		var txt = document.mainform.search.value;
		t.searchNodes(txt);
		ContextMenu();
	};	
EOF;

$stylesheet = array(
				'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/concept.css">',
				'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/jquery-ui.css">'
			  );

$smarty = smarty(array('jquery', 'CTree', 'jquery.contextMenu', 'jquery-ui'), $stylesheet); 
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('id', $map->get('id'));
$smarty->assign('mapname', get_string('mapname', 'concept', $records['name']));
$smarty->display('concept/map.tpl');