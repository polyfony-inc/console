<?php 

// main route for the __Table__ table
Polyfony\Router::map(
	'/__bundle__/__table__/:action/:id/',
	'__Bundle__/__Table__@{action}',
	'__table__'
);

?>