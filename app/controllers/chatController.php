<?php

class Chat extends Controller {
    
    public $chatmodel;
    
    public function init() {
        $this->chatmodel = new ChatModel();
    }

    public function chatHeartbeat() {
	
	$sql = $this->chatmodel->listarChat($_SESSION['username'], 'recd = 0', 'id ASC');
        
        $items = '';
        $chatBoxes = array();
        foreach($sql as $chat) {
        if (!isset($_SESSION['openChatBoxes'][$chat['from']]) && isset($_SESSION['chatHistory'][$chat['from']])) {
            $items = $_SESSION['chatHistory'][$chat['from']];
        }
        $chat['message'] = sanitize($chat['message']);
        $items .= <<<EOD
                                   {
                "s": "0",
                "f": "{$chat['from']}",
                "m": "{$chat['message']}"
   },
EOD;
        if (!isset($_SESSION['chatHistory'][$chat['from']])) {
		$_SESSION['chatHistory'][$chat['from']] = '';
	}

	$_SESSION['chatHistory'][$chat['from']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['from']}",
			"m": "{$chat['message']}"
	   },
EOD;
        unset($_SESSION['tsChatBoxes'][$chat['from']]);
            $_SESSION['openChatBoxes'][$chat['from']] = $chat['sent'];
	}
	if (!empty($_SESSION['openChatBoxes'])) {
	foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
		if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
			$now = time()-strtotime($time);
			$time = date('g:iA M dS', strtotime($time));

			$message = "Sent at $time";
			if ($now > 180) {
				$items .= <<<EOD
                                {
                                    "s": "2",
                                    "f": "$chatbox",
                                    "m": "{$message}"
                                },
EOD;
        if (!isset($_SESSION['chatHistory'][$chatbox])) {
            $_SESSION['chatHistory'][$chatbox] = '';
	}
        $_SESSION['chatHistory'][$chatbox] .= <<<EOD
		{
                    "s": "2",
                    "f": "$chatbox",
                    "m": "{$message}"
                },
EOD;
        $_SESSION['tsChatBoxes'][$chatbox] = 1;
		}
		}
	}
}
 	$sql = $this->chatmodel->alterar($_SESSION['username'],'recd = 0');
         
	if ($items != '') {
		$items = substr($items, 0, -1);
	}
header('Content-type: application/json');
?>
{
		"items": [
			<?php echo $items;?>
        ]
}

<?php
			exit(0);
}
    public function chatBoxSession($chatbox) {
       $items = '';
        if (isset($_SESSION['chatHistory'][$chatbox])) {
                $items = $_SESSION['chatHistory'][$chatbox];
        }
        return $items;
    }

    public function startChatSession() {
        $items = '';
        if (!empty($_SESSION['openChatBoxes'])) {
            foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
                $items .= chatBoxSession($chatbox);
            }
        }
        if ($items != '') {
            $items = substr($items, 0, -1);
        }
        header('Content-type: application/json');
?>
    {
        "username": "<?php echo $_SESSION['username'];?>",
        "items": [
            <?php echo $items;?>
        ]
    }
    <?php
        exit(0);
    }

public function sendChat() {
	$from = $_SESSION['username'];
	$to = $_POST['to'];
	$message = $_POST['message'];

	$_SESSION['openChatBoxes'][$_POST['to']] = date('Y-m-d H:i:s', time());
	

	if (!isset($_SESSION['chatHistory'][$_POST['to']])) {
		$_SESSION['chatHistory'][$_POST['to']] = '';
	}

	$_SESSION['chatHistory'][$_POST['to']] .= <<<EOD
			"s": "1",
			"f": "{$to}",
			"m": "{$messagesan}"
	   },
EOD;


	unset($_SESSION['tsChatBoxes'][$_POST['to']]);
        
        $array = array('from' => $from,'to' => $to,'message' => $message,'sent' => $today = date("Y-m-d H:i:s"));
        $this->chatmodel->setDados($array);
        $this->chatmodel->salvar();
	echo "1";
	exit(0);
}

public function closeChat() {

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	
	echo "1";
	exit(0);
}

public function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br>",$text);
	return $text;
}
    
}
