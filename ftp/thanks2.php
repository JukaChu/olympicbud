<?
	print_r($_REQUEST);
/*
    [yourName] => Serhio
    [yourPhone] => +380 (63)-100-33-43
    [sendForm3] => 
    [showdesktop] => 0
	
	
	
    [formname] => test
    [nertype] => 1 поверх
    [send-result-polzunok] => 90
    [construction] => Так
    [proekt] => Ні
    [techbud] => Газобетон
    [orderRoof33] => Протягом 3-4 місяців
    [yourName] => Serhio
    [yourPhone] => +380 (63)-100-33-43
    [sendForm1] => 
    [showdesktop] => 0
*/	
	
	if(isset($_REQUEST['yourPhone'])){
		
		header("X-Robots-Tag: noindex,nofollow");
		$success = FALSE;
		$errors = array();
		$notify = '';
		$yourName = strip_tags(trim($_REQUEST['yourName']));
		$yourItem = strip_tags(trim($_REQUEST['yourItem']));
		$yourPhone = strip_tags(trim($_REQUEST['yourPhone']));


		$subject = "Новая заявка с формы обратного звонка";
				
		//$ip = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
		$body = '<table>
					<tr><td>Проект:</td><td>'.$yourItem.'</td></tr>
					<tr><td>Имя:</td><td>'.$yourName.'</td></tr>
					<tr><td>Телефон:</td><td>'.$yourPhone.'</td></tr>
				</table>';
		$headers = "From: \"Olympic Bud\"<no-reply@olympic-bud.com.ua>\n".
		stripslashes('Content-Type: text/html; charset="UTF-8"')."\nReturn-path: <no-reply@olympic-bud.com.ua>";
			
		mail( "volodyaglazko@gmail.com" , $subject, $body ,$headers);
		mail( "romaar@ukr.net" , $subject, $body ,$headers);
		mail( "sergey_derepa@ukr.net" , $subject, $body ,$headers);
		
		$sms = new SMSClient('380632666804','r3940219','d80b4453c1d4298d397d0a4bce2e501697f621b9');
		
		if($nertype=='')
			$sms_text = 'Имя:'.$yourName.',Телефон:'.$yourPhone;
		else
			$sms_text = 'имя:'.$yourName.',телефон:'.$yourPhone.',этажность:'.$nertype.',площадь:'.$send_result_polzunok.',земля есть?:'.$construction.',проект есть?:'.$proekt.',материал:'.$techbud.',начало:'.$orderRoof33;



		$sms -> sendSMS('AutoSich', '0632666804', $sms_text, 0 , '' , 0 );
		$sms -> sendSMS('AutoSich', '0935398802', $sms_text, 0 , '' , 0 );
		$sms -> sendSMS('AutoSich', '0631003784', $sms_text, 0 , '' , 0 );
		
	}	
	
	
	
	
	
	
	
	
	
	
	
	
class SMSClient
{
	public $mode = 'HTTPS'; //HTTP or HTTPS
	protected $_server = '://alphasms.com.ua/api/http.php';
	protected $_errors = array();
	protected $_last_response = array();
	private $_version = '1.9';
	private $_login ='';
	private $_password ='';
	private $_key ='';
	
	//IN: login and password or key on platform (AlphaSMS)
	public function __construct($login = '', $password = '', $key = '')
	{
		$this->_login = $login;
		$this->_password = $password;
		$this->_key = $key;
	}


	//IN: 	sender name, phone of receiver, text message in UTF-8 - if long - will be auto split
	//		send_dt - date-time of sms sending, wap - url for Wap-Push link, flash - for Flash sms.
	//OUT: 	message_id to track delivery status, if empty message_id - check errors via $this->getErrors()
	public function sendSMS($from, $to, $message, $send_dt = 0, $wap = '', $flash = 0)
	{
		
		$errors = '';
		$doc_id = (int)$doc_id;
		if(!$send_dt)
			$send_dt = date('Y-m-d H:i:s');
		$d = is_numeric($send_dt) ? $send_dt : strtotime($send_dt);
		$data = array(	'from'=>$from,
						'to'=>$to,
						'message'=>$message,
						'ask_date'=>date(DATE_ISO8601, $d),
						'wap'=>$wap,
						'flash'=>$flash,
						'class_version'=>$this->_version);
		$result = $this->execute('send', $data);
		if(count(@$result['errors'])){
			$this->_errors = $result['errors'];
			
		}
		return @$result['id'];
		
	}
	
	//IN: 	message_id to track delivery status
	//OUT: 	text name of status
	public function receiveSMS($sms_id)
	{
		$data = array('id'=>$sms_id);
		$result = $this->execute('receive', $data);
		if(count(@$result['errors']))
			$this->_errors = $result['errors'];
		return @$result['status'];		
	}

	//IN: 	message_id to delete
	//OUT: 	text name of status
	public function deleteSMS($sms_id)
	{
		$data = array('id'=>$sms_id);
		$result = $this->execute('delete', $data);
		if(count(@$result['errors']))
			$this->_errors = $result['errors'];
		return @$result['status'];		
	}
	
	//OUT:	amount in UAH, if no return - check errors
	public function getBalance()
	{
		$result = $this->execute('balance');
		if(count(@$result['errors']))
			$this->_errors = $result['errors'];
		return @$result['balance'];		
	}
	
	//OUT:	returns number of errors
	public function hasErrors()
	{
		return count($this->_errors);
	}
	
	//OUT:	returns array of errors
	public function getErrors()
	{
		return $this->_errors;
	}

	public function getResponse()
	{
		return $this->_last_response;
	}

	public function translit($string) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',    'є' => 'ye', 
			'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',   'і' => 'i', 
			'и' => 'i',   'й' => 'j',   'к' => 'k',   'ї' => 'yi', 
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'kh',   'ц' => 'ts',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shch',
			'ь' => '\'',  'ы' => 'y',   'ъ' => '"',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
			
			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',   'Є' => 'Ye',
			'Ё' => 'Yo',   'Ж' => 'Zh',  'З' => 'Z',   'І' => 'I',
			'И' => 'I',   'Й' => 'J',   'К' => 'K',   'Ї' => 'Yi',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'Kh',   'Ц' => 'Ts',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Shch',
			'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '"',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		);
		$result = strtr($string, $converter);
		
		//upper case if needed
		if(mb_strtoupper($string) == $string)
			$result = mb_strtoupper($result);
			
		return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $result);
	}	

	protected function execute($command, $params = array())
	{
		$this->_errors = array();

		//HTTP GET
		if(strtolower($this->mode) == 'http')
		{
			$response = @file_get_contents($this->generateUrl($command, $params));
			return @unserialize($this->base64_url_decode($response));
		}
		else
		{
			$params['login'] = $this->_login;
			$params['password'] = $this->_password;
			$params['key'] = $this->_key;
			$params['command'] = $command;
			$params_url = '';
			foreach($params as $key=>$value)
		 		$params_url .= '&' . $key . '=' . $this->base64_url_encode($value);
		
			//cURL HTTPS POST
			$ch = curl_init(strtolower($this->mode) . $this->_server);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_POST, count($params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params_url);			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);			
			$response = @curl_exec($ch);
			curl_close($ch);

			$this->_last_response = @unserialize($this->base64_url_decode($response));
			return $this->_last_response;		
		}
	}
	
	protected function generateUrl($command, $params = array())
	{
		$params_url = '';
		if(count($params))
			foreach($params as $key=>$value)
		 		$params_url .= '&' . $key . '=' . $this->base64_url_encode($value);
		if(!$this->_key) { 		
			$auth = '?login=' . $this->base64_url_encode($this->_login) . '&password=' . $this->base64_url_encode($this->_password);
		} else {
			$auth = '?key=' . $this->base64_url_encode($this->_key);
		}
		$command = '&command=' . $this->base64_url_encode($command);
		return strtolower($this->mode) . $this->_server . $auth . $command . $params_url;
	}

	public function base64_url_encode($input)
	{
		return strtr(base64_encode($input), '+/=', '-_,');
	}
	
	public function base64_url_decode($input)
	{
		return base64_decode(strtr($input, '-_,', '+/='));
	}

}
	
	
	
	
	
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Будівництво енергоефективних будинків</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="fonts/stylesheet.css">
    <link rel="stylesheet" href="fonts/ico-moon/style.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="js/fancybox/jquery.fancybox.css">
    <link rel="stylesheet" href="css/style.css?v=1.3">

    <link rel="shortcut icon" href="img/favi.png" type="image/png">
  <style>
    .thanks{
  width: 100%;
    height: 100vh;
    position: fixed;
    background-image: url(img/back1.jpg);
    /*background-color: grey;*/
    background-position: center center;
    background-size: cover;
    box-shadow: 0 0 0 9999px rgba(0,0,0,0.55) inset;
    top: 0;
    padding-bottom: 170px;
}
.thanks__logotext{
  color: #fff;
}
.thanks.flex{
  display: flex;
  flex-wrap: wrap;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.thanks h2{
  font: 700 55px Rubik, "Arial Black", Gadget, sans-serif;
  color: #fff;
  margin-top: 40px;
  margin-bottom: 40px;
  text-align: center;
}
.thanks h3{
  font: 700 35px Rubik, "Arial Black", Gadget, sans-serif;
  color: #fff;
  margin-bottom: 50px;
  text-align: center;
}
@media (max-width: 767px){
  .thanks{
    padding-bottom: 340px;
    height: auto;
    min-height: 100vh;
  }
  .thanks-footer{
    padding: 20px 0 100px;
  }
  .thanks-footer .footer12{
    justify-content: center;
  }
  .thanks h2{
    font-size: 25px;
    margin: 20px 0;
  }
  .thanks h3{
    font-size: 25px;
    margin-bottom: 30px;
  }
}
  </style>

</head>
<body class="loaded">

	
	<div class="thanks flex">
		<h2>Дякуємо, ваша заявка прийнята</h2>
		<h3>Ми звяжемось з вами найближчим часом</h3>
    <a href="/">
      <button class="btn-test next-test">
          <span style="color: #000;">Наш сайт</span>
      </button>
    </a>
      
	</div>
</body>
</html>