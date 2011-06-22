<?php
class GroupsController extends AppController {

	var $name = 'Groups';
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allowedActions = array('');
	}
	
	function index() 
	{
		$this->Group->recursive = 0;
		$this->set('groups', $this->paginate());
	}

	function view($id = null) 
	{
		if (!$id) 
		{
			//si l'id du groupe n'existe pas afficher un message d'erreur et rediriger
			$this->Session->setFlash(__('Groupe inconnu', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('group', $this->Group->read(null, $id));
	}

	function edit($id = null) 
	{
		//si l'id du groupe n'existe pas afficher un message d'erreur et rediriger
		if (!$id && empty($this->data)) 
		{
			$this->Session->setFlash(__('Groupe inconnu', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) 
		{
			//si la sauvegarde est ok, afficher un message
			if ($this->Group->save($this->data)) 
			{
				$this->Session->setFlash(__('Le groupe a été modifié', true));
				$this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('Le groupe n\'a pas pu être modifié. Veuillez réessayer.', true));
			}
		}
		if (empty($this->data)) 
		{
			$this->data = $this->Group->read(null, $id);
		}
	}
}
?>