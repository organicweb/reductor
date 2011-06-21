<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
	<h1>Liste des utilisateurs</h1>
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
<div id="index_users">
	<table>
	<tr>
		<th><?php echo $this->Paginator->sort('Id');?></th>
		<th><?php echo $this->Paginator->sort('Nom d\'utilisateur');?></th>
		<th><?php echo $this->Paginator->sort('Mot de passe');?></th>
		<th><?php echo $this->Paginator->sort('Groupe');?></th>
		<th><?php echo $this->Paginator->sort('Crée le :');?></th>
		<th><?php echo $this->Paginator->sort('Modifié le :');?></th>
		<th>Changer droits</th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($users as $user):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $user['User']['id']; ?>&nbsp;</td>
		<td><?php echo $user['User']['username']; ?>&nbsp;</td>
		<td><?php echo $user['User']['password']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($user['Group']['name'], array('controller' => 'groups', 'action' => 'view', $user['Group']['id'])); ?>
		</td>
		<td><?php echo $user['User']['created']; ?>&nbsp;</td>
		<td><?php echo $user['User']['modified']; ?>&nbsp;</td>
		<td><?php echo $this->Html->link(__('Grant', true), array('action'=>'userToAdmin', $user['User']['id'])); ?></td>
		<td class="actions">
			<?php echo $this->Html->link($this->Html->image("/img/view.png", array("alt"=>"Voir")), array('action' => 'view', $user['User']['id']), array('escape' => false)); ?>
			<?php echo $this->Html->link($this->Html->image("/img/edit.png", array("alt"=>"Modifier")), array('action' => 'edit', $user['User']['id']), array('escape' => false)); ?>
			<?php echo $this->Html->link($this->Html->image("/img/delete.png", array("alt"=>"Supprimer")), array('action' => 'delete', $user['User']['id']), array('escape' => false), null, $user['User']['id']); ?>			
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% sur %pages%, %current% utilisateurs affichés sur un total de %count%', true)
	));
	?></p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('Précédent', true), array(), null, array('class'=>'disabled'))." --";?>
	  	<?php echo $this->Paginator->numbers();?>
		<?php echo $this->Paginator->next(__('Suivant', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>