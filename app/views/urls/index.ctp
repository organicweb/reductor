<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
	<h1>Liste des urls réduites</h1>
</div>
<?php 
if($group_id == 1) :?>
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
<div id="index_urls">
	<table>
	<tr>
		<th>Identifiant</th>
		<th>Url de base</th>
		<th>Url Réduite</th>
		<th>créé le :</th>
		<th>modifié le :</th>
		<?php if($group_id == 1): ?><th>adresse Ip</th><?php endif; ?>
		<?php if($group_id == 1): ?><th>Supprimé le :</th><?php endif; ?>
		<th>Actions</th>
	</tr>
	<?php
	$i = 0;
	foreach ($urls as $url):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $url['Url']['id']; ?>&nbsp;</td>
		<td><?php echo $url['Url']['longUrl']; ?>&nbsp;</td>
		<td><?php echo $url['Url']['shortUrl']; ?>&nbsp;</td>
		<td><?php echo $url['Url']['created']; ?>&nbsp;</td>
		<td><?php echo $url['Url']['modified']; ?>&nbsp;</td>
		<?php if($group_id == 1): ?><td><?php echo $url['Url']['adrIp']; ?>&nbsp;</td><?php endif; ?>
		<?php if($group_id == 1): ?><td><?php echo $url['Url']['delete_at'] == '0000-00-00 00:00:00' ? '' : $url['Url']['delete_at']; ?>&nbsp;</td><?php endif; ?>
		<td class="actions">
			<?php echo $this->Html->link($this->Html->image("/img/view.png", array("alt"=>"Voir")), array('action' => 'view', $url['Url']['shortUrl']), array('escape' => false)); ?>
			<?php 
			if($group_id == 1)
			{
				echo $this->Html->link($this->Html->image("/img/edit.png", array("alt"=>"Modifier")), array('action' => 'edit', $url['Url']['id']), array('escape' => false)); 
			} ?>
			<?php echo $this->Html->link($this->Html->image("/img/delete.png", array("alt"=>"Supprimer")), array('action' => 'delete', $url['Url']['id']), array('escape' => false), null, $url['Url']['id']); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>	
	<p id="paginator">
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% sur %pages%, %current% urls affichées sur un total de %count%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('Précédent', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 	 |	<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
	</div>
	<div id="link"><?php echo $this->Html->link('Réduire une url', array('action'=>'add')); ?></div>
</div>