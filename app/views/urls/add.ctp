<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
</div>
<div id="content">
	<div id="box">
		<?php 
			echo $this->Form->create('Url');
			echo $this->Form->input('longUrl', array('placeholder'=>'Collez ici le lien à raccourcir', 'id'=>'longUrl'));		
		?>			
			<img id="shadow-input" src="/img/shadow-input.png" alt="Shadow Input" />
			<img id="shadow-submit" src="/img/shadow-submit.png" alt="Shadow Submit" />
		<?php echo $this->Form->end(__('Raccourcir', array('id'=>'submit')));?>
	</div>
	<div id="text">
		<p>we like our urls short. <em>really</em> short</p>
	</div>
	<div id="shortUrl">
		<?php
			if(isset($this->data['Url']['shortUrl']) && !empty($this->data['Url']['shortUrl']))
			{				
				$shortUrl = "localhost:8888/" .$this->data['Url']['shortUrl'];
				echo "Votre Url raccourcie est : <a href=" .$shortUrl. " id=\"shortUrl\" >" .$shortUrl. "</a>";
			}
		?>
	</div>
	<?php if(@$userId != '0'): ?>
	<div id="link"><?php echo $this->Html->link('Voir mes urls réduites', array('action'=>'index')); ?></div>
	<?php endif; ?>
	<p>Pour accéder à une multitude de fonctionnalités vis-à-vis des urls que vous avez personnellement réduites, créer un compte <?php echo $this->Html->link('ici', array('controller'=>'users', 'action'=>'add')); ?></p>
</div>
<div id="footer">
	<p>ow.gs est le raccourcisseur d’URL d’Organic Web, agence de création de sites internet à Rennes.</p>
	<?php if(isset($bookmarklet) && $bookmarklet != '')
	{
		$token = $this->Session->read('Auth.User.token');
		echo "<a href=\"javascript:void(location.href='http://localhost:8888/urls/bookmarklet/$token/'+window.btoa(unescape(encodeURIComponent(unescape(location.href)))))\" title=\"Placez le bookmarklet dans vos favoris, pour utiliser ow.gs depuis n'importe quelle page.\" id=\"bookmarklet\"><img src=\"/img/bookmarket.png\" alt=\"Bookmarket\" /></a>";
	}
	?>
	
</div>