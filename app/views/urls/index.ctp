<div class="urls index">
	<h2><?php __('Liste des urls réduites');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo ('identifiant');?></th>
			<th><?php echo ('Url de base');?></th>
			<th><?php echo ('Url Réduite');?></th>
			<th><?php echo ('créé le');?></th>
			<th><?php echo ('modifié le');?></th>
			<th><?php echo ('adresse Ip');?></th>
			<th><?php echo ('delete at');?></th>
			<th class="actions"><?php __('Actions');?></th>
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
		<td><?php echo $url['Url']['adrIp']; ?>&nbsp;</td>
		<td><?php echo $url['Url']['delete_at']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $url['Url']['shortUrl'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $url['Url']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $url['Url']['id']), null, sprintf(__('Etes-vous sûr de vouloir supprimer ce lien # %s?', true), $url['Url']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>	
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Url', true), array('action' => 'add')); ?></li>
	</ul>
</div>