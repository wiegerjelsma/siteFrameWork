var Ajax = Module.extend({
	
	init: function(params){
		this._super($.extend({
			type: "POST"
			,data: {}
			,global : false // global ajax events
			,dataType : 'json'
		}, params || {}));
		
		this.load();
	}

	,load : function(){
		var scope = this;
		$.ajax({
			url : this.params.url
			,data : this.params.data
			,type : this.params.type
			,dataType : this.params.dataType
			,global : this.params.global
			,success : function(data, status, request){scope.onSuccess(data, status, request);}
			,beforeSend : function(request){scope.beforeSend(request);}
			,error : function(request, status, error){scope.onError(request, status, error);}
			,complete : function(request, status){scope.onComplete(request, status);}
		});
	}
	
	,onSuccess : function(data, status, request){
		this.fireEvent('success', {data:data, status:status, request:request});
	}
	
	,onError : function(request, status, error){		
		this.fireEvent('error', {error:error, status:status, request:request});
	}
	
	,beforeSend : function(request){
		this.fireEvent('beforesend', {request:request});
	}
	
	,onComplete : function(request, status){
		this.fireEvent('complete', {status:status, request:request});
	}
});