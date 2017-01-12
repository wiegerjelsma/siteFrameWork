// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
$(document).foundation();

$(document).ready(function(){

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
		setHeaderImage();	
		setQuote();
	});
	
	var leftnum = $('body').data('CAPTCHALEFT');
	var rightnum = $('body').data('CAPTCHARIGHT');
	$('#sh').html("Hoeveel is "+leftnum+" + "+rightnum+"?");
		
	$(window).resize(function () {
    	waitForFinalEvent(function(){
			setHeaderImage();
		}, 500, "resized");
	});	
	
	if($('body').data('SHORT_URL') == 'ureterp'){
		init_map('ureterp');
	}
	if($('body').data('SHORT_URL') == 'dokkum'){
		init_map('dokkum');
	}
	if($('body').data('SHORT_URL') == 'groningen'){
		init_map('groningen');		
	}
		
	if($('body').data('SHORT_URL') == 'groningen' || $('body').data('SHORT_URL') == 'ureterp' || $('body').data('SHORT_URL') == 'dokkum'){
	    if($('#pano')){
			$(document).on('opened.fndtn.reveal', '[data-reveal]', function () {
				var modal = $(this).attr('id');
				if(modal == 'binnenkijken-popup')
					initializeStreetview($(this).data('lat'), $(this).data('lng'), $(this).data('heading'), $(this).data('pitch'));
			});		
	    }		
	}
    	
	function init_map(stad){
		if(stad == 'groningen'){
			var lat = 53.207965;
			var lng = 6.570204099999955;
			var cnt = '<b>OsteoVitaal Groningen</b><br/>Vechtstraat 73<br/> Groningen';
		} 
		if(stad == 'dokkum'){
			var lat = 53.3197742;
			var lng = 6.0148772;
			var cnt = '<b>OsteoVitaal Dokkum</b><br/>Bocksmeulen 41<br/> Dokkum';
		}
		if(stad == 'ureterp') {
			var lat = 53.093732;
			var lng = 6.16722;
			var cnt = '<b>OsteoVitaal Friesland</b><br/>Sichte 2<br/> Ureterp';			
		}

		var myOptions = {
			zoom:14,
			center:new google.maps.LatLng(lat,lng),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("c-map-canvas"), myOptions);
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(lat, lng)
		});
				
		infowindow = new google.maps.InfoWindow({
			content:cnt 
		});
		google.maps.event.addListener(marker, "click", function(){
			infowindow.open(map,marker);
		});
		infowindow.open(map,marker);
	}
	
	function initializeStreetview(lat,lng,heading,pitch){
		var location = new google.maps.LatLng(lat,lng); 
		var mapOptions = {}; 		
		var map = new google.maps.Map( document.getElementById('map-canvas'), mapOptions); 
		var panoramaOptions = { 
			position: location, 
			pov: { 
				heading: heading,
				pitch: pitch
			},
			addressControlOptions: {
				position: google.maps.ControlPosition.BOTTOM_CENTER
    		},
		    linksControl: false,
		    panControl: false,
		    zoomControlOptions: {
		      style: google.maps.ZoomControlStyle.SMALL
		    }, 
		    enableCloseButton: false   					 
		}; 
		
		var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions); 
		map.setStreetView(panorama);
    }	
	
	
function initialize() {
    var mapCanvas = document.getElementById('map-canvas');
    var mapOptions = {
      center: new google.maps.LatLng(44.5403, -78.5463),
      zoom: 8,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(mapCanvas, mapOptions);
  }
	
/*	function setColumnHeights(){
		setTimeout(function(){
			// Hier #column-left & #column-right even hoog maken
			var columnLeftHeight = $('#column-left').height() + 20;	
			var columnRightHeight = $('#column-right').height() + 20;
		
			console.log('columnLeftHeight: '+columnLeftHeight);
			console.log('columnRightHeight: '+columnRightHeight);
		
			if(columnLeftHeight >= columnRightHeight){
				$('#column-left').height(columnLeftHeight);
				$('#column-right').height(columnLeftHeight);
			} else {
				$('#column-left').height(columnRightHeight);
				$('#column-right').height(columnRightHeight);		
			}			
		}, 500);			
	} */
	
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
	    var u = 'http://osteovitaal.nl/';
	    window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u),'sharer','toolbar=0,status=0,width=626,height=340');
	    return false;
	});	

	$('#twittersharelink').click(function(event) {
	    var url = 'http://osteovitaal.nl/';
	    window.open('https://twitter.com/share?url='+encodeURIComponent(url),'tweet','toolbar=0,status=0,width=626,height=340');
	    return false;
	});	
	
});
