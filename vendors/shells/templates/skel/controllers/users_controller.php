<?php
class UsersController extends AppController {

	var $name = 'Users';

	var $components = array();

	var $helpers = array();

	var $postActions = array(
		'admin_delete',
		'admin_sudo',
	);

/**
 * beforeFilter method
 *
 * Set the black hole to prevent white-screen-of-death symptoms for invalid form submissions.
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('authFields', $this->Auth->fields);
		$this->Auth->allow(
			'back',
			'confirm',
			'forgotten_password',
			'index',
			'logout',
			'profile',
			'register',
			'reset_password',
			'switch_language'
		);
		$this->Auth->autoRedirect = false;
		if (isset($this->Security)) {
			$this->Security->blackHoleCallback = '_blackHole';
		}
	}

/**
 * beforeRender method
 *
 * @return void
 * @access public
 */
	function beforeRender() {
		unset($this->data['User'][$this->Auth->fields['password']]);
		unset($this->data['User']['confirm']);
		unset($this->data['User']['current_password']);
		return parent::beforeRender();
	}

/**
 * admin_add method
 *
 * @return void
 * @access public
 */
	function admin_add() {
		if ($this->data) {
			if ($this->User->saveAll($this->data)) {
				$display = $this->User->display();
				$this->Session->setFlash(sprintf(__('User "%1$s" added', true), $display));
				return $this->_back();
			} else {
				$this->data = $this->User->data;
				if (Configure::read()) {
					foreach ($this->User->validationErrors as $i => &$error) {
						if (is_array($error)) {
							$error = implode($error, '<br />');
						}
					}
					$this->Session->setFlash(implode($this->User->validationErrors, '<br />'));
				} else {
					$this->Session->setFlash(__('errors in form', true));
				}
			}
		}
		$this->_setSelects();
		$this->render('admin_edit');
	}

/**
 * admin_delete method
 *
 * @param mixed $id null
 * @return void
 * @access public
 */
	function admin_delete($id = null) {
		$this->User->id = $id;
		if ($id && $this->User->exists()) {
			$display = $this->User->display($id);
			if ($this->User->delete($id)) {
				$this->Session->setFlash(sprintf(__('User %1$s "%2$s" deleted', true), $id, $display));
			} else {
				$this->Session->setFlash(sprintf(__('Problem deleting User %1$s "%2$s"', true), $id, $display));
			}
		} else {
			$this->Session->setFlash(sprintf(__('User with id %1$s doesn\'t exist', true), $id));
		}
		return $this->_back();
	}

/**
 * admin_edit method
 *
 * @param mixed $id null
 * @return void
 * @access public
 */
	function admin_edit($id = null) {
		if ($this->data) {
			if ($this->User->saveAll($this->data)) {
				$display = $this->User->display();
				$this->Session->setFlash(sprintf(__('User "%1$s" updated', true), $display));
				return $this->_back();
			} else {
				$this->data = $this->User->data;
				if (Configure::read()) {
					foreach ($this->User->validationErrors as $i => &$error) {
						if (is_array($error)) {
							$error = implode($error, '<br />');
						}
					}
					$this->Session->setFlash(implode($this->User->validationErrors, '<br />'));
				} else {
					$this->Session->setFlash(__('errors in form', true));
				}
			}
		} elseif ($id) {
			$this->data = $this->User->read(null, $id);
			if (!$this->data) {
				$this->Session->setFlash(sprintf(__('User with id %1$s doesn\'t exist', true), $id));
				$this->_back();
			}
		} else {
			return $this->_back();
		}
		$this->_setSelects();
	}

/**
 * admin_index method
 *
 * @return void
 * @access public
 */
	function admin_index() {
		if (isset($this->SwissArmy)) {
			$conditions = $this->SwissArmy->parseSearchFilter();
		} else {
			$conditions = array();
		}
		if ($conditions) {
			$this->set('filters', $this->User->searchFilterFields());
			$this->set('addFilter', true);
		}
		$this->data = $this->paginate($conditions);
		$this->_setSelects();
	}

/**
 * admin_lookup method
 *
 * @param string $input ''
 * @return void
 * @access public
 */
	function admin_lookup($input = '') {
		$this->autoRender = false;
		if (!$input) {
			$input = $this->params['url']['q'];
		}
		if (!$input) {
			$this->output = '0';
			return;
		}
		$conditions = array(
			'id LIKE' => $input . '%',
			'id LIKE' => $input . '%'
		);
		if (!$this->data = $this->User->find('list', compact('conditions'))) {
			$this->output = '0';
			return;
		}
		return $this->render('/elements/lookup_results');
	}

/**
 * admin_multi_add method
 *
 * @return void
 * @access public
 */
	function admin_multi_add() {
		if ($this->data) {
			$data = array();
			foreach ($this->data as $key => $row) {
				if (!is_numeric($key)) {
					continue;
				}
				$data[$key] = $row;
			}
			if ($this->User->saveAll($data, array('validate' => 'first', 'atomic' => false))) {
				$this->Session->setFlash(sprintf(__('Users added', true)));
				$this->_back();
			} else {
				if (Configure::read()) {
					foreach ($this->User->validationErrors as $i => &$error) {
						if (is_array($error)) {
							$error = implode($error, '<br />');
						}
					}
					if($this->User->validationErrors) {
						$this->Session->setFlash(implode($this->User->validationErrors, '<br />'));
					} else {
						$this->Session->setFlash(__('Save did not succeed with no validation errors', true));
					}
				} else {
					$this->Session->setFlash(__('Some or all additions did not succeed', true));
				}
			}
		} else {
			$this->data = array('1' => array('User' => $this->User->create()));
			$this->data[1]['User']['id'] = null;
		}
		$this->_setSelects();
		$this->render('admin_multi_edit');
	}

/**
 * admin_multi_edit method
 *
 * @return void
 * @access public
 */
	function admin_multi_edit() {
		if ($this->data) {
			$data = array();
			foreach ($this->data as $key => $row) {
				if (!is_numeric($key)) {
					continue;
				}
				$data[$key] = $row;
			}
			if ($this->User->saveAll($data, array('validate' => 'first'))) {
				$this->Session->setFlash(sprintf(__('Users updated', true)));
			} else {
				if (Configure::read()) {
					foreach ($this->User->validationErrors as $i => &$error) {
						if (is_array($error)) {
							$error = implode($error, '<br />');
						}
					}
					if($this->User->validationErrors) {
						$this->Session->setFlash(implode($this->User->validationErrors, '<br />'));
					} else {
						$this->Session->setFlash(__('Save did not succeed with no validation errors', true));
					}
				} else {
					$this->Session->setFlash(__('Some or all updates did not succeed', true));
				}
			}
			$this->params['paging'] = $this->Session->read('User.paging');
			$this->helpers[] = 'Paginator';
		} else {
			$args = func_get_args();
			call_user_func_array(array($this, 'admin_index'), $args);
			array_unshift($this->data, 'dummy');
			unset($this->data[0]);
			$this->Session->write('User.paging', $this->params['paging']);
		}
		$this->_setSelects();
	}

/**
 * admin_search method
 *
 * @param mixed $term null
 * @return void
 * @access public
 */
	function admin_search($term = null) {
		if ($this->data) {
			$term = trim($this->data['User']['query']);
			$url = array(urlencode($term));
			if ($this->data['User']['extended']) {
				$url['extended'] = true;
			}
			$this->redirect($url);
		}
		$request = $_SERVER['REQUEST_URI'];
		$term = trim(str_replace(Router::url(array()), '', $request), '/');
		if (!$term) {
			$this->redirect(array('action' => 'index'));
		}
		$conditions = $this->User->searchConditions($term, isset($this->passedArgs['extended']));
		$this->Session->setFlash(sprintf(__('All Users matching the term "%1$s"', true), htmlspecialchars($term)));
		$this->data = $this->paginate($conditions);
		$this->_setSelects();
		$this->render('admin_index');
	}

/**
 * assume the identity of another user
 *
 * @param mixed $id null
 * @return void
 * @access public
 */
	function admin_sudo($id = null) {
		$this->Session->destroy();
		$this->Auth->login($id);
		$this->postLogin();
		$this->redirect('/');
	}

/**
 * admin_view method
 *
 * @param mixed $id null
 * @return void
 * @access public
 */
	function admin_view($id = null) {
		$this->data = $this->User->read(null, $id);
		if(!$this->data) {
			$this->Session->setFlash(__('Invalid user', true));
			return $this->_back();
		}
	}

/**
 * back method
 *
 * the steps var is the number of steps to go back, incremented by 1 as the page displaying the back
 * link is the previous page, which would give the impression of going nowhere
 *
 * @param int $steps
 * @return void
 * @access public
 */
	function back($steps = 1) {
		$clear = isset($this->passedArgs['deleteHistoryEntry']);
		return $this->_back((int)$steps + 1, '/', true, $clear);
	}

/**
 * change_password method
 *
 * Used for changing the password of a logged in user
 *
 * @return void
 * @access public
 */
	function change_password() {
		if ($this->data) {
			list($return, $message) = $this->User->changePassword($this->data, $this->Auth->user());
			if ($message) {
				$this->Session->setFlash($message);
			}
			if ($return) {
				return $this->redirect('/');
			}
		}
		$strengths = array_keys($this->User->passwordPolicies());
		$this->set('strengths', array_combine($strengths, $strengths));
	}

/**
 * confirm method
 *
 * @param mixed $token
 * @return void
 * @access public
 */
	function confirm($token = null) {
		$this->set('token', $token);
		$fields = $this->User->accountFields();
		$this->set('fields', $fields);
		if (!$this->data) {
			return;
		}
		list($return, $message) = $this->User->confirmAccount($this->data);
		if ($message) {
			$this->Session->setFlash($message);
		}
		if ($return) {
			$this->Session->write('Auth.redirect', '/'); // Prevent auth from sending you back here
			return $this->redirect('/');
		}
	}

/**
 * edit method
 *
 * @return void
 * @access public
 */
	function edit() {
		if ($this->data) {
			$this->data['User']['id'] = $this->Auth->user('id');
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('profile updated', true));
				return $this->_back();
			} else {
				$this->Session->setFlash(__('errors in form', true));
			}
		} else {
			$this->data = $this->User->read(null, $this->Auth->user('id'));
		}
		$this->_setSelects();
	}

/**
 * forgotten_password method
 *
 * Send the user an email with a confirmation link/token in it. Use the $email (which could be an email or a username)
 * to find the users id. Don't send another email if there is one that is pending
 *
 * @access public
 * @return void
 */
	function forgotten_password() {
		if ($this->data) {
			$email = $this->data['User']['email'];
			if (!$email) {
				$this->Session->setFlash(__('email missing', true));
				return;
			}
			list($return, $message) = $this->User->forgottenPassword($this->data['User']['email']);
			if ($message) {
				$this->Session->setFlash($message);
			}
			if ($return) {
				$this->redirect(array('action' => 'reset_password'));
			}
		}
	}

/**
 * index method
 *
 * @return void
 * @access public
 */
	function index() {
		return $this->redirect('/', 301);
	}

/**
 * login method
 *
 * Only run if there is no user
 *
 * @access public
 * @return void
 */
	function login() {
		if ($this->data) {
			if ($this->Auth->user('id')) {
				$this->User->id = $this->Auth->user('id');
				if (!empty($this->data['User']['remember_me'])) {
					$token = $this->User->token(null, array('length' => 100, 'fields' => array(
						$this->Auth->fields['username'], $this->Auth->fields['password']
					)));
					$this->SwissArmy->loadComponent('Cookie');
					$this->Cookie->write('User.id', $this->User->id, true, '+2 weeks');
					$this->Cookie->write('User.token', $token, true, '+2 weeks');
				}
				$display = $this->User->display();
				$this->Session->setFlash(sprintf(__('Welcome back %1$s.', true), $display));
				if ($this->RequestHandler->isAjax() && !empty($this->params['refresh'])) {
					return $this->_back(null, true);
				}
				return $this->_back();
			}
		} elseif ($this->Auth->user('id')) {
			return $this->_back(null, true);
		}
		if (Configure::read()) {
			$this->Session->setFlash('Debug only message: Save some tedium - check remember me.');
		}
	}

/**
 * logout method
 *
 * Delete the users cookie (if any), log them out, and send them a parting flash meassage. If no user is logged in just
 * send them back to where they came from (no reference to the session refer).
 *
 * @access public
 * @return void
 */
	function logout() {
		if ($this->Auth->user()) {
			$this->SwissArmy->loadComponent('Cookie');
			$this->Cookie->del('User');
			$this->Session->destroy();
			$this->Session->setFlash(__('now logged out', true));
		}
		$this->redirect($this->Auth->logout());
	}

/**
 * profile method
 *
 * @param mixed $username
 * @access public
 * @return void
 */
	function profile($username = null) {
		if ($username && $username != $this->Auth->user($this->Auth->fields['username'])) {
			/* Temp */
			$this->Session->setFlash(__('Not implemented', true));
			return $this->_back();
			/* Temp End */
			$id = $this->User->field('id', array($this->Auth->fields['username'] => $username));
		} else {
			$id = $this->Auth->user('id');
		}
		if (!$id) {
			$this->Session->setFlash(__('User not found', true));
			return $this->_back();
		}
		$conditions['User.id'] = $id;
		$this->data = $this->User->find('first', compact('conditions', 'contain'));
		if (!$this->data) {
			$this->Session->setFlash(__('User not found', true));
			return $this->_back();
		}
	}

/**
 * register method
 *
 * @access public
 * @return void
 */
	function register() {
		if (Configure::read()) {
			if (!$this->User->find('count')) {
				$message = __('Create a site admin user.', true);
				$this->Session->setFlash($message);
			}
		} else {
			$message = __('Registrations are disabled.', true);
			$this->Session->setFlash($message, 'info');
			$this->redirect('/');
		}
		if ($this->data) {
			if (Configure::read() && !$this->User->find('count')) {
				if (isset($this->User->Group)) {
					$this->data['User']['group_id'] = $this->User->Group->field('id',
						array('name' => 'Admin'));
				} else {
					$this->data['User']['group'] = 'admin';
				}
			}
			list($return, $message) = $this->User->register($this->data);
			if ($message) {
				$this->Session->setFlash($message);
			}
			if ($return) {
				$this->Auth->login($this->User->id);
				return $this->redirect('/');
			}
		}
		$this->set('passwordPolicy', $this->User->passwordPolicy());
	}

/**
 * reset_password method
 *
 * Used to set a new password after requesting a reset via the forgotten password method
 *
 * @param string $token
 * @access public
 * @return void
 */
	function reset_password($token = null) {
		$this->set('token', $token);
		$loggedInUser = $this->User->id = $this->Auth->user('id');
		if ($loggedInUser) {
			$this->redirect(array('action' => 'change_password'));
		}
		$this->set('fields', $this->User->Behaviors->UserAccount->settings['User']['fields']);
		if (!$this->data) {
			return $this->render('confirm');
		}
		list($return, $message) = $this->User->resetPassword($this->data);
		if ($message) {
			$this->Session->setFlash($message);
		}
		if ($return) {
			$this->Session->write('Auth.redirect', '/'); // Prevent auth from sending you back here
			return $this->redirect(array('action' => 'login'));
		}
		$view = 'confirm';
		if ($this->data) {
			if (empty($this->User->validationErrors[$this->Auth->fields['username']]) &&
				empty($this->User->validationErrors['token'])) {
				$view = 'reset_password';
			}
		}
		$strengths = array_keys($this->User->passwordPolicies());
		$this->set('strengths', array_combine($strengths, $strengths));
		$this->render($view);
	}

/**
 * switch_language method
 *
 * @param string $language
 * @access public
 * @return void
 */
	function switch_language($language = 'eng') {
		$this->SwissArmy->loadComponent('Cookie');
		$this->Cookie->write('lang', $language, null, '+350 day');
		$this->Session->write('Config.language', $language);
		$this->_back();
	}

/**
 * blackHole method. Handles form submissions deemed invalid by the security component
 *
 * If a login is blackholed, there are 2 possible causes
 * 	1) The user went to /users/login but the form was tampered or the security token out of date
 * 	2) They used the sidebar login form, and the <not-users> controller doesn't use the security component
 *
 * In the first case, there is nothing to do but send the user back to the login form. In the second case, check if
 * their form submission contains a valid (session) user login token, and if so allow them to login; Otheriwse send to
 * the login form. This logic allows the users controller to use the security component, without forcing the rest of the
 * application to do so.
 *
 * If a user is already logged in, and the current action is not a login, then the user submitted a stale form -
 * call the parent blackHole handling method.
 *
 * @param mixed $reason
 * @return void
 * @access protected
 */
	function _blackHole($reason = null) {
		if ($reason == 'auth' && $this->action == 'login') {
			$formToken = isset($this->data['User']['login_token'])?$this->data['User']['login_token']:false;
			$sessionToken = $this->Session->read('User.login_token');
			if (!isset($this->data['_Token']) && $formToken && $sessionToken && $formToken === $sessionToken) {
				return true;
			}
			$token = Security::hash(String::uuid(), null, true);
			$this->Session->write('User.login_token', $token);
			$this->Session->setFlash(__('Invalid login submission', true));
			$this->redirect($this->Auth->loginAction);
		}
		return parent::_blackHole($reason);
	}
}