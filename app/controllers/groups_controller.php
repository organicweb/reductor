<?php
class GroupsController extends AppController {

	var $name = 'Groups';

	function beforeFilter() 
	{
	    parent::beforeFilter(); 
	    $this->Auth->allowedActions = array('*');
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
			$this->Session->setFlash(__('Invalid group', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('group', $this->Group->read(null, $id));
	}

	function add() 
	{
		if (!empty($this->data)) 
		{
			//Réservation d'un espace dans la db pour l'enregistrement
			$this->Group->create();
			
			//si la sauvegarde est ok, afficher un message
			if ($this->Group->save($this->data)) 
			{
				$this->Session->setFlash(__('The group has been saved', true));
				$this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('The group could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) 
	{
		//si l'id du groupe n'existe pas afficher un message d'erreur et rediriger
		if (!$id && empty($this->data)) 
		{
			$this->Session->setFlash(__('Invalid group', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) 
		{
			//si la sauvegarde est ok, afficher un message
			if ($this->Group->save($this->data)) 
			{
				$this->Session->setFlash(__('The group has been saved', true));
				$this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('The group could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) 
		{
			$this->data = $this->Group->read(null, $id);
		}
	}

	function delete($id = null) 
	{
		//si l'id du groupe n'existe pas afficher un message d'erreur et rediriger
		if (!$id) 
		{
			$this->Session->setFlash(__('Invalid id for group', true));
			$this->redirect(array('action'=>'index'));
		}
		//si la suppression est ok, afficher un message
		if ($this->Group->delete($id)) 
		{
			$this->Session->setFlash(__('Group deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		else
		{
			$this->Session->setFlash(__('Group was not deleted', true));
			$this->redirect(array('action' => 'index'));
		}
	}
}
?>