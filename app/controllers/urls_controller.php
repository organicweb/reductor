<?php

class UrlsController extends AppController 
{

	var $name = 'Urls';
	var $components = array('RequestHandler');
	var $paginate = array('limit'=> 5);
	
	function beforeFilter() 
	{
	    parent::beforeFilter(); 
	    $this->Auth->allowedActions = array('add', 'get_url', 'bookmarklet');
	}
		
	function index() 
	{		
		//Id de l'utilisateur
		$id = $this->Session->read('Auth.User.id');
				
		$group_id = $this->requestAction('users/group_id/'.$id);
													
		if($group_id==1)
		{
			$this->set('urls', $this->paginate());
		}
		else
		{
		    $this->paginate = array(
		        'conditions' => array('Url.user_id'=>$id, 'delete_at'=>'0000-00-00 00:00:00'),
		        'limit' => 5
		    );
			$urls = $this->paginate('Url');
		    $this->set(compact('urls'));
		}
		
		$this->set('group_id', $group_id);									
	}
		
	function get_url($id = null)
	{
		//retourne l'id de l'url appelée
		$url_id = $this->Url->find('all', array(
			'fields'=>array('id'),
			'conditions'=>array('shortUrl'=>$id)
			));
		
		//Si la requête précédente retourne un résultat valide		
		if(@$url_id[0]['Url']['id'] && !is_null($url_id[0]['Url']['id']))
		{
			//simplification d'écriture
			$url_id = $url_id[0]['Url']['id'];

			//retourne les infos de l'url appelée						
			$shortUrls = $this->Url->find('all', array(
				'fields'=>array('shortUrl', 'longUrl', 'id', 'adrIp'),
				'conditions'=> array('shortUrl'=>$id),
			));
        	
			//Enregistrement de l'id du lien réduit appelé
			$this->data['Stat']['url_id'] = $shortUrls[0]['Url']['id'];
								
			//Enregistrement de l'@ip de l'utilisateur appelant
			$this->data['Stat']['adrIp'] = $_SERVER['REMOTE_ADDR'];
			
			//Enregistrement de l'adresse du refereur si elle existe
			if(isset($_SERVER['HTTP_REFERER']))
			{
				$this->data['Stat']['referer'] = $_SERVER['HTTP_REFERER'];
			}
			else
			{
				$this->data['Stat']['referer'] = 'direct';
			}
			
			//Enregistrement dans la table stats
			$this->Url->Stat->create();
			$this->Url->Stat->save($this->data['Stat']);
			
			//envoi des données à la vue
			$this->set('url', $this->Url->read(null, $url_id));
			
			if(@$shortUrls[0]['Url']['shortUrl'] && !is_null($shortUrls[0]['Url']['shortUrl']))
			{
				preg_match('/^[a-zA-Z]+:\/\//', $shortUrls[0]['Url']['longUrl'], $matches);
	
				if(@$matches[0] == 'http://')
				{
					$this->redirect($shortUrls[0]['Url']['longUrl']);		
				}
				else
				{
					$this->redirect('http://' .$shortUrls[0]['Url']['longUrl']);
				}
			}
		}	
		else
		{
			$this->flash('L\'identifiant que vous avez entré n\'est pas dans notre base de données', array('controller'=>'urls', 'action'=>'add'));
		}
		
	}

	function view($id = null) 
	{
		if (!$id) 
		{
			//affichage d'un message d'erreur si l'url n'est pas valide
			$this->Session->setFlash(__('Invalid url', true));
			$this->redirect(array('action' => 'index'));
		}
		
		//retourne l'id de l'url appelée
		$url_id = $this->Url->find('all', array(
			'fields'=>array('id'),
			'conditions'=>array('shortUrl'=>$id)
			));
		
		$group_id = $this->Session->read('Auth.User.group_id');
		
		if(isset($group_id) && $group_id != '')
		{
			$this->set('group_id', $group_id);
		}
		
		//Si la requête précédente retourne un résultat valide		
		if(@$url_id[0]['Url']['id'] && !is_null($url_id[0]['Url']['id']))
		{					
			$url_id = $this->Url->find('all', array(
				'fields'=>array('id'),
				'conditions'=>array('shortUrl'=>$id)
				));
			
			//Simplification d'écriture
			$url_id = $url_id[0]['Url']['id'];
					
			//compte le nombre de clicks pour l'url affichée
			$counter['NbAppel'] = $this->Url->Stat->find('count',array(
				'fields'=>'url_id',
				'conditions'=>array('url_id'=>$url_id)
				));
				
			//Retourne les adresses ip des users ayant appelés l'url réduite
			$counter['IpUser'] = $this->Url->Stat->find('count', array(
				'fields'=>'DISTINCT Stat.adrIp',
				'conditions'=>array('url_id'=>$url_id)
				));
        	
			//Retourne le nombre d'appel de l'url réduite par user
			$counter['NbAppelByIp'] = $this->Url->Stat->query("SELECT COUNT(url_id), adrIp
														FROM stats
														WHERE url_id = $url_id
														GROUP BY adrIp;");
														
			$counter['regular'] = 0;
			$counter['new'] = 0;	
			
			//Permet de déterminer le type de visiteur
			foreach($counter['NbAppelByIp'] as $count)
			{
				if($count[0]['COUNT(url_id)'] > 1)
				{
					$counter['regular'] += $count[0]['COUNT(url_id)'];
				}
				else
				{
					$counter['new'] += $count[0]['COUNT(url_id)'];
				}
			}
			
			//Date actuel
			$currentDate = date("Y-m-d H:i:s", time());
        	
			//Calcul des dates antérieures
			$aMonthAgo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")-1, date("d"), date("Y")));		
			$aWeekAgo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")-7, date("Y")));	
			$aDayAgo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")-1, date("Y")));
			
			//intégration des valeurs dans un tableau
			$date['currentDate'] = $currentDate;
			$date['aMonthAgo'] = $aMonthAgo;
			$date['aWeekAgo'] = $aWeekAgo;
			$date['aDayAgo'] = $aDayAgo;
							
			//Permet de sélectionner les tranches horaires sur 24H
			for($i=0 ; $i<25 ; $i++)
			{
				$aDayAgoByHour['date'][] = date("Y-m-d H:i:s", mktime(date("H")+$i, date("i"), date("s"), date("m"), date("d")-1, date("Y")));
				$aDayAgoByHour['hour'][] = date("H", mktime(date("H")+$i, date("i"), date("s"), date("m"), date("d")-1));
			}
			
			//Permet de sélectionner les tranches journalières sur une semaine
			for($i=0 ; $i<8 ; $i++)
			{
				$aWeekAgoByHour['date'][] = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d")-7+$i, date("Y")));
				$aWeekAgoByHour['day'][] = date("d", mktime(0, 0, 0, 0, date("d")-7+$i));
			}
			
			//Permet de sélectionner les tranches journalières sur un mois
			for($i=0 ; $i<31 ; $i++)
			{
				$aMonthAgoByHour['date'][] = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")-1, date("d")+$i, date("Y")));
				$aMonthAgoByHour['day'][] = date("d", mktime(0, 0, 0, date("m")-1, date('d')+$i));
			}
					
			//Permet de compter le nombre de clicks par heures sur 24H
			for($i=0 ; $i<25 ; $i++)
			{
				$j = $i + 1;
				if($j != 25)
				{			
					$counter['nbClicksByDay'][] = $this->Url->Stat->find('count', array(
													'fields'=>'Stat.adrIp',
													'conditions'=>array('url_id'=>$url_id, 'Stat.created BETWEEN ? AND ?'=>array($aDayAgoByHour['date'][$i], $aDayAgoByHour['date'][$j]))
													));
				}
			}
			
			
			//Permet de compter le nombre de clicks par jours sur une semaine
			for($i=0 ; $i<8 ; $i++)
			{
				$j = $i + 1;
				if($j != 8)
				{			
					$counter['nbClicksByWeek'][] = $this->Url->Stat->find('count', array(
													'fields'=>'Stat.adrIp',
													'conditions'=>array('url_id'=>$url_id, 'Stat.created BETWEEN ? AND ?'=>array($aWeekAgoByHour['date'][$i], $aWeekAgoByHour['date'][$j]))
													));
				}
			}
			
			//Permet de compter le nombre de clicks par jours sur un mois
			for($i=0 ; $i<31 ; $i++)
			{
				$j = $i + 1;
				if($j != 31)
				{
					$counter['nbClicksByMonth'][] = $this->Url->Stat->find('count', array(
													'fields'=>'Stat.adrIp',
													'conditions'=>array('url_id'=>$url_id, 'Stat.created BETWEEN ? AND ?'=>array($aMonthAgoByHour['date'][$i], $aMonthAgoByHour['date'][$j]))
													));
				}
			}
			
			//Génération du nom des axes pour le graphique des semaines
			$week = "";
			for($i=0 ; $i<8 ; $i++)
			{
				$j = $i + 1;
				$week .= "[$j, " .$aWeekAgoByHour['day'][$i]. "]";
				if($i <= 6)
				{
					$week .= ", ";
				}
			}
			$date['DatesOfTheWeek'] = "[$week]";
			
			//Génération du tableau de valeur à intégrer au graphique
			$WeekClicks = "";
			for($i=0 ; $i<7 ; $i++)
			{
				$j = $i + 1;
				$WeekClicks .= "[$j, ".$counter['nbClicksByWeek'][$i]."]";
				if($j <= 6)
				{
					$WeekClicks .= ", ";
				}
			}
			$counter['ClicksOfTheWeek'] = "[$WeekClicks]";
			
			//Génération du nom des axes pour le graphique des dernières 24H
			$day = "";
			for($i=0 ; $i<25 ; $i++)
			{
				$j = $i + 1;
				$day .= "[$j, " .$aDayAgoByHour['hour'][$i]. "]";
				if($i < 24)
				{
					$day .= ", ";
				}
			}
			$date['DatesOfTheDay'] = "[$day]";
			
			//Génération du tableau de valeur à intégrer au graphique
			$DayClicks = "";
			for($i=0 ; $i<24 ; $i++)
			{
				$j = $i + 1;
				$DayClicks .= "[$j, ".$counter['nbClicksByDay'][$i]."]";
				if($j <= 23)
				{
					$DayClicks .= ", ";
				}
			}
			$counter['ClicksOfTheDay'] = "[$DayClicks]";
			
			//Génération du nom des axes pour le graphique du mois passé
			$month = "";
			for($i=0 ; $i<31 ; $i++)
			{
				$j = $i + 1;
				$month .= "[$j, " .$aMonthAgoByHour['day'][$i]. "]";
				if($j < 31)
				{
					$month .= ", ";
				}
			}
			$date['DatesOfTheMonth'] = "[$month]";
			
			//Génération du tableau de valeurs à intégrer au graphique
			$MonthClicks = "";
			for($i=0 ; $i<30 ; $i++)
			{
				$j = $i + 1;
				$MonthClicks .= "[$j, ".$counter['nbClicksByMonth'][$i]."]";
				if($j < 30)
				{
					$MonthClicks .= ", ";
				}
			}
			$counter['ClicksOfTheMonth'] = "[$MonthClicks]";				
						
			//Retourne les infos à la vue					
			$this->set('counter', $counter);
			$this->set('date', $date);
			$this->set('url', $this->Url->read(null, $url_id));
		}

	}

	function bookmarklet($token, $url)
	{			
		if(isset($token) && $token != '' && isset($url) && $url != '')
		{
			$listTokens = $this->Url->query("SELECT token 
											 FROM users ;");
		    
			for($i=0 ; $i<count($listTokens) ; $i++)
			{
				if($listTokens[$i]['users']['token'] == $token)
				{
					$this->data['Url']['longUrl'] = base64_decode($url);
					
					$val = self::idGenerator();

					//Création d'un espace réservé à l'enregistrement prochain
					$this->Url->create();

					//récupération des infos sur l'utilisateur	
					$this->data['Url']['adrIp'] = $_SERVER['REMOTE_ADDR']; //Son adresse ip
					
					//recherche de l'id de l'utilisateur depuis son token
					$userOfThisToken = $this->Url->query("SELECT id 
													 	  FROM users 
														  WHERE `token` = '$token';");
														
					$this->data['Url']['user_id'] = $userOfThisToken[0]['users']['id']; //Son identifiant d'utilisateur
				
					//Simplification d'écriture
					$userId = $this->data['Url']['user_id'];

					//envoi de l'id de l'utilisateur à la vue
					$this->set('userId', $userId);
					
					//attribution dans le champ shortUrl de la table urls la valeur $val	
					if($val != '')
					{
						$this->data['Url']['shortUrl'] = $val;			
						$this->Url->save($this->data);
						$this->redirect(array('controller'=>'users', 'action'=>'login'));
					}
				}
			}
		}		
	}

	function idGenerator()
	{
		//ouverture du fichier de blacklistage
		$handle = fopen(CONFIGS.DS.'blacklist.csv', 'r');
		
		//Récupération des valeurs du fichier csv dans un tableau
		$blacklist = fgetcsv($handle,0,';');
		
		//Parcours du tableau de blacklistage
		for($i=0 ; $i<count($blacklist) ; $i++)
		{
			//Recherche du dernier id entré
			$lastDatas = $this->Url->find('first', array(
				'fields'=>array('id', 'shortUrl'),
				'order'=>'id DESC'
				));
			
			//Simplification d'écriture				
			$lastId = $lastDatas['Url']['id'];
			
			//Génération de l'identifiant de l'url réduite
			$val = self::yourls_int2string($lastId);
			
			//Si l'identifiant généré est dans la blacklist créer un enregistrement vide
			if($blacklist[$i] == $val)
			{
				$this->Url->create();
				$this->Url->save($this->data);					
			}
		}	
		
		//Suppression de tous les enregistrements vides (générés lorsque l'identifiant est blacklisté)
		$this->Url->deleteAll(array('shortUrl'=>''));
		
		return $val;
	}

	function add() 
	{
		$bookmarklet = $this->Session->read('Auth.User.id');
		if(isset($bookmarklet) && $bookmarklet != '')
		{		
			$this->set('bookmarklet', $bookmarklet);
		}
		if (!empty($this->data)) 
		{			
			
			$val = self::idGenerator();
			
			//Création d'un espace réservé à l'enregistrement prochain
			$this->Url->create();
						
			//récupération des infos sur l'utilisateur	
			$this->data['Url']['adrIp'] = $_SERVER['REMOTE_ADDR']; //Son adresse ip
			$this->data['Url']['user_id'] = $this->Session->read('Auth.User.id'); //Son identifiant d'utilisateur
			
			//Simplification d'écriture
			$userId = $this->data['Url']['user_id'];
			
			//Si l'utilisateur n'est pas connecté user_id = 0
			if($userId == '')
			{			
				$this->data['Url']['user_id'] = '0';
			}
			
			//envoi de l'id de l'utilisateur à la vue
			$this->set('userId', $userId);
			
			//attribution dans le champ shortUrl de la table urls la valeur $val	
			if($val != '')
			{
				$this->data['Url']['shortUrl'] = $val;			
			}								
			if ($this->Url->save($this->data)) 
			{				
				$this->Session->setFlash(__('L\'url à été sauvegardée avec succès !', true));
			} 
			else 
			{
				$this->Session->setFlash(__('L\'url n\'a pas pu être sauvegardée. Veuillez réessayer.', true));
			}	
		}
	}

	function edit($id = null) 
	{
		if (!$id && empty($this->data)) 
		{
			$this->Session->setFlash(__('Invalid url', true));
			$this->redirect(array('action' => 'index'));
		}
		//Si une modification a été effectuée sauvegarder les données et afficher un message
		if (!empty($this->data)) {
			if ($this->Url->save($this->data)) {
				$this->Session->setFlash(__('L\'url à été sauvegardée avec succès !', true));
				$this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('L\'url n\'a pas pu être sauvegardée. Veuillez réessayer.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Url->read(null, $id);
		}
	}

	function delete($id = null) 
	{
		if (!$id) 
		{
			$this->Session->setFlash(__('Identifiant d\'url incorrecte', true));
			$this->redirect(array('action'=>'index'));
		}	
		
		$group_id = $this->Session->read('Auth.User.group_id');
		if($group_id != 1)
		{							
			//intégration de l'heure actuelle dans le champ delete_at de l'url sur laquelle la demande de suppression à été effectuée
			$this->Url->data['Url']['delete_at'] = date("Y-m-d H:i:s", time());
		
			//sauvegarde du changement de valeur de delete_at
			$this->Url->save($this->data['Url']['delete_at']);
		}
		else
		{
			$this->Url->deleteAll(array('id'=>$id));
			$this->redirect(array('action'=>'index'));
		}
		
		//affichage du message d'information 'url deleted'	
		$this->Session->setFlash(__('Url supprimée', true));
		//$this->redirect(array('action'=>'index'));
		
	}

	function yourls_dec2base($iNum, $iBase, $iScale=0) 
	{ // cope with base 2..62
		$sChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$sResult = ''; // Store the result
		$iBase = 32;
		// special case for Base64 encoding
		if ($iBase == 64)
		 $sChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

		$sNum = is_integer($iNum) ? "$iNum" : (string)$iNum;
		
		$iBase = self::yourls_intval($iBase); // incase it is a string or some weird decimal

		// Check to see if we are an integer or real number
		if (strpos($sNum, '.') !== FALSE) 
		{
			list ($sNum, $sReal) = explode('.', $sNum, 2);
			$sReal = '0.' . $sReal;
		} 
		else
			$sReal = '0';

		while (bccomp($sNum, 0, $iScale) != 0) 
		{ // still data to process
			$sRem = bcmod($sNum, $iBase); // calc the remainder
			$sNum = bcdiv( bcsub($sNum, $sRem, $iScale), $iBase, $iScale );
			$sResult = $sChars[$sRem] . $sResult;
			
		}
		if ($sReal != '0') 
		{
			$sResult .= '.';
			$fraciScale = $iScale;
			while($fraciScale-- && bccomp($sReal, 0, $iScale) != 0) 
			{ // still data to process
				$sReal = bcmul($sReal, $iBase, $iScale); // multiple the float part with the base
				$sFrac = 0;
				if (bccomp($sReal ,1, $iScale) > -1)
					list($sFrac, $dummy) = explode('.', $sReal, 2); // get the yourls_intval
				$sResult .= $sChars[$sFrac];
				$sReal = bcsub($sReal, $sFrac, $iScale);
			}
		}
		return $sResult;
	}


	function yourls_base2dec($sNum, $iBase=0, $iScale=0) 
	{
		$sChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$sResult = '';

		$iBase = self::yourls_intval($iBase); // incase it is a string or some weird decimal

		// special case for Base64 encoding
		if ($iBase == 64)
		 $sChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

		// clean up the input string if it uses particular input formats
		switch ($iBase) 
		{
			case 16: // remove 0x from start of string
				if (strtolower(substr($sNum, 0, 2)) == '0x') $sNum = substr($sNum, 2);
				break;
			case 8: // remove the 0 from the start if it exists - not really required
				if (strpos($sNum, '0')===0) $sNum = substr($sNum, 1);
				break;
			case 2: // remove an 0b from the start if it exists
				if (strtolower(substr($sNum, 0, 2)) == '0b') $sNum = substr($sNum, 2);
				break;
			case 64: // remove padding chars: =
				$sNum = str_replace('=', '', $sNum);
				break;
			default: // Look for numbers in the format base#number,
							 // if so split it up and use the base from it
				if (strpos($sNum, '#') !== false) 
				{
					list ($sBase, $sNum) = explode('#', $sNum, 2);
					$iBase = self::yourls_intval($sBase);	// take the new base
				}
				if ($iBase == 0) 
				{
					print("yourls_base2dec called without a base value and not in base#number format");
					return '';
				}
				break;
		}

		// Convert string to upper case since base36 or less is case insensitive
		if ($iBase < 37) $sNum = strtoupper($sNum);

		// Check to see if we are an integer or real number
		if (strpos($sNum, '.') !== FALSE) 
		{
			list ($sNum, $sReal) = explode('.', $sNum, 2);
			$sReal = '0.' . $sReal;
		} 
		else
			$sReal = '0';


		// By now we know we have a correct base and number
		$iLen = strlen($sNum);

		// Now loop through each digit in the number
		for ($i=$iLen-1; $i>=0; $i--) {
			$sChar = $sNum[$i]; // extract the last char from the number
			$iValue = strpos($sChars, $sChar); // get the decimal value
			if ($iValue > $iBase) {
				print("yourls_base2dec: $sNum is not a valid base $iBase number");
				return '';
			}
			// Now convert the value+position to decimal
			$sResult = bcadd($sResult, bcmul( $iValue, bcpow($iBase, ($iLen-$i-1))) );
		}

		// Now append the real part
		if (strcmp($sReal, '0') != 0) 
		{
			$sReal = substr($sReal, 2); // Chop off the '0.' characters
			$iLen = strlen($sReal);
			for ($i=0; $i<$iLen; $i++) 
			{
				$sChar = $sReal[$i]; // extract the first, second, third, etc char
				$iValue = strpos($sChars, $sChar); // get the decimal value
				if ($iValue > $iBase) 
				{
					print("yourls_base2dec: $sNum is not a valid base $iBase number");
					return '';
				}
				$sResult = bcadd($sResult, bcdiv($iValue, bcpow($iBase, ($i+1)), $iScale), $iScale);
			}
		}

		return $sResult;
	}

	function yourls_base2base($iNum, $iBase, $oBase, $iScale=0) 
	{
		if (!function_exists('bccomp'))
			return base_convert($iNum, $iBase, $oBase);

		if ($iBase != 10) $oNum = self::yourls_base2dec($iNum, $iBase, $iScale);
		else $oNum = $iNum;
			$oNum = self::yourls_dec2base($oNum, $oBase, $iScale);
		return $oNum;
	}
	function yourls_int2string( $id ) {
		$str = self::yourls_base2base(trim(strval($id)), 10, 'YOURLS_URL_CONVERT');
		if (YOURLS_URL_CONVERT <= 37)
			$str = strtolower($str);
		return $str;
	}

	// function to convert a string (3jk) to an integer (1337)
	function yourls_string2int( $str ) {
		if (YOURLS_URL_CONVERT <= 37)
			$str = strtolower($str);
		return self::yourls_base2base(trim($str), YOURLS_URL_CONVERT, 10);
	}

	// Make sure a link keyword (ie "1fv" as in "site.com/1fv") is valid.
	function yourls_sanitize_string($in) {
		if (YOURLS_URL_CONVERT <= 37)
			$in = strtolower($in);
		return substr(preg_replace('/[^a-zA-Z0-9]/', '', $in), 0, 199);
	}

	// Alias function. I was always getting it wrong.
	function yourls_sanitize_keyword( $keyword ) {
		return self::yourls_sanitize_string( $keyword );
	}

	// Is an URL a short URL?
	function yourls_is_shorturl( $shorturl ) {
		// TODO: make sure this function evolves with the feature set.
		// A short URL might be, in the future:
		// - http://site.com/abc
		// - http://site.com/abc-bleh
		// Could allow site.com/abc+ and site.com/abc+all

		$keyword = preg_replace( '!^'.YOURLS_SITE.'/!', '', $shorturl ); // accept either 'http://ozh.in/abc' or 'abc'
		if( $keyword && $keyword == self::yourls_sanitize_string( $keyword ) && self::yourls_keyword_is_taken( $keyword ) ) {
			return true;
		} else {
			return false;
		}
	}

	// A few sanity checks on the URL
	function yourls_sanitize_url($url) {
		// make sure there's only one 'http://' at the beginning (prevents pasting a URL right after the default 'http://')
		$url = str_replace('http://http://', 'http://', $url);

		// make sure there's a protocol, add http:// if not
		if ( !preg_match('!^([a-zA-Z]+://)!', $url ) )
			$url = 'http://'.$url;

		$url = self::yourls_clean_url($url);

		return substr( $url, 0, 1999 );
	}

	// Function to filter all invalid characters from a URL. Stolen from WP's clean_url()
	function yourls_clean_url( $url ) {
		$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'"()\\x80-\\xff]|i', '', $url );
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = self::yourls_deep_replace($strip, $url);
		$url = str_replace(';//', '://', $url);
		$url = str_replace('&amp;', '&', $url); // Revert & not to break query strings

		return $url;
	}

	// Perform a replacement while a string is found, eg $subject = '%0%0%0DDD', $search ='%0D' -> $result =''
	// Stolen from WP's _deep_replace
	function yourls_deep_replace($search, $subject){
		$found = true;
		while($found) {
			$found = false;
			foreach( (array) $search as $val ) {
				while(strpos($subject, $val) !== false) {
					$found = true;
					$subject = str_replace($val, '', $subject);
				}
			}
		}

		return $subject;
	}

	// Make sure an integer is a valid integer (PHP's intval() limits to too small numbers)
	// TODO FIXME FFS: unused ?
	function yourls_sanitize_int($in) {
		return ( substr(preg_replace('/[^0-9]/', '', strval($in) ), 0, 20) );
	}

	// Make sure a integer is safe
	// Note: this is not checking for integers, since integers on 32bits system are way too limited
	// TODO: find a way to validate as integer
	function yourls_intval($in) {
		return self::yourls_escape($in);
	}

	// Escape a string
	function yourls_escape( $in ) {
		return mysql_real_escape_string($in);
	}

	// Check to see if a given keyword is reserved (ie reserved URL or an existing page)
	// Returns bool
	function yourls_keyword_is_reserved( $keyword ) {
		global $yourls_reserved_URL;
		$keyword = self::_sanitize_keyword( $keyword );

		if ( in_array( $keyword, $yourls_reserved_URL)
			or file_exists(dirname(dirname(__FILE__))."/pages/$keyword.php")
			or is_dir(dirname(dirname(__FILE__))."/$keyword")
		)
			return true;

		return false;
	}

	// Function: Get IP Address. Returns a DB safe string.
	function yourls_get_IP() {
		if( !empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if(!empty($_SERVER['HTTP_VIA '])) {
				$ip = $_SERVER['HTTP_VIA '];
			}
		}

		return self::yourls_sanitize_ip( $ip );
	}

	// Sanitize an IP address
	function yourls_sanitize_ip( $ip ) {
		return preg_replace( '/[^0-9a-fA-F:., ]/', '', $ip );
	}

	// Make sure a date is m(m)/d(d)/yyyy, return false otherwise
	function yourls_sanitize_date( $date ) {
		if( !preg_match( '!^\d{1,2}/\d{1,2}/\d{4}$!' , $date ) ) {
			return false;
		}
		return $date;
	}

	// Sanitize a date for SQL search. Return false if malformed input.
	function yourls_sanitize_date_for_sql( $date ) {
		if( !yourls_sanitize_date( $date ) )
			return false;
		return date('Y-m-d', strtotime( $date ) );
	}

	// Add the "Edit" row
	function yourls_table_edit_row( $keyword ) {
		global $ydb;

		$table = YOURLS_DB_TABLE_URL;
		$keyword = self::yourls_sanitize_string( $keyword );
		$id = self::yourls_string2int( $keyword ); // used as HTML #id
		$url = $ydb->get_row("SELECT `url` FROM `$table` WHERE `keyword` = '$keyword';");
		$safe_url = stripslashes( $url->url );
		$www = YOURLS_SITE;

		if( $url ) 
		{
			$return = '<tr id="edit-$id" class="edit-row"><td colspan="5"><strong>Original URL</strong>:<input type="text" id="edit-url-$id" 					name="edit-url-$id" value="$safe_url" class="text" size="70" /> <strong>Short URL</strong>: $www/<input type="text" id="edit-keyword-$id" name="edit-keyword-$id" value="$keyword" class="text" size="10" /></td><td colspan="1"><input type="button" id="edit-submit-$id" name="edit-submit-$id" value="Save" title="Save new values" class="button" onclick="edit_save(' .$id. ');" />&nbsp;<input type="button" id="edit-close-$id" name="edit-close-$id" value="X" title="Cancel editing" class="button" onclick="hide_edit(' .$id. ');" /><input type="hidden" id="old_keyword_$id" value="$keyword"/></td></tr>';
		} 
		else 
		{
			$return = '<tr><td colspan="6">Error, URL not found</td></tr>';
		}

		return $return;
	}

	// Add a link row
	function yourls_table_add_row( $keyword, $url, $ip, $clicks, $timestamp ) {
		$keyword = self::yourls_sanitize_string( $keyword );
		$id = self::yourls_string2int( $keyword ); // used as HTML #id
		$date = date( 'M d, Y H:i', $timestamp+( YOURLS_HOURS_OFFSET * 3600) );
		$clicks = number_format($clicks, 0, '', '');
		$shorturl = YOURLS_SITE.'/'.$keyword;
		$display_url = htmlentities( self::yourls_trim_long_string( $url ) );
		$statlink = $shorturl.'+';
		$url = htmlentities( $url );

		return '<tr id="id-$id"><td id="keyword-$id"><a href="$shorturl">$keyword</a></td><td id="url-$id"><a href="$url" title="$url">$display_url</a></td><td id="timestamp-$id">$date</td><td id="ip-$id">$ip</td><td id="clicks-$id">$clicks</td><td class="actions" id="actions-$id"><a href="$statlink" id="statlink-$id" class="button button_stats">&nbsp;&nbsp;&nbsp;</a>&nbsp;<input type="button" id="edit-button-$id" name="edit-button" value="" title="Edit" class="button button_edit" onclick="edit(' .$id. ');" />&nbsp;<input type="button" id="delete-button-$id" name="delete-button" value="" title="Delete" class="button button_delete" onclick="remove(' .$id. ');" /><input type="hidden" id="keyword_$id" value="$keyword"/></td></tr>';
	}

	// Get next id a new link will have if no custom keyword provided
	function yourls_get_next_decimal() {
		return (int)self::yourls_get_option( 'next_id' );
	}

	// Update id for next link with no custom keyword
	function yourls_update_next_decimal( $int = '' ) {
		$int = ( $int == '' ) ? yourls_get_next_decimal() + 1 : (int)$int ;
		return self::yourls_update_option( 'next_id', $int );
	}

	// Delete a link in the DB
	function yourls_delete_link_by_keyword( $keyword ) {
		global $ydb;

		$table = YOURLS_DB_TABLE_URL;
		$keyword = self::yourls_sanitize_string( $keyword );
		return $ydb->query("DELETE FROM `$table` WHERE `keyword` = '$keyword';");
	}

	// SQL query to insert a new link in the DB. Needs sanitized data. Returns boolean for success or failure of the inserting
	function yourls_insert_link_in_db($url, $keyword) {
		global $ydb;

		$table = YOURLS_DB_TABLE_URL;
		$timestamp = date('Y-m-d H:i:s');
		$ip = self::yourls_get_IP();
		$insert = $ydb->query("INSERT INTO `$table` VALUES('$keyword', '$url', '$timestamp', '$ip', 0);");

		return (bool)$insert;
	}

	// Add a new link in the DB, either with custom keyword, or find one
	function yourls_add_new_link( $url, $keyword = '' ) {
		global $ydb;

		if ( !$url || $url == 'http://' || $url == 'https://' ) {
			$return['status'] = 'fail';
			$return['code'] = 'error:nourl';
			$return['message'] = 'Missing URL input';
			$return['errorCode'] = '400';
			return $return;
		}

		// Prevent DB flood
		$ip = yourls_get_IP();
		self::yourls_check_IP_flood( $ip );

		// Prevent internal redirection loops: cannot shorten a shortened URL
		$url = self::yourls_escape( self::yourls_sanitize_url($url) );
		if( preg_match( '!^'.YOURLS_SITE.'/!', $url ) ) {
			if( self::yourls_is_shorturl( $url ) ) {
				$return['status'] = 'fail';
				$return['code'] = 'error:noloop';
				$return['message'] = 'URL is a short URL';
				$return['errorCode'] = '400';
				return $return;
			}
		}

		$table = YOURLS_DB_TABLE_URL;
		$strip_url = stripslashes($url);
		$url_exists = $ydb->get_row("SELECT keyword,url FROM `$table` WHERE `url` = '".$strip_url."';");
		$return = array();

		// New URL : store it -- or: URL exists, but duplicates allowed
		if( !$url_exists || self::yourls_allow_duplicate_longurls() ) {

			// Custom keyword provided
			if ( $keyword ) {
				$keyword = self::yourls_escape( yourls_sanitize_string($keyword) );
				if ( !self::yourls_keyword_is_free($keyword) ) {
					// This shorturl either reserved or taken already
					$return['status'] = 'fail';
					$return['code'] = 'error:keyword';
					$return['message'] = 'Short URL '.$keyword.' already exists in database or is reserved';
				} else {
					// all clear, store !
					self::yourls_insert_link_in_db($url, $keyword);
					$return['url'] = array('keyword' => $keyword, 'url' => $strip_url, 'date' => date('Y-m-d H:i:s'), 'ip' => $ip );
					$return['status'] = 'success';
					$return['message'] = $strip_url.' added to database';
					$return['html'] = self::yourls_table_add_row( $keyword, $url, $ip, 0, time() );
					$return['shorturl'] = YOURLS_SITE .'/'. $keyword;
				}

			// Create random keyword	
			} else {
				$timestamp = date('Y-m-d H:i:s');
				$id = self::yourls_get_next_decimal();
				$ok = false;
				do {
					$keyword = self::yourls_int2string( $id );
					$free = self::yourls_keyword_is_free($keyword);
					$add_url = @self::yourls_insert_link_in_db($url, $keyword);
					$ok = ($free && $add_url);
					if ( $ok === false && $add_url === 1 ) {
						// we stored something, but shouldn't have (ie reserved id)
						$delete = yourls_delete_link_by_keyword( $keyword );
						$return['extra_info'] .= '(deleted '.$keyword.')';
					} else {
						// everything ok, populate needed vars
						$return['url'] = array('keyword' => $keyword, 'url' => $strip_url, 'date' => $timestamp, 'ip' => $ip );
						$return['status'] = 'success';
						$return['message'] = $strip_url.' added to database';
						$return['html'] = self::yourls_table_add_row( $keyword, $url, $ip, 0, time() );
						$return['shorturl'] = YOURLS_SITE .'/'. $keyword;
					}
					$id++;
				} while (!$ok);
				@self::yourls_update_next_decimal($id);
			}
		} else {
			// URL was already stored
			$return['status'] = 'fail';
			$return['code'] = 'error:url';
			$return['message'] = $strip_url.' already exists in database';
			$return['shorturl'] = YOURLS_SITE .'/'. $url_exists->keyword;
		}

		$return['statusCode'] = 200; // regardless of result, this is still a valid request
		return $return;
	}


	// Edit a link
	function yourls_edit_link($url, $keyword, $newkeyword='') {
		global $ydb;

		$table = YOURLS_DB_TABLE_URL;
		$url = self::yourls_escape(yourls_sanitize_url($url));
		$keyword = self::yourls_sanitize_string( $keyword );
		$newkeyword = self::yourls_sanitize_string( $newkeyword );
		$strip_url = stripslashes($url);
		$old_url = $ydb->get_var("SELECT `url` FROM `$table` WHERE `keyword` = '$keyword';");
		$old_id = $id = self::yourls_string2int( $keyword );
		$new_id = ( $newkeyword == '' ? $old_id : self::yourls_string2int( $newkeyword ) );

		// Check if new URL is not here already
		if ($old_url != $url) {
			$new_url_already_there = intval($ydb->get_var("SELECT COUNT(keyword) FROM `$table` WHERE `url` = '$strip_url';"));
		} else {
			$new_url_already_there = false;
		}

		// Check if the new keyword is not here already
		if ( $newkeyword != $keyword ) {
			$keyword_is_ok = self::yourls_keyword_is_free( $newkeyword );
		} else {
			$keyword_is_ok = true;
		}

		// All clear, update
		if ( ( !$new_url_already_there || self::yourls_allow_duplicate_longurls() ) && $keyword_is_ok ) {
				$update_url = $ydb->query("UPDATE `$table` SET `url` = '$url', `keyword` = '$newkeyword' WHERE `keyword` = '$keyword';");
			if( $update_url ) {
				$return['url'] = array( 'keyword' => $newkeyword, 'shorturl' => YOURLS_SITE.'/'.$newkeyword, 'url' => $strip_url, 'display_url' => self::yourls_trim_long_string( $strip_url ), 'new_id' => $new_id );
				$return['status'] = 'success';
				$return['message'] = 'Link updated in database';
			} else {
				$return['status'] = 'fail';
				$return['message'] = 'Error updating '.$strip_url.' (Short URL: '.$keyword.') to database';
			}

		// Nope
		} else {
			$return['status'] = 'fail';
			$return['message'] = 'URL or keyword already exists in database';
		}

		return $return;
	}


	// Check if keyword id is free (ie not already taken, and not reserved). Return bool.
	function yourls_keyword_is_free( $keyword ) {
		if ( self::yourls_keyword_is_reserved( $keyword ) or self::yourls_keyword_is_taken( $keyword ) )
			return false;

		return true;
	}

	// Check if a keyword is taken (ie there is already a short URL with this id). Return bool.		
	function yourls_keyword_is_taken( $keyword ) {
		global $ydb;
		$keyword = self::yourls_sanitize_keyword( $keyword );

		$table = YOURLS_DB_TABLE_URL;
		$already_exists = $ydb->get_var("SELECT COUNT(`keyword`) FROM `$table` WHERE `keyword` = '$keyword';");
		if ( $already_exists )
			return true;

		return false;
	}


	// Display a page
	function yourls_page( $page ) {
		$include = dirname(dirname(__FILE__))."/pages/$page.php";
		if (!file_exists($include)) {
			self::yourls_die("Page '$page' not found", 'Not found', 404);
		}
		include($include);
		die();	
	}

	// Connect to DB
	function yourls_db_connect() {
		global $ydb;

		if (!defined('YOURLS_DB_USER')
			or !defined('YOURLS_DB_PASS')
			or !defined('YOURLS_DB_NAME')
			or !defined('YOURLS_DB_HOST')
			or !class_exists('ezSQL_mysql')
		) yourls_die ('DB config missigin, or could not find DB class', 'Fatal error', 503);

		// Are we standalone or in the WordPress environment?
		if ( class_exists('wpdb') ) {
			$ydb =  new wpdb(YOURLS_DB_USER, YOURLS_DB_PASS, YOURLS_DB_NAME, YOURLS_DB_HOST);
		} else {
			$ydb =  new ezSQL_mysql(YOURLS_DB_USER, YOURLS_DB_PASS, YOURLS_DB_NAME, YOURLS_DB_HOST);
		}
		if ( $ydb->last_error )
			yourls_die( $ydb->last_error, 'Fatal error', 503 );

		if ( defined('YOURLS_') && YOURLS_DEBUG === true )
			$ydb->show_errors = true;

		return $ydb;
	}

	// Return JSON output. Compatible with PHP prior to 5.2
	function yourls_json_encode($array) {
		if (function_exists('json_encode')) {
			return json_encode($array);
		} else {
			require_once(dirname(__FILE__).'/functions-json.php');
			return self::yourls_array_to_json($array);
		}
	}

	// Return XML output.
	function yourls_xml_encode($array) {
		require_once(dirname(__FILE__).'/functions-xml.php');
		$converter= new yourls_array2xml;
		return $converter->array2xml($array);
	}

	// Return array of all informations associated with keyword. Returns false if keyword not found. Set optional $use_cache to false to force fetching from DB
	function yourls_get_keyword_infos( $keyword, $use_cache = true ) {
		global $ydb;
		$keyword = self::yourls_sanitize_string( $keyword );

		if( isset( $ydb->infos[$keyword] ) && $use_cache == true ) {
			return $ydb->infos[$keyword];
		}

		$table = YOURLS_DB_TABLE_URL;
		$infos = $ydb->get_row("SELECT * FROM `$table` WHERE `keyword` = '$keyword'");

		if( $infos ) {
			$infos = (array)$infos;
			$ydb->infos[$keyword] = $infos;
		} else {
			$ydb->infos[$keyword] = false;
		}

		return $ydb->infos[$keyword];
	}

	// Return (string) selected information associated with a keyword. Optional $notfound = string default message if nothing found
	function yourls_get_keyword_info( $keyword, $field, $notfound = false ) {
		global $ydb;

		$keyword = self::yourls_sanitize_string( $keyword );
		$infos = self::yourls_get_keyword_infos( $keyword );

		if ( isset($infos[$field]) && $infos[$field] !== false )
			return $infos[$field];

		return $notfound;	
	}

	// Return long URL associated with keyword. Optional $notfound = string default message if nothing found
	function yourls_get_keyword_longurl( $keyword, $notfound = false ) {
		return self::yourls_get_keyword_info( $keyword, 'url', $notfound );
	}

	// Return number of clicks on a keyword. Optional $notfound = string default message if nothing found
	function yourls_get_keyword_clicks( $keyword, $notfound = false ) {
		return self::yourls_get_keyword_info( $keyword, 'clicks', $notfound );
	}

	// Return IP that added a keyword. Optional $notfound = string default message if nothing found
	function yourls_get_keyword_IP( $keyword, $notfound = false ) {
		return self::yourls_get_keyword_info( $keyword, 'ip', $notfound );
	}

	// Return timestamp associated with a keyword. Optional $notfound = string default message if nothing found
	function yourls_get_keyword_timestamp( $keyword, $notfound = false ) {
		return self::yourls_get_keyword_info( $keyword, 'timestamp', $notfound );
	}

	// Update click count on a short URL
	function yourls_update_clicks( $keyword ) {
		global $ydb;
		$keyword = self::yourls_sanitize_string( $keyword );
		$table = YOURLS_DB_TABLE_URL;
		return $ydb->query("UPDATE `$table` SET `clicks` = clicks + 1 WHERE `keyword` = '$keyword'");
	}

	// Return array of stats. (string)$filter is 'bottom', 'last', 'rand' or 'top'. (int)$limit is the number of links to return
	function yourls_get_stats( $filter = 'top', $limit = 10 ) {
		global $ydb;

		switch( $filter ) {
			case 'bottom':
				$sort_by = 'clicks';
				$sort_order = 'asc';
				break;
			case 'last':
				$sort_by = 'timestamp';
				$sort_order = 'desc';
				break;
			case 'rand':
			case 'random':
				$sort_by = 'RAND()';
				$sort_order = '';
				break;
			case 'top':
			default:
				$sort_by = 'clicks';
				$sort_order = 'desc';
				break;
		}

		$limit = intval( $limit );
		if ( $limit == 0 )
			$limit = 1;
		$table_url = YOURLS_DB_TABLE_URL;
		$results = $ydb->get_results("SELECT * FROM `$table_url` WHERE 1=1 ORDER BY `$sort_by` $sort_order LIMIT 0, $limit;");

		$return = array();
		$i = 1;

		foreach ($results as $res) {
			$return['links']['link_'.$i++] = array(
				'shorturl' => YOURLS_SITE .'/'. $res->keyword,
				'url' => $res->url,
				'timestamp' => $res->timestamp,
				'ip' => $res->ip,
				'clicks' => $res->clicks,
			);
		}

		$return['stats'] = self::yourls_get_db_stats();

		$return['statusCode'] = 200;

		return $return;
	}

	// Return array of stats. (string)$filter is 'bottom', 'last', 'rand' or 'top'. (int)$limit is the number of links to return
	function yourls_get_link_stats( $shorturl ) {
		global $ydb;

		$table_url = YOURLS_DB_TABLE_URL;
		$res = $ydb->get_row("SELECT * FROM `$table_url` WHERE keyword = '$shorturl';");
		$return = array();

		if( !$res ) {
			// non existent link
			$return = array(
				'statusCode' => 404,
				'message'    => 'Error: short URL not found',
			);
		} else {
			$return = array(
				'statusCode' => 200,
				'message'    => 'success',
				'link'       => array(
			        'shorturl' => YOURLS_SITE .'/'. $res->keyword,
			        'url' => $res->url,
			        'timestamp' => $res->timestamp,
			        'ip' => $res->ip,
			        'clicks' => $res->clicks,
				)
			);
		}

		return $return;
	}

	// Return array for API stat requests
	function yourls_api_stats( $filter = 'top', $limit = 10 ) {
		$return = self::yourls_get_stats( $filter, $limit );
		$return['simple']  = 'Need either XML or JSON format for stats';
		$return['message'] = 'success';
		return $return;
	}

	// Return array for API stat requests
	function yourls_api_url_stats($shorturl) {
		$keyword = str_replace( YOURLS_SITE . '/' , '', $shorturl ); // accept either 'http://ozh.in/abc' or 'abc'
		$keyword = self::yourls_sanitize_string( $keyword );

		$return = self::yourls_get_link_stats( $keyword );
		$return['simple']  = 'Need either XML or JSON format for stats';
		return $return;
	}

	// Expand short url to long url
	function yourls_api_expand( $shorturl ) {
		$keyword = str_replace( YOURLS_SITE . '/' , '', $shorturl ); // accept either 'http://ozh.in/abc' or 'abc'
		$keyword = self::yourls_sanitize_string( $keyword );

		$longurl = self::yourls_get_keyword_longurl( $keyword );

		if( $longurl ) {
			return array(
				'keyword'  => $keyword,
				'shorturl' => YOURLS_SITE . "/$keyword",
				'longurl'  => $longurl,
				'simple'   => $longurl,
				'message'  => 'success',
				'statusCode' => 200,
			);
		} else {
			return array(
				'keyword'  => $keyword,
				'simple'   => 'not found',
				'message'  => 'Error: short URL not found',
				'errorCode' => 404,
			);
		}
	}


	// Get total number of URLs and sum of clicks. Input: optional "AND WHERE" clause. Returns array
	function yourls_get_db_stats( $where = '' ) {
		global $ydb;
		$table_url = YOURLS_DB_TABLE_URL;

		$totals = $ydb->get_row("SELECT COUNT(keyword) as count, SUM(clicks) as sum FROM `$table_url` WHERE 1=1 $where");
		return array( 'total_links' => $totals->count, 'total_clicks' => $totals->sum );
	}

	// Return API result. Dies after this
	function yourls_api_output( $mode, $return ) {
		if( isset( $return['simple'] ) ) {
			$simple = $return['simple'];
			unset( $return['simple'] );
		}
		switch ( $mode ) {
			case 'json':
				header('Content-type: application/json');
				echo self::yourls_json_encode($return);
				break;

			case 'xml':
				header('Content-type: application/xml');
				echo self::yourls_xml_encode($return);
				break;

			case 'simple':
			default:
				if( isset( $simple ) )
					echo $simple;
				break;
		}
		die();
	}

	// Get number of SQL queries performed
	function yourls_get_num_queries() {
		global $ydb;

		return $ydb->num_queries;
	}

	// Compat http_build_query for PHP4
	/*if (!function_exists('http_build_query')) {
		function http_build_query($data, $prefix=null, $sep=null) {
			return yourls_http_build_query($data, $prefix, $sep);
		}
	}
	*/
	// from php.net (modified by Mark Jaquith to behave like the native PHP5 function)
	function yourls_http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
		$ret = array();

		foreach ( (array) $data as $k => $v ) {
			if ( $urlencode)
				$k = urlencode($k);
			if ( is_int($k) && $prefix != null )
				$k = $prefix.$k;
			if ( !empty($key) )
				$k = $key . '%5B' . $k . '%5D';
			if ( $v === NULL )
				continue;
			elseif ( $v === FALSE )
				$v = '0';

			if ( is_array($v) || is_object($v) )
				array_push($ret,self::yourls_http_build_query($v, '', $sep, $k, $urlencode));
			elseif ( $urlencode )
				array_push($ret, $k.'='.urlencode($v));
			else
				array_push($ret, $k.'='.$v);
		}

		if ( NULL === $sep )
			$sep = ini_get('arg_separator.output');

		return implode($sep, $ret);
	}

	// Returns a sanitized a user agent string. Given what I found on http://www.user-agents.org/ it should be OK.
	function yourls_get_user_agent() {
		if ( !isset( $_SERVER['HTTP_USER_AGENT'] ) )
			return '-';

		$ua = strip_tags( html_entity_decode( $_SERVER['HTTP_USER_AGENT'] ));
		$ua = preg_replace('![^0-9a-zA-Z\':., /{}\(\)\[\]\+@&\!\?;_\-=~\*\#]!', '', $ua );

		return substr( $ua, 0, 254 );
	}

	// Redirect to another page
	function yourls_redirect( $location, $code = 301 ) {
		// Redirect, either properly if possible, or via Javascript otherwise
		if( !headers_sent() ) 
		{
			self::yourls_status_header( $code );
			header("Location: $location");
		} else 
		{
			self::yourls_redirect_javascript( $location );
		}
		die();
	}

	// Set HTTP status header
	function yourls_status_header( $code = 200 ) {
		if( headers_sent() )
			return;

		$protocol = $_SERVER["SERVER_PROTOCOL"];
		if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
			$protocol = 'HTTP/1.0';

		$code = intval( $code );
		$desc = self::yourls_get_HTTP_status($code);

		@header ("$protocol $code $desc"); // This causes problems on IIS and some FastCGI setups
	}

	// Redirect to another page using Javascript. Set optional (bool)$dontwait to false to force manual redirection (make sure a message has been read by user)
	function yourls_redirect_javascript( $location, $dontwait = true ) {
		if( $dontwait ) {
		echo '<script type="text/javascript">
		window.location="$location";
		</script>
		<small>(if you are not redirected after 10 seconds, please <a href="$location">click here</a>)</small>';
		} else {
		echo '<p>Please <a href="$location">click here</a></p>';
		}
	}

	// Return a HTTP status code
	function yourls_get_HTTP_status( $code ) {
		$code = intval( $code );
		$headers_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended'
		);

		if ( isset( $headers_desc[$code] ) )
			return $headers_desc[$code];
		else
			return '';
	}


	// Log a redirect (for stats)
	function yourls_log_redirect( $keyword ) {
		if ( !self::yourls_do_log_redirect() )
			return true;

		global $ydb;
		$table = YOURLS_DB_TABLE_LOG;

		$keyword = self::yourls_sanitize_string( $keyword );
		$referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ? yourls_sanitize_url( $_SERVER['HTTP_REFERER'] ) : 'direct' );
		$ua = self::yourls_get_user_agent();
		$ip = self::yourls_get_IP();
		$location = self::yourls_geo_ip_to_countrycode( $ip );

		return $ydb->query( "INSERT INTO `$table` VALUES ('', NOW(), '$keyword', '$referrer', '$ua', '$ip', '$location')" );
	}

	// Check if we want to not log redirects (for stats)
	function yourls_do_log_redirect() {
		return ( !defined('YOURLS_NOSTATS') || YOURLS_NOSTATS != true );
	}

	// Converts an IP to a 2 letter country code, using GeoIP database if available in includes/geo/
	function yourls_geo_ip_to_countrycode( $ip = '', $default = '' ) {
		if ( !file_exists( dirname(__FILE__).'/geo/GeoIP.dat') || !file_exists( dirname(__FILE__).'/geo/geoip.inc') )
			return $default;

		if ( $ip == '' )
			$ip = yourls_get_IP();

		require_once( dirname(__FILE__).'/geo/geoip.inc') ;
		$gi = geoip_open( dirname(__FILE__).'/geo/GeoIP.dat', GEOIP_STANDARD);
		$location = geoip_country_code_by_addr($gi, $ip);
		geoip_close($gi);

		return $location;
	}

	// Converts a 2 letter country code to long name (ie AU -> Australia)
	function yourls_geo_countrycode_to_countryname( $code ) {
		// Load the Geo class if not already done
		if( !class_exists('GeoIP') ) {
			$temp = self::yourls_geo_ip_to_countrycode('127.0.0.1');
		}

		if( class_exists('GeoIP') ) {
			$geo = new GeoIP;
			$id = $geo->GEOIP_COUNTRY_CODE_TO_NUMBER[$code];
			$long = $geo->GEOIP_COUNTRY_NAMES[$id];
			return $long;
		} else {
			return false;
		}
	}

	// Return flag URL from 2 letter country code
	function yourls_geo_get_flag( $code ) {
		// Load the Geo class if not already done
		if( !class_exists('GeoIP') ) {
			$temp = self::yourls_geo_ip_to_countrycode('127.0.0.1');
		}

		if( class_exists('GeoIP') ) {
			return YOURLS_SITE.'/includes/geo/flags/flag_'.(strtolower($code)).'.gif';
		} else {
			return false;
		}
	}


	// Check if an upgrade is needed
	function yourls_upgrade_is_needed() {
		// check YOURLS_DB_VERSION exist && match values stored in YOURLS_DB_TABLE_OPTIONS
		list( $currentver, $currentsql ) = yourls_get_current_version_from_sql();
		if( $currentsql < YOURLS_DB_VERSION )
			return true;

		return false;
	}

	// Get current version & db version as stored in the options DB. Prior to 1.4 there's no option table.
	function yourls_get_current_version_from_sql() {
		$currentver = self::yourls_get_option( 'version' );
		$currentsql = self::yourls_get_option( 'db_version' );

		// Values if version is 1.3
		if( !$currentver )
			$currentver = '1.3';
		if( !$currentsql )
			$currentsql = '100';

		return array( $currentver, $currentsql);
	}

	// Read an option from DB (or from cache if available). Return value or $default if not found
	function yourls_get_option( $option_name, $default = false ) {
		global $ydb;
		if ( !isset( $ydb->option[$option_name] ) ) {
			$table = YOURLS_DB_TABLE_OPTIONS;
			$option_name = self::yourls_escape( $option_name );
			$row = $ydb->get_row( "SELECT `option_value` FROM `$table` WHERE `option_name` = '$option_name' LIMIT 1" );
			if ( is_object( $row) ) { // Has to be get_row instead of get_var because of funkiness with 0, false, null values
				$value = $row->option_value;
			} else { // option does not exist, so we must cache its non-existence
				$value = $default;
			}
			$ydb->option[$option_name] = self::yourls_maybe_unserialize( $value );
		}

		return $ydb->option[$option_name];
	}

	// Read all options from DB at once
	function yourls_get_all_options() {
		global $ydb;
		$table = YOURLS_DB_TABLE_OPTIONS;

		$allopt = $ydb->get_results("SELECT `option_name`, `option_value` FROM `$table` WHERE 1=1");

		foreach( (array)$allopt as $option ) {
			$ydb->option[$option->option_name] = self::yourls_maybe_unserialize( $option->option_value );
		}
	}

	// Update (add if doesn't exist) an option to DB
	function yourls_update_option( $option_name, $newvalue ) {
		global $ydb;
		$table = YOURLS_DB_TABLE_OPTIONS;

		$safe_option_name = self::yourls_escape( $option_name );

		$oldvalue = self::yourls_get_option( $safe_option_name );

		// If the new and old values are the same, no need to update.
		if ( $newvalue === $oldvalue )
			return false;

		if ( false === $oldvalue ) {
			self::yourls_add_option( $option_name, $newvalue );
			return true;
		}

		$_newvalue = self::yourls_escape( yourls_maybe_serialize( $newvalue ) );

		$ydb->query( "UPDATE `$table` SET `option_value` = '$_newvalue' WHERE `option_name` = '$option_name'");

		if ( $ydb->rows_affected == 1 ) {
			$ydb->option[$option_name] = $newvalue;
			return true;
		}
		return false;
	}

	// Add an option to the DB
	function yourls_add_option( $name, $value = '' ) {
		global $ydb;
		$table = YOURLS_DB_TABLE_OPTIONS;
		$safe_name = self::yourls_escape( $name );

		// Make sure the option doesn't already exist. We can check the 'notoptions' cache before we ask for a db query
		if ( false !== self::yourls_get_option( $safe_name ) )
			return;

		$_value = self::yourls_escape( yourls_maybe_serialize( $value ) );

		$ydb->query( "INSERT INTO `$table` (`option_name`, `option_value`) VALUES ('$name', '$_value')" );
		$ydb->option[$name] = $value;
		return;
	}


	// Delete an option from the DB
	function yourls_delete_option( $name ) {
		global $ydb;
		$table = YOURLS_DB_TABLE_OPTIONS;
		$name = self::yourls_escape( $name );

		// Get the ID, if no ID then return
		$option = $ydb->get_row( "SELECT option_id FROM `$table` WHERE `option_name` = '$name'" );
		if ( is_null($option) || !$option->option_id )
			return false;
		$ydb->query( "DELETE FROM `$table` WHERE `option_name` = '$name'" );
		return true;
	}



	// Serialize data if needed. Stolen from WordPress
	function yourls_maybe_serialize( $data ) {
		if ( is_array( $data ) || is_object( $data ) )
			return serialize( $data );

		if ( self::yourls_is_serialized( $data ) )
			return serialize( $data );

		return $data;
	}

	// Check value to find if it was serialized. Stolen from WordPress
	function yourls_is_serialized( $data ) {
		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
			return true;
		if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
			return false;
		switch ( $badions[1] ) {
			case 'a' :
			case 'O' :
			case 's' :
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
					return true;
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
				break;
		}
		return false;
	}

	// Unserialize value only if it was serialized. Stolen from WP
	function yourls_maybe_unserialize( $original ) {
		if ( self::yourls_is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
			return @unserialize( $original );
		return $original;
	}

	// Determine if the current page is private
	function yourls_is_private() {
		if (defined('YOURLS_PRIVATE') && YOURLS_PRIVATE == true) {

			// Allow overruling of particular pages
			$current = basename( $_SERVER["SCRIPT_NAME"] );

			switch( $current ) {

			case 'yourls-api.php':
				if( !defined('YOURLS_PRIVATE_API') || YOURLS_PRIVATE_API != false )
					return true;
				break;

			case 'yourls-infos.php':
				if( !defined('YOURLS_PRIVATE_INFOS') || YOURLS_PRIVATE_INFOS !== false )
					return true;
				break;

			default:
				return true;
				break;
			}
		}

		return false;
	}

	// Show login form if required
	function yourls_maybe_require_auth() {
		if( self::yourls_is_private() )
			require_once( dirname(__FILE__).'/auth.php' );
	}

	// Return word or words if more than one
	function yourls_plural( $word, $count=1 ) {
		return $word . ($count > 1 ? 's' : '');
	}

	// Return trimmed string
	function yourls_trim_long_string( $string, $length = 60, $append = '[...]' ) {
		if ( strlen( $string ) > $length ) {
			$string = substr( $string, 0, $length - strlen( $append ) ) . $append;	
		}
		return $string;
	}

	// Allow several short URLs for the same long URL ?
	function yourls_allow_duplicate_longurls() {
		// special treatment if API to check for WordPress plugin requests
		if( self::yourls_is_API() ) {
			if ( isset($_REQUEST['source']) && $_REQUEST['source'] == 'plugin' ) 
				return false;
		}
		return ( defined( 'YOURLS_UNIQUE_URLS' ) && YOURLS_UNIQUE_URLS == false );
	}

	// Return list of all shorturls associated to the same long URL. Returns NULL or array of keywords.
	function yourls_get_duplicate_keywords( $longurl ) {
		if( !self::yourls_allow_duplicate_longurls() )
			return NULL;

		global $ydb;
		$longurl = self::yourls_escape( yourls_sanitize_url($longurl) );
		$table = YOURLS_DB_TABLE_URL;

		return $ydb->get_col( "SELECT `keyword` FROM `$table` WHERE `url` = '$longurl'" );
	}

	// Check if an IP shortens URL too fast to prevent DB flood. Return true, or die.
	function yourls_check_IP_flood( $ip = '' ) {
		if(
			( defined('YOURLS_FLOOD_DELAY_SECONDS') && YOURLS_FLOOD_DELAY_SECONDS === 0 ) ||
			!defined('YOURLS_FLOOD_DELAY_SECONDS')
		)
			return true;

		$ip = ( $ip ? self::yourls_sanitize_ip( $ip ) : self::yourls_get_IP() );

		// Don't throttle whitelist IPs
		if( defined('YOURLS_FLOOD_IP_WHITELIST' && YOURLS_FLOOD_IP_WHITELIST ) ) {
			$whitelist_ips = explode( ',', YOURLS_FLOOD_IP_WHITELIST );
			foreach( $whitelist_ips as $whitelist_ip ) {
				$whitelist_ip = trim( $whitelist_ip );
				if ( $whitelist_ip == $ip )
					return true;
			}
		}

		// Don't throttle logged in users
		if( self::yourls_is_private() ) {
			 if( self::yourls_is_valid_user() === true )
				return true;
		}

		global $ydb;
		$table = YOURLS_DB_TABLE_URL;

		$lasttime = $ydb->get_var( "SELECT `timestamp` FROM $table WHERE `ip` = '$ip' ORDER BY `timestamp` DESC LIMIT 1" );
		if( $lasttime ) {
			$now = date( 'U' );
			$then = date( 'U', strtotime( $lasttime ) );
			if( ( $now - $then ) <= YOURLS_FLOOD_DELAY_SECONDS ) {
				// Flood!
				yourls_die( 'Too many URLs added too fast. Slow down please.', 'Forbidden', 403 );
			}
		}

		return true;
	}

	// Check if YOURLS is installed
	function yourls_is_installed() {
		static $is_installed = false;
		if ( $is_installed === false ) {
			$check_14 = $check_13 = false;
			global $ydb;
			if( defined('YOURLS_DB_TABLE_NEXTDEC') )
				$check_13 = $ydb->get_var('SELECT `next_id` FROM '.YOURLS_DB_TABLE_NEXTDEC);
			$check_14 = self::yourls_get_option( 'version' );
			$is_installed = $check_13 || $check_14;
		}
		return $is_installed;
	}

	// Compat for PHP < 5.1
	/*if ( !function_exists('htmlspecialchars_decode') ) {
		function htmlspecialchars_decode($text) {
			return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
		}
	}
	*/
	// Generate random string of (int)$lenght length and type $type (see function for details)
	function yourls_rnd_string ( $length = 5, $type = 1 ) {
		$str = '';
		$length = intval( $length );

		// define possible characters
		switch ( $type ) {
			// no vowels to make no offending word, no 0 or 1 to avoid confusion betwee letters & digits. Perfect for passwords.
			case '1':
				$possible = "23456789bcdfghjkmnpqrstvwxyz";
				break;

			// all letters, lowercase
			case '2':
				$possible = "abcdefghijklmnopqrstuvwxyz";
				break;

			// all letters, lowercase + uppercase
			case '3':
				$possible = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
				break;

			// all digits & letters lowercase 
			case '4':
				$possible = "0123456789abcdefghijklmnopqrstuvwxyz";
				break;

			// all digits & letters lowercase + uppercase
			case '5':
				$possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
				break;

		}

		$i = 0;
		while ($i < $length) {
		$str .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}

		return $str;
	}

	// Return salted string
	function yourls_salt( $string ) {
		$salt = defined('YOURLS_COOKIEKEY') ? YOURLS_COOKIEKEY : md5(__FILE__) ;
		return md5 ($string . $salt);
	}

	// Return a time-dependent string for nonce creation
	function yourls_tick() {
		return ceil( time() / YOURLS_NONCE_LIFE );
	}

	// Create a time limited, action limited and user limited token
	function yourls_create_nonce( $action = '-1', $user = false ) {
		if( false == $user )
			$user = defined('YOURLS_USER') ? YOURLS_USER : '-1';
		$tick = self::yourls_tick();
		return substr( self::yourls_salt($tick . $action . $user), 0, 10 );
	}

	// Check validity of a nonce (ie time span, user and action match)
	function yourls_verify_nonce( $nonce, $action = -1, $user = false ) {
		if( false == $user )
			$user = defined('YOURLS_USER') ? YOURLS_USER : '-1';
		$valid = self::yourls_create_nonce( $action, $user );

		return $nonce == $valid ;
	}

	// Sanitize a version number (1.4.1-whatever -> 1.4.1)
	function yourls_sanitize_version( $ver ) {
		return preg_replace( '/[^0-9.]/', '', $ver );
	}

	// Converts keyword into short link
	function yourls_link( $keyword = '' ) {
		return YOURLS_SITE . '/' . self::yourls_sanitize_keyword( $keyword );
	}

	// Check if we're in API mode. Returns bool
	function yourls_is_API() {
		if ( defined('YOURLS_API') && YOURLS_API == true )
			return true;
		return false;
	}

	// Check if we're in Ajax mode. Returns bool
	function yourls_is_Ajax() {
		if ( defined('YOURLS_AJAX') && YOURLS_AJAX == true )
			return true;
		return false;
	}

	// Check if we're in GO mode (redirection on yourls-go.php). Returns bool
	function yourls_is_GO() {
		if ( defined('YOURLS_GO') && YOURLS_GO == true )
			return true;
		return false;
	}

	// Check if we'll need interface display function (ie not API or redirection)
	function yourls_has_interface() {
		if( self::yourls_is_API() or self::yourls_is_GO() or self::yourls_is_Ajax() )
			return false;
		return true;
	}

	// Check if we're in the admin area. Returns bool
	function yourls_is_admin() {
		if ( defined('YOURLS_ADMIN') && YOURLS_ADMIN == true )
			return true;
		return false;
	}

	// Check if SSL is required. Returns bool.
	function yourls_needs_ssl() {
		if ( defined('YOURLS_ADMIN_SSL') && YOURLS_ADMIN_SSL == true )
			return true;
		return false;
	}

	// Return admin link, with SSL preference if applicable.
	function yourls_admin_url( $page = '' ) {
		$admin = YOURLS_SITE . '/admin/' . $page;
		if( defined('YOURLS_ADMIN_SSL') && YOURLS_ADMIN_SSL == true )
			$admin = str_replace('http://', 'https://', $admin);
		return $admin;
	}

	// Check if SSL is used, returns bool. Stolen from WP.
	function yourls_is_ssl() {
		if ( isset($_SERVER['HTTPS']) ) {
			if ( 'on' == strtolower($_SERVER['HTTPS']) )
				return true;
			if ( '1' == $_SERVER['HTTPS'] )
				return true;
		} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			return true;
		}
		return false;
	}
}
?>