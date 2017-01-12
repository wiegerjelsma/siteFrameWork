<?php
# eventuele dubbele extenties voor domeinnamen. Op deze manier fetchen we toch goed het subdomain en hoofddomain.
# Komt bij textntell.it niet voor.
$doubleext = array('.com.my','.co.uk','.com.au','.com.hk','.co.nz','.com.ph','.com.sg','.com.tw','.com.pt','.com.mx');

# welke app draait op welke domeinnaam
$domains['http'][SITENAME.'.macbook.com'] = array('site.back','site.back.admin','site.back.user','site.front');
$domains['http']['wgsframework.nl'] = array('site.back','site.back.admin','site.back.user','site.front');
$domains['http']['osteovitaal.nl'] = array('site.back','site.back.admin','site.back.user','site.front');
$domains['http']['gwin.nl'] = array('site.back','site.back.admin','site.back.user','site.front');



//$domains['http']['wonderwens.nl'] = array('ww.back','ww.back.admin','ww.back.user','ww.front');

#Subdomain redirects
$subdomains[''] = '';