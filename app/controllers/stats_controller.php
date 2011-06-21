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