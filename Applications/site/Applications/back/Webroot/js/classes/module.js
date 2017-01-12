var Module = Class.extend({				

	params : null
	,type : null
	,events : null
	,rendered : null
	
	
   	/**
   	 * @name	init
   	 */	
	,init: function(params){
		this.type = 'mdl';
		this.events = [];
		this.rendered = null;
		
		// set the params for this module
		this.params = $.extend({on : {}, items : []}, params || {});
		
		// set
		this.params.id = this.params.id ? this.params.id : this.getUniqueId();
		
		// add events	
		this.resetEvents();
		
		this.type = this.params.type ? this.params.type : this.type;
	}
	
	,onStartDrag : function(){}
	
	,onDrag : function(){}
	
	,onStopDrag : function(){}
	
	,getParams : function(){
		return this.params;
	}
	
	,setParam : function(key, value){
		this.params[key] = value;
	}
	
	,getId : function(){
		return this.params.id;
	}
		
	
	/**
	 * @name	getParamsForRender
	 */
	,getParamsForRender : function(){
		return this.params;
	}	
	
	/**
	 * @name	render
	 */
	,render : function(){
		var self = this;
		var tpl = new JTMLTemplate(this.params.tpl);	
		this.rendered = $(tpl.render(this.getParamsForRender()));
		return this.rendered;
	}
	
	/**
	 * @name	append
	 */
	,append : function(){
		if(this.params.after){
			$(this.params.after).after(this.rendered);
		} else {
			if(this.params.before){
				$(this.params.before).before(this.rendered);
			} else {
				if(this.params.prepend)
					$(this.params.appendTo).prepend(this.rendered);
				else
					$(this.params.appendTo).append(this.rendered);
			}
		}
		
		this.onAfterAppend();
	}
	
	,onAfterAppend : function(){
		if(this.params.offset)
			$('#'+this.params.id).offset({top: this.params.offset.top, left: this.params.offset.left});
		
		if(this.params.shadow){
			if(this.params.hasHtmlContainer)
				$('#'+this.params.id+'-container').addClass('shadow');
			else
				$('#'+this.params.id).addClass('shadow');			
		}
		
		if(this.params.hide){
			$('#'+this.params.id).hide();
			this.fireEvent('afterappend');
		} else {
				
			if(this.params.fadeIn){
				var self = this;
				var speed = this.params.fadeIn > 100 ? this.params.fadeIn : $('body').data("speed");
				$('#'+this.params.id).hide();
				$('#'+this.params.id).fadeIn(speed, function(){
					if(self.params.draggable){
						if(self.params.draggablehandle){
							$('#'+self.params.id).draggable({handle : self.params.draggablehandle});
						} else {
							if(self.params.draggablecancel){
								$('#'+self.params.id).draggable({cancel : self.params.draggablecancel});							
							} else {
								$('#'+self.params.id).draggable();							
							}
						}	
					}
					self.fireEvent('afterappend');
				});			
			} else {
				this.fireEvent('afterappend');
			}
		}
	}
	
	,resetEvents : function(){	
		for(var action in this.params.on)
			this.addEvent(action, this.params.on[action]);
	}
	
	,renderSubItems : function(events){
		if(events.on['beforerender']){
			var fn_een = events.on['beforerender'].fn;
			var scope_een  = events.on['beforerender'].scope;
			fn_een.call(scope_een);
		}
		var l = this.params.items.length;
		for(var i=0; i<l; i++){
			this.params.items[i].params.parent = this;
			if(!this.params.items[i].params.appendTo)
				this.params.items[i].params.appendTo = '#'+this.params.id;

			this.params.items[i].renderAndAppend();
		}
		if(events.on['render']){
			var fn = events.on['render'].fn;
			var scope  = events.on['render'].scope;
			fn.call(scope);
		}
	}
	
	,getSubItems : function(){
		return this.params.items;
	}	
		
	/**
	 * @name	renderAppend
	 */
	,renderAndAppend : function(){
		this.render();
		this.append();
		this.renderSubItems({
			on : {
				'render' : {
					fn : this.onRender
					,scope : this
				}
			}
		});		
	}
	
	,onRender : function(){
		this.setListeners();
	}
	
	,setListeners : function(){
	    var self = this;
	    for(var action in this.params.on){
	    	$("#"+this.params.id).bind(action, function(event){ 
	    		if(!$('body').data("disableevents"))
		    		self.fireEvent(action, {event:event}); 
	    		return false;
	    	});					
	    }
	    	    
	    $("#"+this.params.id).bind('dragstop', function(event, ui){ 
	    	self.onStopDrag(); 
	    });	
	   	
	   	$("#"+this.params.id).bind('dragstart', function(event, ui){ 
	    	self.onStartDrag(); 
	    });	
	    
	   	$("#"+this.params.id).bind('drag', function(event, ui){ 
	    	self.onDrag(); 
	    });		      
	}
	
		
	/**
	 * @name	getUniqueId
	 */
	,getUniqueId : function(){
		var id = this.type+'-';
		for(var i=0; i<5; i++){
			id += Math.floor(Math.random()*11);
		}
		return id;
	}
	
	/**
	 * @name	getId
	 */
	,getId : function(){
		return this.params.id;
	}

	/**
	 * @name	addEvent
	 */
	,addEvent : function(e, p){
		this.events[e] = {
			fn : p.fn
			,scope : p.scope
			,params : p.params
		};
	}
	
	/**
	 * @name	fireEvent
	 */	
	,fireEvent : function(e, arguments){		
		if(this.events[e]){
			var params = $.extend(arguments, this.events[e].params || {});
			var fn = this.events[e].fn;
			var scope = this.events[e].scope; 
			fn.call(scope, params);
		}						
	}
});