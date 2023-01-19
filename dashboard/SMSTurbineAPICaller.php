<?php 
/* SMSTurbineAPICaller Class */
class SMSTurbineAPICaller
{
    private $curl = null;
    private $MASK = null;       //Mask [Required]
    private $API_KEY = null;      //Bearer Token [required]
    private $NUMBER = null;     //Receiver Number [Required]
    private $MESSAGE = null;    //Message [Required]
    private $API_LINK = null;   //URL of API [Required]

    function __construct($mask, $api_key, $number, $message, $api_link)
    {
        $this->MASK = $mask;
        $this->API_KEY = $api_key;
        $this->NUMBER = $number;
        $this->MESSAGE = $message;
        $this->API_LINK = $api_link;
        $this->initialize_curl();
    }

    private function initialize_curl()
    {
        $this->curl = curl_init();
        $body = [
            'mask' => $this->MASK,
            'number' => $this->NUMBER,
            'message' => $this->MESSAGE,
        ];

        $header = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->API_KEY
        ];
        curl_setopt($this->curl, CURLOPT_URL, $this->API_LINK);       //API link
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 1 );
        curl_setopt($this->curl, CURLOPT_POST, 1);              //Uing POST method
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);        //Parameters
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);        //Header
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1 );
    }

    private function SmsValidation()
    {
        $isvalid = true;
        $errors = array();
        if ($this->curl == null) {
            $errors[] = 'CURL not initialized';
            $isvalid = false;
        }
        if ($this->MASK == null) {
            $errors[] = 'Mask is required';
            $isvalid = false;
        }
        if ($this->API_KEY == null) {
            $errors[] = 'API key is required';
            $isvalid = false;
        }
        if ($this->NUMBER == null) {
            $errors[] = 'Receiver number is required';
            $isvalid = false;
        }
        if ($this->MESSAGE == null) {
            $errors[] = 'Message is required';
            $isvalid = false;
        }
        if ($this->API_LINK == null) {
            $errors[] = 'API link is required';
            $isvalid = false;
        }

        return array('status' => $isvalid, 'message' => $errors);
    }

    public function execute()
    {
        $res = $this->SmsValidation();
        if ($res['status']) {
            $response = curl_exec($this->curl);
            curl_close($this->curl);
            return json_decode($response,true);
        } else {
            return $res;
        }
    }
}
 
?>                    