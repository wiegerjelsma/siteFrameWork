if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

/*RedactorPlugins.myimages = {
    init: function()
    {
        this.buttonAddBefore('video', 'myimages', 'My Modal', this.showMyModal);        
    },
    showMyModal: function()
    {
        var callback = $.proxy(function()
        {
            this.selectionSave();
            $('#redactor_modal #myimages-insert').click($.proxy(this.insertFromMyModal, this));
 
        }, this);
 
        // modal call
        this.modalInit('My Modal', '#myimages', 600, callback);
    },
    insertFromMyModal: function(html)
    {
        this.selectionRestore();
        this.insertHtml($('#myimages-textarea').val());
        this.modalClose();
    }
} */


RedactorPlugins.mymodal = {
    init: function()
    {
        this.buttonAdd('mymodal', 'My Modal', this.showMyModal);
    },
    showMyModal: function()
    {
        var callback = $.proxy(function()
        {
            this.selectionSave();
            $('#redactor_modal #mymodal-insert').click($.proxy(this.insertFromMyModal, this));
 
        }, this);
 
        // modal call
        this.modalInit('My Modal', '#mymodal', 500, callback);
    },
    insertFromMyModal: function(html)
    {
        this.selectionRestore();
        this.insertHtml($('#mymodal-textarea').val());
        this.modalClose();
    }
}