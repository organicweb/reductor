<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
	<h1>Informations sur le groupe</h1>
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
<div id="view_groups">
	<table>
		<tr>
			<th>Identifiant</th>
			<th>Nom</th>
			<th>Crée le :</th>		
			<th>Modifié le :</th>		
		</tr>
		<tr>		
			<td><?php echo $group['Group']['id']; ?>&nbsp;</td>	
			<td><?php echo $group['Group']['name']; ?>&nbsp;</td>
			<td><?php echo $group['Group']['created']; ?>&nbsp;</td> 	
			<td><?php echo $group['Group']['modified']; ?>&nbsp;</td>
		</tr>
	</table>
</div>
<div id="users_group">
	<h3>Liste des utilisateurs du groupe</h3>
	<?php if (!empty($group['User'])):?>
	<table>
		<tr>
			<th>Id</th>
			<th>Nom d'utilisateur</th>
			<th>Mot de passe</th>
			<th>Crée le :</th>
			<th>Modifié le :</th>
			<th class="actions"><?php __('Actions');?></th>
		</tr>
		<?php
			$i = 0;
			foreach ($group['User'] as $user):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
			?>
			<tr<?php echo $class;?>>
				<td><?php echo $user['id'];?></td>
				<td><?php echo $user['username'];?></td>
				<td><?php echo $user['password'];?></td>
				<td><?php echo $user['created'];?></td>
				<td><?php echo $user['modified'];?></td>
				<td class="actions">
					<?php echo $this->Html->link($this->Html->image("/img/view.png", array("alt"=>"Voir")), array('controller' => 'users', 'action' => 'view', $user['id']), array('escape' => false)); ?>
					<?php echo $this->Html->link($this->Html->image("/img/edit.png", array("alt"=>"Modifier")), array('controller' => 'users', 'action' => 'edit', $user['id']), array('escape' => false)); ?>
					<?php echo $this->Html->link($this->Html->image("/img/delete.png", array("alt"=>"Supprimer")), array('controller' => 'users', 'action' => 'delete', $user['id']), array('escape' => false), null, $user['id']); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
