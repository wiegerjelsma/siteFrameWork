/**
 * Redactor plugins
 */
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};


/**
 * @name	mylink
 * @desc	We gaan een pulldown button toevoegen met daarin
 *			- Interne link
 *			- Externe link
 *			- Link naar bestand
 */ 
RedactorPlugins.mylink = {
 
    init: function(){
	
		var linktypes = {
      		'blog': {
        		title: 'Naar een blog',
        		callback: function(obj, event, key) {
					obj.modalInit('Maak een link naar een blog', '#mylink_module', 650, callback_module_blog); // 500 is the width of the window        		
				}
      		},
      		'nieuws': {
        		title: 'Naar een nieuwsbericht',
        		callback: function(obj, event, key) {
					obj.modalInit('Maak een link naar een nieuwsbericht', '#mylink_module', 650, callback_module_nieuws); // 500 is the width of the window        		
				}
      		},
      		
      		'separator' : {						
      			name: 'separator'
      		},		
      		'intern': {
        		title: 'Naar een interne pagina',
        		callback: function(obj, event, key) {
					obj.modalInit('Maak een link naar een interne pagina', '#mylink_intern', 500, callback_intern); // 500 is the width of the window        		
				}
      		}      		
      		,'extern': {
       	 		title: 'Naar een externe pagina',
       	 		callback: function(obj, event, key) {
  					obj.modalInit('Maak een link naar een externe pagina', '#mylink_extern', 500, callback_extern); // 500 is the width of the window
        		}
      		},
      		'file': {
        		title: 'Naar een bestand',
        		callback: function(obj, event, key) {
//          			console.log('bestand');    
    				obj.modalInit('Maak een link naar een bestand', '#mylink_file', 500, callback_file); // 500 is the width of the window
        		}
      		},
      		'separator2' : {						
      			name: 'separator'
      		},		
      		'unlink': {
        		title: 'Verwijder de link',
        		exec: 'unlink'
      		}       		      	
    	};
    
		// Add dropdown to the Redactor toolbar
		this.addBtn('mylink', 'Maak een link', function(obj, event, key) {
			var html = obj.getSelectedHtml();
		}, linktypes);    	

		// Callback na het openen van de modal
		var callback_module_blog = $.proxy(function(){
			$('#redactor_modal select[module!="blog"]').hide();			
		
			this.saveSelection();		
			
			var selected = this.getSelectedHtml();
			$('#redactor_modal #mylink_module-linktext').val(selected);
			
			// set the domainname					
			$('#redactor_modal #label').text('Blog');

			// Handler na het sluiten van de modal
			$('#redactor_modal #mylink_module-insert').click($.proxy(function(){
				this.insertModule('blog');					
				return false;
					
			}, this));
		}, this);
		
		var callback_module_nieuws = $.proxy(function(){
			$('#redactor_modal select[module!="nieuws"]').hide();			
		
			this.saveSelection();		
			
			var selected = this.getSelectedHtml();
			$('#redactor_modal #mylink_module-linktext').val(selected);
			
			// set the domainname					
			$('#redactor_modal #label').text('Nieuwsbericht');

			// Handler na het sluiten van de modal
			$('#redactor_modal #mylink_module-insert').click($.proxy(function(){
				this.insertModule('nieuws');					
				return false;
					
			}, this));
		}, this);		
		
		

		// Callback na het openen van de modal
		var callback_intern = $.proxy(function(){
			this.saveSelection();		
			
			var selected = this.getSelectedHtml();
			$('#redactor_modal #mylink_intern-linktext').val(selected);					
			
			// Handler na het sluiten van de modal
			$('#redactor_modal #mylink_intern-insert').click($.proxy(function(){
				this.insertIntern();					
				return false;
			}, this));
		}, this);
		

		// Callback na het openen van de modal
		var callback_extern = $.proxy(function(){
			this.saveSelection();
			
			var selected = this.getSelectedHtml();			
			$('#redactor_modal #mylink_extern-linktext').val(selected);					
		
			// Handler na het sluiten van de modal
			$('#redactor_modal #mylink_extern-insert').click($.proxy(function(){
				this.insertExtern();					
				return false;					
			}, this));
		}, this);


		// Callback na het openen van de modal
		var callback_file = $.proxy(function(){
			this.saveSelection();
			
			var selected = this.getSelectedHtml();			
			$('#redactor_modal #mylink_file-linktext').val(selected);					
		
			// Handler na het sluiten van de modal
			$('#redactor_modal #mylink_file-insert').click($.proxy(function(){
				this.insertFile();					
				return false;					
			}, this));
		}, this);

		
		
		
		
	//	this.addBtn('internal_link', 'internal_link', function(obj)
	//	{
	//		obj.modalInit('internal_link', '#internal_link', 500, callback);			
	//	});	
		
	//	this.addBtnSeparatorBefore('internal_link');		
		
	}
	
	,insertLink : function(module, id, text, blank){
		var url = '{APPLICATION_URL/'+module+'/'+id+'}';		
		if(text)
			var name = text;
		else
			var name = url; 
		var target = blank ? 'target="_blank"' : 'target="_self"';
	
		this.restoreSelection();
		this.execCommand('unlink');
		this.execCommand('inserthtml', '<a href="'+url+'" '+target+'>'+name+'</a>');
		this.modalClose();	
	}
	
	,insertModule : function(type){
		var module = type;
		var id = $('#redactor_modal #mylink_module_'+module+'-linkid').val();
		var text = $('#redactor_modal #mylink_module-linktext').val();
		var blank = $('#redactor_modal #mylink_module-linkblank').is(':checked');	
		
		this.insertLink(module, id, text, blank);	
	}

	,insertIntern : function(){
		var module = 'page';
		var id = $('#redactor_modal #mylink_intern-linkpage').val();
		var text = $('#redactor_modal #mylink_intern-linktext').val();
		var blank = $('#redactor_modal #mylink_intern-linkblank').is(':checked');	
		
		this.insertLink(module, id, text, blank);
	}
	
	,insertExtern : function(){
		var url = $('#redactor_modal #mylink_extern-linkurl').val();
		url = url.replace(/^http:\/\//, '');
		var text = $('#redactor_modal #mylink_extern-linktext').val();
		
		this.restoreSelection();
		this.execCommand('inserthtml', '<a href="http://'+url+'" target="_blank">'+text+'</a>');
		this.modalClose();		
	}
	
	,insertFile : function(){
		var module = 'file';
		var id = $('#redactor_modal #mylink_file-file').val();
		var text = $('#redactor_modal #mylink_file-linktext').val();
		var blank = $('#redactor_modal #mylink_file-linkblank').is(':checked');	
		
		this.insertLink(module, id, text, blank);		
	}
}	

RedactorPlugins.fullscreen = {
	init: function()
	{
		this.fullscreen = false;

		this.buttonAdd('fullscreen', 'Fullscreen', $.proxy(this.toggleFullscreen, this));

		if (this.opts.fullscreen) this.toggleFullscreen();
	},
	enableFullScreen: function()
	{
		this.buttonChangeIcon('fullscreen', 'normalscreen');
		this.buttonActive('fullscreen');
		this.fullscreen = true;

		if (this.opts.toolbarExternal)
		{
			this.toolcss = {};
			this.boxcss = {};
			this.toolcss.width = this.$toolbar.css('width');
			this.toolcss.top = this.$toolbar.css('top');
			this.toolcss.position = this.$toolbar.css('position');
			this.boxcss.top = this.$box.css('top');
		}

		this.fsheight = this.$editor.height();
		if (this.opts.iframe) this.fsheight = this.$frame.height();

		if (this.opts.maxHeight) this.$editor.css('max-height', '');
		if (this.opts.iframe) var html = this.get();

		if (!this.$fullscreenPlaceholder) this.$fullscreenPlaceholder = $('<div/>');
		this.$fullscreenPlaceholder.insertAfter(this.$box);

		this.$box.appendTo(document.body);

		this.$box.addClass('redactor_box_fullscreen');
		$('body, html').css('overflow', 'hidden');

		if (this.opts.iframe) this.fullscreenIframe(html);

		this.fullScreenResize();
		$(window).resize($.proxy(this.fullScreenResize, this));
		$(document).scrollTop(0, 0);

		this.focus();
		this.observeStart();
	},
	disableFullScreen: function()
	{
		this.buttonRemoveIcon('fullscreen', 'normalscreen');
		this.buttonInactive('fullscreen');
		this.fullscreen = false;

		$(window).off('resize', $.proxy(this.fullScreenResize, this));
		$('body, html').css('overflow', '');

		this.$box.insertBefore(this.$fullscreenPlaceholder);
		this.$fullscreenPlaceholder.remove();

		this.$box.removeClass('redactor_box_fullscreen').css({ width: 'auto', height: 'auto' });

		if (this.opts.iframe) html = this.$editor.html();

		if (this.opts.iframe) this.fullscreenIframe(html);
		else this.sync();

		var height = this.fsheight;
		if (this.opts.autoresize) height = 'auto';
		if (this.opts.maxHeight) this.$editor.css('max-height', this.opts.maxHeight);

		if (this.opts.toolbarExternal)
		{
			this.$box.css('top', this.boxcss.top);
			this.$toolbar.css({
				'width': this.toolcss.width,
				'top': this.toolcss.top,
				'position': this.toolcss.position
			});
		}

		if (!this.opts.iframe) this.$editor.css('height', height);
		else this.$frame.css('height', height);

		this.$editor.css('height', height);
		this.focus();
		this.observeStart();
	},
	toggleFullscreen: function()
	{
		if (!this.fullscreen)
		{
			this.enableFullScreen();
		}
		else
		{
			this.disableFullScreen();
		}
	},
	fullscreenIframe: function(html)
	{
		this.$editor = this.$frame.contents().find('body');
		this.$editor.attr({ 'contenteditable': true, 'dir': this.opts.direction });

		// set document & window
		if (this.$editor[0])
		{
			this.document = this.$editor[0].ownerDocument;
			this.window = this.document.defaultView || window;
		}

		// iframe css
		this.iframeAddCss();

		if (this.opts.fullpage) this.setFullpageOnInit(html);
		else this.set(html);

		if (this.opts.wym) this.$editor.addClass('redactor_editor_wym');
	},
	fullScreenResize: function()
	{
		if (!this.fullscreen) return false;

		var toolbarHeight = this.$toolbar.height();

		var pad = this.$editor.css('padding-top').replace('px', '');
		var height = $(window).height() - toolbarHeight;
		this.$box.width($(window).width() - 2).height(height + toolbarHeight);

		if (this.opts.toolbarExternal)
		{
			this.$toolbar.css({
				'top': '0px',
				'position': 'absolute',
				'width': '100%'
			});

			this.$box.css('top', toolbarHeight + 'px');
		}

		if (!this.opts.iframe) this.$editor.height(height - (pad * 2));
		else
		{
			setTimeout($.proxy(function()
			{
				this.$frame.height(height);

			}, this), 1);
		}

		this.$editor.height(height);
	}
};