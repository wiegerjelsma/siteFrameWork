<?php 
$this->includeTpl('includes/header.tpl'); 
$this->includeTpl('includes/topbar.tpl'); 

/**
 * @desc	Hier opent de content (grids, forms etc)
 */
?>
<div id='content-container'>

	<?php $this->includeTpl('includes/messages.tpl'); ?>

	<h1><?=$this->Titel?></h1>
	<?=isset($this->Subtitel) && $this->Subtitel ? '<h3>'.$this->Subtitel.'</h3>' : ''?>	

	
<?php
$this->includeTpl('includes/content/blocks/form.tpl', array(
	'dataset' => isset($this->DataSet) && $this->DataSet ? $this->DataSet : false, 
	'fields' => isset($this->Fields) && $this->Fields ? $this->Fields : false,
	'buttons' => isset($this->Buttons) && $this->Buttons ? $this->Buttons : false,
	'formheader' => isset($this->Formheader) && $this->Formheader ? $this->Formheader : false,
	'errorfields' => isset($this->ErrorFields) && $this->ErrorFields ? $this->ErrorFields : false,
	'messages' => isset($this->Messages['form']) && $this->Messages['form'] ? $this->Messages['form'] : false
	)); 
?>

<?php 
if(isset($this->SubDataSet)){
	if(isset($this->SubTitle)){
		echo '<p>&nbsp;</p><h3>'.$this->SubTitle.'</h3></p>';
	}
?>
	
<?php if($this->SubSequenceManager): ?>
<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'eenderde')); ?>
<h4>Volgorde</h4>
<p>Versleep de items om de volgorde te bewerken</p>
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
                    return m.o.attr("locked") ? false : true;  
                }
            }
        }	
		,plugins : ["themes","ui","dnd","types","json_data","crrm"] 
		,themes : {
			theme : "classic",
			dots : true,
			icons : true
		},		
		json_data : {
			data : <?=$this->SubSequence?>
		}, 		
		types : {				
			max_depth : 2, // maximaal aantal niveaus in deze tree (2 is hoofdmenu en submenu)
            types : {
                'content' : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/content.png"
                    }
                }, 
                'folder' : {
                    icon : {
                        image : "<?=WEBROOT_SITE_BACK?>/img/treeicons/folder.png"
                    }
                }               
           	}
        }	
        
		,"core" : {
            "initially_open": ["root_node"]
        }
       
        					
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
			url : '<?=APPLICATION_URL_AJAX.'/'.CONTROLLER_NAME?>/updateSubSequence'
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
	});	
});

</script>



</p>

<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>	
	
	<?php endif; ?>	
		
<?php
if($this->SubSequenceManager){
	if($this->isTrue('SubSearchForm'))
		$this->includeTpl('includes/content/blocks/search.tpl', array('width' => 'tweederde')); 
	
	$this->includeTpl('includes/content/blocks/grid.tpl', array('width' => 'tweederde', 'dataset' => $this->SubDataSet, 'dataheader' => $this->SubDataHeader,'buttons' => $this->SubButtons, 'issubset' => true)); 
} else {
	if($this->isTrue('SubSearchForm'))
		$this->includeTpl('includes/content/blocks/search.tpl');
		
	$this->includeTpl('includes/content/blocks/grid.tpl', array('dataset' => $this->SubDataSet, 'dataheader' => $this->SubDataHeader,'buttons' => $this->SubButtons, 'issubset' => true)); 		 
}
?>		
		
<?php } ?>

</div>
	
<?php
$this->includeTpl('includes/leftmenu.tpl'); 
$this->includeTpl('includes/credits.tpl'); 
$this->includeTpl('includes/footer.tpl'); 
?>