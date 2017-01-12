var Loadingpopup = Popup.extend({
	
	params : {}
	,spinner : null
	
	,init: function(params){
		this._super($.extend({
			appendTo : 'body'
			,tpl : 'modal'
			,type : 'loading'
			,modal : true
			,title : 'Even geduld aub'
			,content : 'HIer nog tekstje'
		}, params || {}));
		
		this.renderAndAppend();
	}
	
	,setup : function(){
		this.loading(function(){		
			this.renderContent(function(){
	//			this.spinner.stop();
	
				$('#modal').css({ opacity: 0.5 });
	
			}, this);
		}, this);
	}	
		
	,renderContent : function(callback, scope){
		this.setTitle(function(){
			this.setContent(function(){
				if(callback)
					callback.call(scope);
			}, this);			
		}, this);			
	}		
	
	,setContent : function(callback, scope){
		this.setContentWrapper(function(){
			if(callback)
				callback.call(scope);
		}, this);
	}			
	
	
	,setContentWrapper : function(callback, scope){
		var columnwrapper = new Module({
			tpl : 'popup-columnwrapper-content'
			,appendTo : '#'+this.params.id+'-content'
			,id : this.params.id+'-columnwrapper-content'
			,on : {
				'afterappend' : {
					fn : function(){
						$('#'+this.params.id+'-columnwrapper-content').html(this.params.content);
						if(callback)
							callback.call(scope);
					}
					,scope : this
				}
			}
		});
		columnwrapper.renderAndAppend();		
	}	
});