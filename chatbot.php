<?php

include('config.php');


var_dump(HTTPRequest('https://proxer.me/api/v1/user/login', array('api_key'=>$api_key, 'username' => $username, 'password' => $password)));

$messages = HTTPRequest('https://proxer.me/api/v1/chat/messages',array('api_key'=>$api_key, 'room_id' => $room_id, 'message_id' => 0));
$mid = $messages->data[0]->id;


while(true) {
	$messages = HTTPRequest('https://proxer.me/api/v1/chat/newmessages',array('api_key'=>$api_key, 'room_id' => $room_id, 'message_id' => $mid));
	if(!isset($messages->data)){
		break;
	}
	if(isset($messages->data[0])){
		$mid = $messages->data[count($messages)-1]->id;
	}
	foreach($messages->data as $message){
		$merker = explode(' ', $message->message);
		if(strtolower($merker[0]) != '@proxerbot'){
			continue;
		}else{
			echo $message->username.': '.$message->message.''.PHP_EOL;
		}
		if(count($merker) == 3 && $merker[1] == 'dice' && is_numeric($merker[2])){
			if($merker[2]<2 || $merker[2] > 10000){
				$reply = $message->username.'-kun, nimm mich nicht auf den Arm. Baaaka!';
			}else{
				$merker[2] = round($merker[2]);
				$reply = '[b]'.$message->username.'[/b] hat gewürfelt. Augenzahl '.rand(1,$merker[2]).' bei '.$merker[2].' Seiten.';

			}
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(count($merker) == 2 && $merker[1] == 'oniichan'){
			$reply = 'Great job Onii-chan! (✿◠‿◠)';
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(count($merker) == 2 && $merker[1] == 'oneechan'){
			$reply = 'Great job Onee-chan! (✿◠‿◠)';
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(isset($merker[1]) && strtolower($merker[1]) == 'miesmuschel'){
			$antworten = array('Ja','Nein','Vielleicht');
			$reply = '@'.$message->username.': '.$antworten[rand(0,count($antworten)-1)];
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(isset($merker[1]) && strtolower($merker[1]) == 'miisumusheru'){
			$antworten = array('Hai','Yada','Tabun');
			$reply = '@'.$message->username.': '.$antworten[rand(0,count($antworten)-1)];
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(isset($merker[1]) && strtolower($merker[1]) == 'daily-reminder'){
			$reply = 'Deine Waifu ist Scheiße.';
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(isset($merker[1]) && $merker[1] == 'makelove'){
			$users = HTTPRequest('https://proxer.me/api/v1/chat/roomusers',array('api_key'=>$api_key, 'room_id' => $room_id))->data;
			if(count($users) < 4){
				$reply = 'Es sind zu wenige Leute online *schnief*';
			}else{
				$first = rand(0,count($users)-1);
				$second = rand(0,count($users)-1);
				if($first == $second){
					$reply = '@'.$users[$first]->username.' darf sich selbst ... :3';
				}else{
					$reply = $message->username.': @'.$users[$first]->username.' und @'.$users[$second]->username.' sind auserwählt!';
				}
			}
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(count($merker)>1 && strpos($message->message,'?')!==false){
			$antworten = array('Ja','Nein','Vielleicht');
			$reply = '@'.$message->username.': '.$antworten[rand(0,count($antworten)-1)];
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(count($merker) == 2 && $merker[1] == 'neko'){
			$reply = 'Nyaaa~ (^･ｪ･^)';
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}elseif(count($merker)>2 && (strpos($message->message,'ich liebe dich')!==false || $merker[1] == 'ily')){
			$reply = 'Ich liebe dich auch '.$message->username.' <3';
			HTTPRequest('https://proxer.me/api/v1/chat/newmessage',array('api_key'=>$api_key, 'room_id' => $room_id,'message' => $reply));
		}
	}

	sleep(2);
}



function HTTPRequest($url, $post = array()){
	$ch = curl_init();
	$timeout = 100;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0');
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	if(!empty($post)){
		$post_string = '';
		foreach($post as $key=>$value) {
			$post_string .= $key.'='.$value.'&';
		}
		$post_string = rtrim($post_string, '&');
		curl_setopt($ch,CURLOPT_POST, count($post));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $post_string);
	}
	$content = curl_exec($ch);
	curl_close($ch);

	return json_decode($content);
}

?>
