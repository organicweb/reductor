<?php
class UsersController extends AppController {

	var $name = 'Users';
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allowedActions = array('group_id', 'add', 'lostPassword', 'alterPassword');
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
			$this->Session->setFlash(__('Utilisateur inconnu', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function userToAdmin($user_id)
	{
		
		//Retourne le group_id de l'utilisateur sélectionné
		$group_id = $this->User->find('all', array(
			'fields'=>'group_id',
			'conditions'=>array('User.id'=>$user_id)
			));
					
		if($group_id[0]['User']['group_id'] == 1)
		{
			$group_id[0]['User']['group_id'] = 2;
			$this->User->save($group_id[0]);		
		}
		elseif($group_id[0]['User']['group_id'] == 2)
		{
			$group_id[0]['User']['group_id'] = 1;
			$this->User->save($group_id[0]);	
		}
		$this->redirect(array('action' => 'index'));
	}

	function add() 
	{
		$group_id = $this->Session->read('Auth.User.group_id');
		if(isset($group_id))
		{
			$this->set('group_id', $group_id);
		}
		if (!empty($this->data)) 
		{
			//Réservation d'un espace dans la db
			$this->User->create();
			
			//fixation du type de groupe (user)
			$this->data['User']['group_id'] = 2;
			
			//Création du token
			$created = date('Y-m-d H:i:s');
			$username = $this->data['User']['username'];
			$this->data['User']['token'] = $this->Auth->password($username.$created);	
			
			//Si la sauvegarde est ok afficher le message correspondant
			if ($this->User->save($this->data)) 
			{
				$mail['token'] = $this->data['User']['token']; 
				$mail['username'] = $username;
				$mail['created'] = $created;
				$this->Session->setFlash(__('L\'utilisateur à été enregistré', true));
				$this->Email->to = $username;
				$this->Email->subject = 'Confirmation d\'inscription à ow.gs';
				$this->Email->template = 'simple_message';
				$this->Email->sendAs = 'html';
				$this->set('mail', $mail);
				if($this->Email->send($mail, 'simple_message', 'default'))
				{
					$this->redirect(array('action' => 'login'));
				}
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

	function lostPassword($token)
	{
		$listOfTokens = $this->User->find('all', array(
							'fields'=>array('token', 'username', 'id')
							));
		
		for($i=0, $ok = false ; $i<count($listOfTokens) && $ok == false ; $i++)
		{
			if($listOfTokens[$i]['User']['token'] == $token)
			{
				$username = $listOfTokens[$i]['User']['username'];
				$id = $listOfTokens[$i]['User']['id'];
				$ok = true;
			}
		}
		if($ok)
		{
			//Génération du mot de passe temporaire
			$tmpPassword = substr($token, rand(0, 12), 8);
			$shaPassword = $this->Auth->password($tmpPassword);
			$this->User->id = $id;
			$this->User->save(array('password'=>$shaPassword));
			
			//Envoi des variables à la vue
			$mail['tmpPassword'] = $tmpPassword; 
			$mail['username'] = $username;
			$mail['token'] = $token;
			
			//Envoi du mail
			$this->Email->to = $username;
			$this->Email->subject = 'Oubli de mot de passe sur ow.gs';
			$this->Email->template = 'lost_password';
			$this->Email->sendAs = 'html';
			$this->set('mail', $mail);
			
			if($this->Email->send($mail, 'lost_password', 'default'))
			{
				$this->redirect(array('action' => 'login'));
			}
		}
		else
		{
			$this->redirect(array('action' => 'add'));
		}
	}
	
	function alterPassword($token)
	{
		if(isset($token) && $token != '')
		{
			$this->User->save($this->data);
		}
		
		//$this->redirect(array('action' => 'login'));
	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Utilisateur incorrect', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('L\'utilisateur a été enregistré.', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('L\'utilisateur n\'a pas pu être enregistré. Veuillez réessayer.', true));
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
			$this->Session->setFlash(__('Identifiant invalide pour l\'utilisateur', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) 
		{
			$this->Session->setFlash(__('L\'utilisateur a été supprimé', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('L\'utilisateur n\'a pas été supprimé', true));
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
		$this->Acl->allow($group, 'controllers/Urls/get_url');
		$this->Acl->allow($group, 'controllers/Users/alterPassword');		
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
			$this->Session->setFlash('Vous êtes authentifié !');
			$this->redirect(array('controller'=>'urls', 'action'=>'add'));
		}
	}       
	
	function logout()
	{
		$this->Session->setFlash('Au revoir');
		$this->redirect($this->Auth->logout());
	}
}
?>