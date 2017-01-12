var Popup = Module.extend({
	
	params : {}
	,spinner : null
	,hideTooltipInterval : null
	,searchParam : null
	,modifiedSelected : null

	,init: function(params){
		this._super($.extend({
			appendTo : 'body'
			,tpl : 'popup'
			,draggable : true
			,fadeIn : true
		}, params || {}));
	}

	,onAfterAppend : function(){
		this._super();
		this.setup();
	}
	
	,close : function(){
		$('#'+this.params.id).poshytip('hide');
		$('#'+this.params.id).remove();
		this.fireEvent('close');
	}
	
	,setup : function(){
		this.loading(function(){		
			this.renderContent(function(){
				this.spinner.stop();
			}, this);
		}, this);
	}
	
	,reRenderContent : function(callback, scope){
		$('#'+this.params.id+'-content').empty();
		this.renderContent(callback, scope);
	}
	
	,renderContent : function(callback, scope){
		this.setTitle(function(){
			this.setContent(function(){
				if(callback)
					callback.call(scope);
			}, this);			
		}, this);			
	
	}
	
	,loading : function(callback, scope){
		this.spinner = new Spinner({				
			appendTo : '#'+this.params.id+'-wrapper'
			,size : '20'
			,color : 'grey'
			,on : {
				'afterappend' : {
					fn : function(){
						if(callback)
							callback.call(scope);
					}
					,scope : this
				}
			}
		});
		this.spinner.renderAndAppend();
	}
		
	,setTitle : function(callback, scope){	
		var title = new Module({
			tpl : 'popup-title'
			,value : this.params.title
			,appendTo : '#'+this.params.id+'-content'
			,on : {
				'afterappend' : {
					fn : function(){
						if(callback)
							callback.call(scope);
					}
					,scope : this
				}
			}			
		});
		title.renderAndAppend();
	}
	
	,setContent : function(callback, scope){
		var content = new Module({
			tpl : 'popup-content'
		});
		var columnwrapper = new Module({
			tpl : 'popup-columnwrapper'
			,appendTo : '#'+this.params.id+'-content'
			,id : this.params.id+'-columnwrapper'
			,on : {
				'afterappend' : {
					fn : function(){
						if(callback)
							callback.call(scope);
					}
					,scope : this
				}
			}
			,items : [content]			
		});

		columnwrapper.renderAndAppend();
	}
	
	,setMessage : function(msg){
		$('#'+this.params.id).poshytip('hide');
		clearInterval(this.hideTooltipInterval);
						
		$('#'+this.params.id).poshytip({
			className: 'tip-twitter',
//			timeOnScreen : 5000,
			alignTo: 'target',
			alignX: 'center',
			alignY: 'top',			
			offsetY: 0,			
			fade: true,
			slide: true,
			content : msg,
			showOn : 'none'
		});
		var self = this;
		this.hideTooltipInterval = setInterval(function(){
			$('#'+self.params.id).poshytip('hide');				
		}, 5000);
		$('#'+self.params.id).poshytip('show');				

	}
});