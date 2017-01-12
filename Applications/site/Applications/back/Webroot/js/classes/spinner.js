/**
 * 			
 * @name	Spinner
 * @params	appendTo
 * 			size : 20 / 16
 * 			color : white / grey / blue
 * 
 * @usage	var spin = new Spinner({
				appendTo : 'body'
				,size : '16'
				,color : 'grey'
			});
			spin.renderAndAppend();
			spin.stop();
 */
var Spinner = Module.extend({				

	type : 'spn'
	,speed : 50
	,interval : null
	,frame : 1
	,topPosition : 'top'
		
   	,init: function(params){
		this._super($.extend({
			tpl : 'spinner'
			,size : '20'
			,color : 'white'
		}, params || {}));									
	}

	,setCss : function(){		
		var colorObject = {'white':1,'grey':2,'blue':3};
		this.topPosition = ((this.params.size * colorObject[this.params.color]) - this.params.size)*-1;
		var webroot = $('body').data("Webroot");
		
		var cssObject = {
				'width' : this.params.size+'px'
				,'height' : this.params.size+'px'
			};	
		
		$('#'+this.params.id).css(cssObject);
	}

	,append : function(){
		$(this.params.appendTo).append(this.rendered);
		this.setCss();
		this.start();
		this.fireEvent('afterappend');
	}
	
	,redraw : function(){
		this.frame = (this.frame > 11) ? 1 : this.frame+1;
		var pos = (this.params.size * (this.frame-1))*-1;
		$('#'+this.params.id).css('background-position', pos+'px '+this.topPosition+'px');					
	}

	,start : function(){
		this.interval = setInterval((function(self){
			return function(){
				self.redraw();
			}})(this), this.speed);
	}
	
	,stop : function(){	
		var self = this;
		$('#'+this.params.id+'-wrapper').fadeOut(1000, function(){
			$('#'+self.params.id).fadeOut(1000, function(){
				if(self.interval)
					clearInterval(self.interval);					
			});		
		});
	}
});