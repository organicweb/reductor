<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
	<h1>Authentification</h1>
</div>
<div id="form_users">
<?php
echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' =>'login')));
echo $this->Form->input('User.username', array('placeholder'=>'Entrez ici votre identifiant'));
echo $this->Form->input('User.password', array('placeholder'=>'Entrez ici votre mot de passe'));
echo $this->Form->end('Login');
?>
