<?php


/**
 * These are the default application settings.
 *
 * This file contains only example data. The intented use is to not habitually instanciate the setting model but to use
 * the following approach when wanting to load your app settings:
 *
 * $array = MiCache::setting('Section');
 * $aString = MiCache::setting('Section.astring');
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.config
 * @since         v 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
$config = array (
	'Users' => array (
		'allowRegistrations' => array (
			'description' => 'This value determins whether /users/register is enabled or disabled',
			'value' => true,
			'type' => 'boolean',
		),
	),
	'TestValues' => array (
		'positive' => array (
			'description' => 'a positive boolean value',
			'value' => true,
			'type' => 'boolean',
			'read_only' => 'true',
		),
		'negative' => array (
			'description' => 'a negative boolean value',
			'value' => false,
			'type' => 'boolean',
		),
		'anumber' => array (
			'description' => 'a numerical value',
			'value' => 22,
			'type' => 'integer',
		),
		'astring' => array (
			'description' => 'a string value',
			'value' => 'string',
			'type' => 'string',
		),
		'subsection' => array (
			'positive' => array (
				'description' => 'a positive boolean value',
				'value' => true,
				'type' => 'boolean',
			),
			'negative' => array (
				'description' => 'a negative boolean value',
				'value' => false,
				'type' => 'boolean',
			),
			'anumber' => array (
				'description' => 'a numerical value',
				'value' => 22,
				'type' => 'integer',
			),
			'astring' => array (
				'description' => 'Configure class stops at this level',
				'value' => 'sub string',
				'value' => 'string',
				'type' => 'string',
			),
			'subsubsection' => array (
				'positive' => array (
					'description' => 'a positive boolean value',
					'value' => true,
					'type' => 'boolean',
				),
				'negative' => array (
					'description' => 'a negative boolean value',
					'value' => false,
					'type' => 'boolean',
				),
				'anumber' => array (
					'description' => 'a numerical value',
					'value' => 22,
					'type' => 'integer',
				),
				'astring' => array (
					'description' => 'Boldly going where the Configure class won\'t',
					'value' => 'sub sub string',
					'type' => 'string',
				),
				'subsubsubsection' => array (
					'positive' => array (
						'description' => 'a positive boolean value',
						'value' => true,
						'type' => 'boolean',
					),
					'negative' => array (
						'description' => 'a negative boolean value',
						'value' => false,
						'type' => 'boolean',
					),
					'anumber' => array (
						'description' => 'a numerical value',
						'value' => 22,
						'type' => 'integer',
					),
					'astring' => array (
						'description' => 'Still boldly going',
						'value' => 'sub sub sub string',
						'type' => 'string',
					),
					'subsubsubsubsection' => array (
						'positive' => array (
							'description' => 'a positive boolean value',
							'value' => true,
							'type' => 'boolean',
						),
						'negative' => array (
							'description' => 'a negative boolean value',
							'value' => false,
							'type' => 'boolean',
						),
						'anumber' => array (
							'description' => 'a numerical value',
							'value' => 22,
							'type' => 'integer',
						),
						'astring' => array (
							'description' => 'Etc.',
							'value' => 'sub sub sub sub string',
							'type' => 'string',
						),
					),
				),
			),
		),
	)
);