/*
 * @name	Datastore
 * @desc	Hierin kunnen we een hash beheren. Tbv grid bijvoorbeeld
 * @events	beforeload
 * 			afterload
 * 			add (als er een item wordt toegevoegd)
 * 			delete (als er een item wordt verwijderd)
 * 			change (als er een item wordt gewijzigd)
 * 			error
 * 
 * @methods	load (laad de store adv url)
 * 			
 */
var Datastore = Module.extend({
	
	data : []
	,errormsg : null
	,errorcode : null
	
	,init: function(params){
		this._super($.extend({}, params || {}));
	}

	,load : function(){
		this.onBeforeLoad();
		
		var self = this;
		var request = new Ajax({
			url : this.params.url
			,data : this.params.data
			,on : {
				'success' : {
					fn : this.onAfterLoad
					,scope : this
				}
				,'error' : {
					fn : this.onError
					,scope : this
				}
			}
		});
	}
	
	,reload : function(){
	}
	
	,getData : function(){
		return this.data;
	}
	
	,onBeforeLoad : function(){
		this.fireEvent('beforeload', {params:this.params, store: this});		
	}
	
	,onAfterLoad : function(result){
		this.data = result.data;
		this.fireEvent('load', this.data);		
	}
	
	,onAdd : function(){}
	
	,onDelete : function(){}
	
	,onError : function(){}
	
	,onChange : function(){}

});
