// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

//$(document).ready(function(){
$(window).load(function() {

	var a_Quotes = new Array();
	a_Quotes.push('Mobilisatie van de organen verbetert de doorbloeding waardoor het zenuwstelsel rondom de organen beter kan functioneren.');
	a_Quotes.push('De op- en neergaande beweging van de alvleesklier is nodig voor een goede afgifte van zijn spijsverteringssappen.');
	a_Quotes.push('Een gespannen twaalfvingerige darm bemoeilijkt de galafgifte en dus de vetvertering.');
	a_Quotes.push('Onze organen en ons bewegingsapparaat beïnvloeden elkaar via het zenuwstelsel.');
	a_Quotes.push('Een goede beweeglijkheid van het middenrif is nodig voor de beweging en functie van de organen.');
	a_Quotes.push('Na een operatie of trauma kunnen bewegingsbeperkingen ontstaan en jaren nadien nog problemen veroorzaken.');
	a_Quotes.push('De nieren leggen per dag 600 meter af bij een normale beweging van het middenrif.');
	a_Quotes.push('Verklevingen in de buik kunnen ten grondslag liggen aan nek- of rugklachten!');
	a_Quotes.push('Bewegingsbeperkingen kunnen ontstaan na blessures, chirurgie, overbelasting, slechte circulatie of ontstekingen...');
	a_Quotes.push('Door blokkades in de wervelkolom neemt de doorbloeding in onze ledematen en buikorganen af.');
	a_Quotes.push('Een geblokkeerd bekken of heiligbeen kan leiden tot een hernia!');
			
	var a_Images = new Array();
	a_Images.push('elleboog@1x');
	a_Images.push('schouder@1x');
	a_Images.push('rugtechniek1@1x');
	a_Images.push('rugtechniek2@1x');
	a_Images.push('rugtechniek3@1x');
	
	if($('body').data('SHORT_URL') == 'publicaties'){
		var a_Images = new Array();
		a_Images.push('publicaties@1x');		
	}	
	
	var waitForFinalEvent = (function () {
		var timers = {};
		return function (callback, ms, uniqueId) {
			if (!uniqueId) {
				uniqueId = "Don't call this twice without a uniqueId";
			}
			if (timers[uniqueId]) {
				clearTimeout (timers[uniqueId]);
			}
			timers[uniqueId] = setTimeout(callback, ms);
		};
	})();
		
	waitForFinalEvent(function(){
		setColumnHeights();	
		setHeaderImage();	
		setQuote();
	});
	

	var leftnum = $('body').data('CAPTCHALEFT');
	var rightnum = $('body').data('CAPTCHARIGHT');
	$('#sh').html("Hoeveel is "+leftnum+" + "+rightnum+"?");
	
	
	$(window).resize(function () {
    	waitForFinalEvent(function(){
			setColumnHeights();
			setHeaderImage();
		}, 500, "resized");
	});	
	
	function setColumnHeights(){
		// Hier #column-left & #column-right even hoog maken
		var columnLeftHeight = $('#column-left').height() + 20;	
		var columnRightHeight = $('#column-right').height() + 20;
	
		if(columnLeftHeight >= columnRightHeight){
			$('#column-left').height(columnLeftHeight);
			$('#column-right').height(columnLeftHeight);
		} else {
			$('#column-left').height(columnRightHeight);
			$('#column-right').height(columnRightHeight);		
		}			
	}
	
	function setHeaderImage(){
		var w = $(window).width();
		var type = w <= 640 ? 'small' : 'medium-up';		
		
		var imageindex = Math.floor(Math.random()*a_Images.length);	
		var image = $('body').data('WEBROOT')+'/images/headerimages/'+type+'/'+a_Images[imageindex]+'.jpg';				
	
		$('#header-image').attr("src",image);
	}
	
	function setQuote(){
		var quoteindex = Math.floor(Math.random()*a_Quotes.length);
		var quote = a_Quotes[quoteindex];			
		$('p.quote').text(quote);				
	}
	
	$('#facebooksharelink').click(function(event) {
	    var u = $('body').data('THIS_URL');
	    window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u),'sharer','toolbar=0,status=0,width=626,height=340');
	    return false;
	});	

	$('#twittersharelink').click(function(event) {
	    var url = $('body').data('THIS_URL');
	    window.open('https://twitter.com/share?url='+encodeURIComponent(url),'tweet','toolbar=0,status=0,width=626,height=340');
	    return false;
	});	
	
});
