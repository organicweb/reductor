<?php
class UsersController extends AppController {

	var $name = 'Users';
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allowedActions = array('group_id');
	}
	
	function group_id($id = null)
	{
		$group_id = $this->User->find('all', array(
			'fields'=>'group_id',
			'conditions'=>array('User.id'=>$id)
			));
		return $group_id[0]['User']['group_id'];
	}

	function index() 
	{
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());		
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() 
	{
		if (!empty($this->data)) 
		{
			//Réservation d'un espace dans la db
			$this->User->create();
			
			//fixation du type de groupe (user)
			$this->data['User']['group_id'] = 2;
			
			//Si la sauvegarde est ok afficher le message correspondant
			if ($this->User->save($this->data)) 
			{
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) 
		{
			$this->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

	function delete($id = null) 
	{
		if (!$id) 
		{
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) 
		{
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function initDB() 
	{
	    $group =& $this->User->Group;
	    //ACL autorisant tout pour l'admin dont le groupe id vaut 1
	    $group->id = 1;     
	    $this->Acl->allow($group, 'controllers');

	    //ACL de permission pour le groupe d'id 2
	    $group->id = 2;
	    $this->Acl->deny($group, 'controllers');
	    $this->Acl->allow($group, 'controllers/Urls/view');
	    $this->Acl->allow($group, 'controllers/Urls/add');
		$this->Acl->allow($group, 'controllers/Urls/delete');
		$this->Acl->allow($group, 'controllers/Urls/index');
		$this->Acl->allow($group, 'controllers/Users/login');
		$this->Acl->allow($group, 'controllers/Users/logout');
		
	    //we add an exit to avoid an ugly "missing views" error message
	    echo "all done";
	    exit;
	}
	
	function login() 
	{
		if ($this->Session->read('Auth.User')) 
		{
			$this->Session->setFlash('You are logged in!');
			//$this->redirect('/', null, false);
		}
	}       
	
	function logout()
	{
		$this->Session->setFlash('Good-Bye');
		$this->redirect($this->Auth->logout());
	}
}
?>