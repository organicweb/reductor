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
				$shortUrl = "http://ow.gs/" .$this->data['Url']['shortUrl'];
				echo "Votre Url raccourcie est : <a href=" .$shortUrl. " id=\"shortUrl\" >" .$shortUrl. "</a>";
			}
		?>
	</div>
</div>
<div id="footer">
	<p>ow.gs est le raccourcisseur d’URL d’Organic Web, agence de création de sites internet à Rennes.</p>
	<a href="/" alt="bookmarket"><img src="/img/bookmarket.png" alt="Bookmarket" /></a>
</div>

