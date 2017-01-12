<?php
/**
 * @name	/cms.tpl
 * @version	1.0 2012-11-26 11:27:58
 *
 * @usage:	<?php $this->includeTpl('/cms.tpl'); ?>
 */
?>
<?php 
$this->includeTpl('includes/header.tpl'); 
$this->includeTpl('includes/topbar.tpl'); 

/**
 * @desc	Hier opent de content (grids, forms etc)
 */
?>
<div id='content-container'>

	<?php $this->includeTpl('includes/messages.tpl'); ?>

	<?php $this->includeTpl('includes/title.tpl'); ?>	
	
<!-- PAGES TREE -->
<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'eenderde')); ?>
<h4>Menu</h4>
<p>Klik op de pagina's om deze te bewerken.</p>
<p>

<div id="tree" class="demo">
</div>

<script type='text/javascript'>
$(function(){
	$("#tree").jstree({
		crrm : {
            move : {
                "default_position" : "first",
                "check_move" : function (m) {                
                	var new_parent = m.np.attr("id");
                	if(new_parent == 'tree')
                		return false;
                	                  
/*
.o - the node being moved 
.r - the reference node in the move 
.ot - the origin tree instance 
.rt - the reference tree instance 
.p - the position to move to (may be a string - "last", "first", etc) 
.cp - the calculated position to move to (always a number) 
.np - the new parent 
.oc - the original node (if there was a copy) 
.cy - boolen indicating if the move was a copy 
.cr - same as np, but if a root node is created this is -1 
.op - the former parent 
.or - the node that was previously in the position of the moved node 
*/                  
                  
                    return m.o.attr("locked") ? false : true;  
                }
            }
        }	
		,plugins : ["themes","ui","dnd","types","json_data","crrm","cookies"<?php if(IS_ADMIN): ?>,"contextmenu"<?php endif; ?>] 
		,themes : {
			theme : "classic",
			dots : true,
			icons : true
		},
		<?php if(IS_ADMIN): ?>
		contextmenu : {
        	items : function(node){
        		if(node.attr('rel') == 'menu'){
					return {
						'edit' : {
		                	label : "Edit",
        		            action : function (obj) {
                    			var id = $(obj).attr('id');
								window.location.href = "<?=APPLICATION_URL.'/cms.menu/edit/';?>"+id;
                    		}
                		},
						'delete' : {
		                	label : "Delete",
        		            action : function (obj) {
                    			var id = $(obj).attr('id');
								window.location.href = "<?=APPLICATION_URL.'/cms.menu/delete/';?>"+id;
                    		}
                		},
                		'rename': false,
						'remove': false,
						'ccp': false,
						'create' : false        		
					}
        		} else {
        			return false;
        		}
        	}
		},		
		<?php endif; ?>			
		json_data : {
			data : <?=$this->Menu?>
		}, 		
		types : {				
			max_depth : 4, // maximaal aantal niveaus in deze tree
            types : {
                content : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/content.png"
                    }
                }, 
                virtual : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/virtual.png"
                    }
                },
               	redirect : {
                    icon : {                    
                    	image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/redirect.png"
                    }
                },
                alias : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/alias.png"
                    }                
                },
                template : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/template.png"
                    }                
                },                
                contentlocked : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/content-locked.png"
                    }
                }, 
                virtuallocked : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/virtual-locked.png"
                    }
                },
               	redirectlocked : {
                    icon : {                    
                    	image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/redirect-locked.png"
                    }
                },
                aliaslocked : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/alias-locked.png"
                    }                
                },
                templatelocked : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/template-locked.png"
                    }                
                },                
               	draft : {
                    icon : {                    
                    	image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/draft.png"
                    }
                }, 
               	predefinedfolder : {
                    icon : {                    
                    	image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/predefined-folder.png"
                    }
                },
               	predefined : {
                    icon : {                    
                    	image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/predefined.png"
                    }
                },                                                	
               	menu : {
                    icon : {                    
                    	image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/menu.png"
                    }
                }                
           	}
        },   			
	/*	ui : {
			select_limit : 1
			<?php if(isset($this->MenuDraftID) && $this->MenuDraftID): ?>			
			,initially_select: <?=$this->MenuDraftID?>
			<?php endif; ?>
		}		
		,core : {
			<?php if(isset($this->MenuDraftID) && $this->MenuDraftID): ?>			
			initially_open: <?=$this->MenuDraftID?>
			<?php endif; ?>
		}		*/
		
								
	})
	.bind("move_node.jstree", function (event, data){
       	var _data = {};       		
       	_data.node_id = data.rslt.o.attr("id");
       	_data.parent_id = data.rslt.np.attr("id") != 'tree' ? data.rslt.np.attr("id") : 0;
       	_data.position = data.rslt.p;
       	_data.before_id = _data.position == 'before' ? data.rslt.r.attr("id") : 0; 
       	_data.after_id = _data.position == 'after' ? data.rslt.r.attr("id") : 0;        	
       	_data.subject = data.rslt.r.attr("id");

		var request = new Ajax({
			url : '<?=APPLICATION_URL_AJAX.'/'.CONTROLLER_NAME?>/updateMenu'
			,data : _data
			,on : {
				'success' : {
					fn : function(a){
//						console.log('success');
//						console.log(a);
					}
					,scope : this
				}
				,'error' : {
					fn : function(a){
//						console.log('error');
//						console.log(a);					
					}
					,scope : this
				}
			}
		});   		
	})
	<?php if($this->Access['actions']['edit']): ?>		
	.bind("select_node.jstree", function (event, data){
		if(data.rslt.e){
	 		var rsltObj = data.rslt.obj;
			var node_id = rsltObj.attr('id');
			var node = $('#'+node_id);
			var rel = node.attr('rel');
			var page_id = node.attr('page_id');
		
			if(rel != 'menu' && rel != 'draft')
				window.location.href = "<?=APPLICATION_URL.'/'.(isset($this->EditController) ? strToLower($this->EditController) : strToLower(CONTROLLER_NAME)).'/edit/';?>"+page_id;						
		}
			
	})
	<?php endif; ?>
	
	
	<?php if(isset($this->MenuDraftID) && $this->MenuDraftID): ?>
	.bind("loaded.jstree", function(){
    	window.setTimeout(function(){
    		$("#tree").jstree("open_node", $('#<?=$this->MenuDraftID?>'));    	
    	}, 1000);
	})
	<?php endif; ?>
	;	
});

</script>



</p>

<?php if(IS_ADMIN){
 	$a_Buttons = array();
 	$a_Buttons[] = array('url' => APPLICATION_URL.'/cms.menu/add', 'value' => 'add');
 	$this->includeTpl('includes/content/includes/buttoncontainer.tpl', array('buttons' => $a_Buttons));
}
?>

<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>	

<?php
if($this->isTrue('SearchForm'))
	$this->includeTpl('includes/content/blocks/search.tpl', array('width' => 'tweederde')); 
?>	 
	
<?php
$this->includeTpl('includes/content/blocks/grid.tpl', array('width' => 'tweederde', 'dataset' => $this->DataSet, 'dataheader' => $this->DataHeader, 'buttons' => $this->Buttons)); 
?>

</div>
	
<?php
$this->includeTpl('includes/leftmenu.tpl'); 
$this->includeTpl('includes/credits.tpl'); 
$this->includeTpl('includes/footer.tpl'); 
?>