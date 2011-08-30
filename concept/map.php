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
		$(document).ready(function() {
			CreateTree($jnodes);
		});
		
			var event_id = 0;
			var t = null;

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
				updateMenu();
			};	
	

			function updateMenu() {
				var str = '#' + t.nDatabaseNodes[0].id;

			    for (var n = 1; n < t.nDatabaseNodes.length; n++) {
			        str = str + ', #' + t.nDatabaseNodes[n].id;
			    }  
			      
			    $(str).contextMenu('menu', {
			    	onContextMenu: function(e) {	
			    		event_id = $(e.target).attr('id');
			    		t.selectNode($(e.target).attr('id'), false);
			    		t.UpdateTree();
			    		updateMenu();
			    		return true;
			        },
			        onShowMenu: function(e, menu) {
			        	node = t.getSelectedNodes();
						
			            if (node[0].type == ECOTree.T_DEF) {
			              $('#nconcept, #ndef, #rconcept', menu).remove();
			            }
			            else {
			            	$('#nexample, #rdef', menu).remove();
			            	if (node[0].pid == -1)
			            		$('#rconcept, #rename', menu).remove();
			            }
			            return menu;
			        },
			    	bindings: {
			    		'nconcept': function(e) {
							dialogOpen(ECOTree.T_CON);
			    		},
			    		'rconcept': function(e) {
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
			    		},
			    		'rdef': function(e) {
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
			    		},
			    		'ndef': function(e) {
							dialogOpen(ECOTree.T_DEF);
			    		},
			    		'rename': function(e) {
			    			dialogRename(event_id);
			    		},
			    		'nexample': function(e) {
							dialogChoose(event_id);
			    		},
			    		'vexample': function(e) {
			    			window.location.href = "{$wwwroot}concept/examples.php?id="+event_id;
			    		}
			    	}
			    });
			    
			};
			
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
			
			function dialogOpen(item_type){
				var name = $('#name');
				var dsc = $('#description');
				var form = $('#newc');
			    		
			    $('#cdialog').dialog({
					autoOpen: false,
					height: 350,
					width: 320,
					modal: true,
					buttons: {
						"Save": function() {
							//var temp = name.val();
										
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
					close: function() {
						name.val('');
						dsc.val('');
					}
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
			};	
			
			function selectedNodes() {
				var selnodes = t.getSelectedNodes();
				var s = [];
				for (var n = 0; n < selnodes.length; n++)
				{
					s.push('' + n + ': Id=' + selnodes[n].id + ', Title='+ selnodes[n].dsc + ', Metadata='+ selnodes[n].meta);
				}
				alert('The selected nodes are:' + ((selnodes.length >0) ? s.join(''): 'None.'));
			};
EOF;

$stylesheet = array(
				'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/concept.css">',
				'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/jquery-ui.css">'
			  );

$smarty = smarty(array('jquery', 'CTree', 'contextmenu', 'jquery-ui'), $stylesheet); 
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('id', $map->get('id'));
$smarty->assign('mapname', get_string('mapname', 'concept', $records['name']));
$smarty->display('concept/map.tpl');