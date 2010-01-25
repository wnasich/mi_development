<?php
/* SVN FILE: $Id$ */

/**
 * Short description for development.test.php
 *
 * Long description for development.test.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright            Copyright (c) 2008, Andy Dawson
 * @link                 www.ad7six.com
 * @package              base
 * @subpackage           base.tests.cases.behaviors
 * @since                v 1.0
 * @version              $Revision$
 * @modifiedBy           $LastChangedBy$
 * @lastModified         $Date$
 * @license              http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class MessageDevelopment extends CakeTestModel {

/**
 * useTable property
 *
 * @var string 'messages'
 * @access public
 */
	var $useTable = 'messages';

/**
 * actsAs property
 *
 * Use the random field for the order/sequence of the list behavior
 *
 * @var array
 * @access public
 */
	var $actsAs = array('Development');
}

/**
 * DevelopmentTestCase class
 *
 * @uses                 CakeTestCase
 * @package              base
 * @subpackage           base.tests.cases.behaviors
 */
class DevelopmentTestCase extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array('message');

/**
 * start method
 *
 * @return void
 * @access public
 */
	function start() {
		parent::start();
		$this->Message = new MessageDevelopment();
	}

/**
 * testFindWorks method
 *
 * @return void
 * @access public
 */
	function testFindWorks() {
		$results = $this->Message->find('first');
		$this->AssertEqual($results, true);
		$results = $this->Message->find('list');
		$this->AssertEqual($results, true);
		$results = $this->Message->find('all');
		$this->AssertEqual($results, true);
	}

/**
 * testDetectOldSyntax method
 *
 * @return void
 * @access public
 */
	function testDetectOldSyntax() {
		ob_start();
		$this->expectError();
		$results = $this->Message->find('all', array('conditions' => array('id' => '=1')));
		ob_clean();

		ob_start();
		$this->expectError();
		$results = $this->Message->find('all', array('conditions' => array('id' => '>1')));
		ob_clean();

		ob_start();
		$this->expectError();
		$results = $this->Message->find('all', array('conditions' => array('id' => '<1')));
		ob_clean();

		ob_start();
		$this->expectError();
		$results = $this->Message->find('all', array('conditions' => array('id' => '!=1')));
		ob_clean();

		ob_start();
		$this->expectError();
		$results = $this->Message->find('all', array('conditions' => array(array('id' => 'LIKE %x%'))));
		ob_clean();

		ob_start();
		$this->expectError();
		$results = $this->Message->find('all', array('conditions' => array(array('id' => '=1'))));
		ob_clean();
	}
}