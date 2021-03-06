var Confirm = Popup.extend({
	
	params : {}
	,spinner : null
	
	,init: function(params){
		this._super($.extend({
			appendTo : 'body'
			,tpl : 'popup'
			,type : 'confirm'
			,fadeIn : null
		}, params || {}));
		
		this.renderAndAppend();
	}
		
	,renderContent : function(callback, scope){
		this.setTitle(function(){
			this.setContent(function(){
				if(callback)
					callback.call(scope);
			}, this);			
		}, this);			
	
	}	
	
	,setup : function(){
	//	this.loading(function(){		
			this.renderContent(function(){
	//			this.spinner.stop();
			}, this);
	//	}, this);
	}			
	
	,setContent : function(callback, scope){
	//	this.setContentWrapper(function(){
			this.setButtons(function(){
				if(callback)
					callback.call(scope);
			}, this);
	//	}, this);
	}
	
	,setContentWrapper : function(callback, scope){
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
		});
		columnwrapper.renderAndAppend();		
	}
	
	,confirm : function(){
		if(this.params.url)
			window.location.href = this.params.url;
	}
		
	,setButtons : function(callback, scope){	
		var a_Button = [];
		
		a_Button.push(new Module({
			tpl : 'popup-buttoncontainer-button'
			,value : 'ja'
			,on : {
				'click' : {
					fn : function(){
						this.confirm();
					}
					,scope : this
				}
			}
		}));
		
		a_Button.push(new Module({
			tpl : 'popup-buttoncontainer-a'
			,value : 'nee'
			,on : {
				'click' : {
					fn : function(){
						this.close();
					}
					,scope : this
				}
			}
		}));
			
		var buttonContainer = new Module({
			tpl : 'popup-buttoncontainer'
			,appendTo : '#'+this.params.id+'-content'
			,items : [new Module({
				tpl : 'popup-buttoncontainer-ul'
				,items : a_Button
			})]
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
						
		buttonContainer.renderAndAppend();		
	}		
});