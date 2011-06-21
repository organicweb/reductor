<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
	<h1>Détails de l'utilisateur</h1>
</div>
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
<div id="view_users">
	<table>
		<tr>
			<th>Id</th>
			<th>Nom d'utilisateur</th>		
			<th>Mot de passe</th>		
			<th>Nom du groupe</th>		
			<th>Crée le :</th>		
			<th>Modifié le :</th>	
		</tr>
		<tr>
			<td><?php echo $user['User']['id']; ?>&nbsp;</td>
			<td><?php echo $user['User']['username']; ?>&nbsp;</td>
			<td><?php echo $user['User']['password']; ?>&nbsp;</td>
			<td><?php echo $this->Html->link($user['Group']['name'], array('controller' => 'groups', 'action' => 'view', $user['Group']['id'])); ?>&nbsp;</td>
			<td><?php echo $user['User']['created']; ?>&nbsp;</td>
			<td><?php echo $user['User']['modified']; ?>&nbsp;</td>
		</tr>
	</table>
</div>
<div id="list_urls">
	<h3>Liste des urls de l'utilisateur</h3>
	<?php if (!empty($user['Url'])):?>
	<table>
		<tr>
			<th>Id</th>
			<th>Url de départ</th>
			<th>Url réduite</th>
			<th>Créée le :</th>
			<th>Modifiée le :</th>
			<th>Adresse ip :</th>
			<th>Actions</th>
		</tr>
	<?php
		$i = 0;
		foreach ($user['Url'] as $url):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $url['id'];?></td>
			<td><?php echo $url['longUrl'];?></td>
			<td><?php echo $url['shortUrl'];?></td>
			<td><?php echo $url['created'];?></td>
			<td><?php echo $url['modified'];?></td>
			<td><?php echo $url['adrIp'];?></td>
			<td class="actions">				
				<?php echo $this->Html->link($this->Html->image("/img/view.png", array("alt"=>"Voir")), array('controller' => 'urls', 'action' => 'view', $url['id']), array('escape' => false)); ?>
				<?php echo $this->Html->link($this->Html->image("/img/edit.png", array("alt"=>"Modifier")), array('controller' => 'urls', 'action' => 'edit', $url['id']), array('escape' => false)); ?>
				<?php echo $this->Html->link($this->Html->image("/img/delete.png", array("alt"=>"Supprimer")), array('controller' => 'urls', 'action' => 'delete', $url['id']), array('escape' => false), null, $url['id']); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
