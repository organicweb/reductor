<?php
class AppController extends Controller 
{
    var $components = array('Acl', 'Auth', 'Session', 'Email');
    var $helpers = array('Javascript', 'Html', 'Form', 'Session');

    function beforeFilter() 
	{
        //Configure AuthComponent
        $this->Auth->authorize = 'actions';
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
        $this->Auth->logoutRedirect = '/';
        $this->Auth->loginRedirect = array('controller' => 'urls', 'action' => 'add');
		$this->Auth->actionPath = 'controllers/';
		$this->Auth->allowedActions = array('display');
	 
    }
}
?>
