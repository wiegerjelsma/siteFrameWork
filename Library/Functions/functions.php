<?php
function priceFormat($amount, $delimeter, $thousandseperator, $currencysign, $centsign){
	if($amount >= 1)
		return $currencysign.' '.number_format($amount, 2, $delimeter, $thousandseperator);
	return number_format($amount*100, 1, $delimeter, $thousandseperator).' '.$centsign;
}

function subtotalCountryPriceFormat($communicatecent, $subtotal, $delimeter, $thousandseperator, $currencysign, $centsign){
	if($communicatecent)
		return array($currencysign.' '.number_format($subtotal, 2, $delimeter, $thousandseperator), number_format($subtotal*100, 1, $delimeter, $thousandseperator).' '.$centsign);
	return array($currencysign.' '.number_format($subtotal, 2, $delimeter, $thousandseperator));
}

function subtotalPriceFormat($communicatecent, $subtotal, $delimeter, $thousandseperator, $currencysign, $centsign, $countries_count){
	if($communicatecent && $countries_count > 1)
		return array($currencysign.' '.number_format($subtotal, 2, $delimeter, $thousandseperator), number_format($subtotal*100, 1, $delimeter, $thousandseperator).' '.$centsign);
	return array($currencysign.' '.number_format($subtotal, 2, $delimeter, $thousandseperator));
}



function arrayMerge($a_Arr1, $a_Arr2){
    $a_Arr1 = is_array($a_Arr1) ? $a_Arr1 : array();
    $a_Arr2 = is_array($a_Arr2) ? $a_Arr2 : array();
    if(is_array($a_Arr2) && count($a_Arr2)){

		// loopen over alle keys van $a_Arr2
		foreach($a_Arr2 as $key => $value){

			// als de key niet bestaat in $a_Arr1 voegen we hem toe aan de $a_Arr1
			if(!array_key_exists($key, $a_Arr1)){
				$a_Arr1[$key] = $value;

			// Als de key wel bestaat in $a_Arr1
			} else {

				// als de value een array betreft gaan we de functie 'merge' aanroepen voor die value
				if(is_array($value) && count($value)){
					$a_Arr1[$key] = arrayMerge($a_Arr1[$key], $value);
				} else {

					// overschrijven de array met de waarde vanuit $a_Arr2
					$a_Arr1[$key] = $value;
				}
			}
		}
	}
	return $a_Arr1;
}

function arrayCombine($a_Arr1, $a_Arr2){
    $a_Arr1 = is_array($a_Arr1) ? $a_Arr1 : array();
    $a_Arr2 = is_array($a_Arr2) ? $a_Arr2 : array();
    if(is_array($a_Arr2) && count($a_Arr2)){

		// loopen over alle keys van $a_Arr2
		foreach($a_Arr2 as $key => $value){

			// als de key niet bestaat in $a_Arr1 voegen we hem toe aan de $a_Arr1
			if(!array_key_exists($key, $a_Arr1)){
				$a_Arr1[$key] = $value;

			// Als de key wel bestaat in $a_Arr1
			} else {

				// als de value een array betreft gaan we de functie 'merge' aanroepen voor die value
				if(is_array($value) && count($value)){
					$a_Arr1[$key] = arrayCombine($a_Arr1[$key], $value);
				} else {

					// nieuwe key aanmaken
					array_push($a_Arr1, $value);
					//$a_Arr1[$key] = $value;
				}
			}
		}
	}
	return $a_Arr1;
}