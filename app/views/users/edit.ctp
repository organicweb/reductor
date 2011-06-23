<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
	<h1>Modifier les informations de l'utilisateur</h1>
</div>
<?php if(@$group_id == 1) : ?>
<ul id="nav">
	<li>Urls
		<ul>
			<li><a href="/urls/add">Ajouter une url</a></li>
			<li><a href="/urls/index">Lister les urls</a></li>
		</ul>
	</li>
	<li>Utilisateurs
		<ul>
			<li><a href="/users/add">Ajouter un utilisateur</a></li>
			<li><a href="/users/index">Lister les utilisateurs</a></li>
			<li><a href="/users/logout">Déconnexion</a></li>
		</ul>
	</li>
	<li>Groupes
		<ul>
			<li><a href="/groups/index">Lister les groupes</a></li>
		</ul>
	</li>
</ul>
<?php endif; ?>
<div id="form_users">
<?php 
	echo $this->Form->create('User');
	echo $this->Form->input('id');
	echo $this->Form->input('username');
	echo $this->Form->input('password');
	echo $this->Form->end(__('Submit', true));
?>
</div>