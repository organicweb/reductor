<?php

class StatsController extends AppController 
{
	var $name = 'Stats';
	
	function index() 
	{
		$this->Stat->recursive = 0;
		$this->set('stats', $this->paginate());
		
		//compte le nombre de cliques total
		$counter['AllClicks'] = $this->Stat->find('count',array(
			'fields'=>'url_id'
			));
		
		//compte le nombre d'@IP différentes
		$counter['IpDistinct'] = $this->Stat->find('count',array(
			'fields'=>'DISTINCT Stat.adrIp'
			));	
			
		//compte le nombre d'Url différentes
		$counter['UrlDistinct'] = $this->Stat->find('count',array(
			'fields'=>'DISTINCT url_id'
			));
				
		//envoi des informations à la page index.ctp
		$this->set('counter', $counter);
	}

	function edit($id = null) 
	{
		if (!$id && empty($this->data)) 
		{
			$this->Session->setFlash(__('Invalid Stat', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) 
		{
			if ($this->Stat->save($this->data)) 
			{
				$this->Session->setFlash(__('The stat has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The stat could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) 
		{
			$this->data = $this->Stat->read(null, $id);
		}
	}

	function delete($id = null) 
	{
		if (!$id) 
		{
			$this->Session->setFlash(__('Invalid id for stat', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Stat->delete($id)) 
		{
			$this->Session->setFlash(__('Stat deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Stat was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
		
}
?>