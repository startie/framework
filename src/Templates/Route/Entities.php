<?php

return $Entites = 
[
	#
	#
	#		PAGES
	#
	#

	'entities' => [
		'controller' => 'Entities::index',
		'type' => 'page',
		'title' => 'Entities',
		'layout' => 'page',
		'middles' => 'authAsk'
	],

	'entities/add' => [
		'controller' => 'Entities::add',
		'type' => 'page',
		'title' => 'Edit post',
		'layout' => 'page',
		'middles' => 'authAsk'
	],

	'entities/$id:int' => [
		'controller' => 'Entities::show',
		'type' => 'page',
		'title' => 'Edit post',
		'layout' => 'page',
		'middles' => 'authAsk'
	],

	'entities/$id:int/edit' => [
		'controller' => 'Entities::edit',
		'type' => 'page',
		'title' => 'Edit post',
		'layout' => 'page',
		'middles' => 'authAsk'
	],

	#
	#
	#		ACTIONS
	#
	#

	'entities/create' => [
		'controller' => 'Entities::create',
		'type' => 'action',
	],

	'entities/update' => [
		'controller' => 'Entities::update',
		'type' => 'action',
	],

	'entities/hide' => [
		'controller' => 'Entities::hide',
		'type' => 'action',
		'middles' => 'authAsk',
	],

	'entities/getAddForm' => [
		'controller' => 'Entities::getAddForm',
		'type' => 'action',
	],

	'entities/getEditForm' => [
		'controller' => 'Entities::getEditForm',
		'type' => 'action',
	],

	#
	#
	#		TASKS
	#
	#
	
	#
	#
	#		API
	#
	#	
];