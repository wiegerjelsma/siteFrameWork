function priceFormat(amount, delimeter, thousandseperator, currencysign, centsign){
	if(amount >= 1)
		return currencysign + ' ' +number_format(amount, 2, delimeter, thousandseperator);
	return number_format(amount*100, 1, delimeter, thousandseperator)+ ' ' +centsign;
}

function subtotalCountryPriceFormat(communicatecent, subtotal, delimeter, thousandseperator, currencysign, centsign){
	if(communicatecent)
		return currencysign + ' ' +number_format(subtotal, 2, delimeter, thousandseperator)+' <span class=\'small\'>('+number_format(subtotal*100, 1, delimeter, thousandseperator)+ ' ' +centsign+')</span>';
	return currencysign + ' ' +number_format(subtotal, 2, delimeter, thousandseperator);
}


function subtotalPriceFormat(communicatecent, subtotal, delimeter, thousandseperator, currencysign, centsign, countries_count){
	if(communicatecent && countries_count > 1)
		return currencysign + ' ' +number_format(subtotal, 2, delimeter, thousandseperator)+' <span class=\'small\'>('+number_format(subtotal*100, 1, delimeter, thousandseperator)+ ' ' +centsign+')</span>';
	return currencysign + ' ' +number_format(subtotal, 2, delimeter, thousandseperator);
}
