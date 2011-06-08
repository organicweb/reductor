<div class="users index">
	<h2><?php __('Stats'); echo"( $counter[AllClicks] cliques ont été réalisés )"; ?></h2>
	<h2><?php echo"( $counter[IpDistinct] adresse(s) ip unique(s) )"; ?></h2>
	<h2><?php echo"( $counter[UrlDistinct] url(s) différentes ont été utilisées )"; ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('adrIp');?></th>
			<th><?php echo $this->Paginator->sort('url_id');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($stats as $stat):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $stat['Stat']['id']; ?>&nbsp;</td>
		<td><?php echo $stat['Stat']['adrIp']; ?>&nbsp;</td>
		<td><?php echo $stat['Stat']['url_id']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $stat['Stat']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $stat['Stat']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $stat['Stat']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $stat['Stat']['id'])); ?>
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
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Stat', true), array('action' => 'add')); ?></li>
	</ul>
</div>