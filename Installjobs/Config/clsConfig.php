<?php
/**
 * De config gaan we eigenlijk net zoals de andere files regelen
 * 
 * In de _bin directory maken we een dir Config.
 * In die dir:
 * 	Applications
 * 	Modules
 * 	Adapters
 * 
 * In de App dir bijvoorbeeld:
 * application.conf
 * dir: Subapp
 * 
 * etcetera. Dus de basis application config staat weer in de Applications root. Net zoals de basis module in de Modules root staat.
 * 
 * 
 * Dus voor app wellxone
 * 
 * _bin
 * 		- wellxone
 * 			- Config
 * 				framework.conf
 * 				application.conf
 * 				- Modules
 * 					module.conf
 * 					- Test
 * 						test.conf
 * 				- Adapters
 * 					adapters.conf
 * 					- Template
 * 						template.conf
 * 		- wellxone.cli
 * 
 * Op deze manier kunnen we per applicatie heel gemakkelijk de config inladen.
 * Elke file bevat ook de config van het framework. Dus het zijn allemaal al gestapelde configuratiefiles.
 * 
 * 1) Laadt application.conf
 * require '_bin/wellxone/Config/application.conf'
 * 
 * 2) Laadt config Module Test
 * require '_bin/wellxone/Config/Modules/Test/test.conf'; // Nu laden we dus framework, application, module en test.conf
 * 
 * 
 */