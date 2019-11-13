<?php
class Document
{
	protected $_content;
	protected $_httpCode;
	protected $_time;
	protected $_resourceLink;
	protected $_dom;

	public function __construct($link)
	{
		$this->_resourceLink = $link;
		$this->open($this->_resourceLink);
		       
	}

	//**
    //parameters: $url
    //out: array with three informations: 
    //  $response = html or xml or whatever...
    //  $httpCode = the code of response
    //  $time = response total time
    //**
    protected function open($link)
    {
        $handle = curl_init($link->getUrl());
        print_r($handle);

        if ($link->getUseHttpAuth()) {
            curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($handle, CURLOPT_USERPWD, $link->getUser() . ":" . $link->getPass());
        }

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $this->_content = curl_exec($handle);
        print_r($this->_content);
        exit();
        // response total time
        $this->_time = curl_getinfo($handle, CURLINFO_TOTAL_TIME);
        /* Check for 404 (file not found). */
        $this->_httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);
        //return array($response, $httpCode, $time);
        
        $this->_dom = new DOMDocument('1.0');
        @$this->_dom->loadHTML($this->_content);
    }

    public function getDOM()
    {
    	return $this->_dom;
    }

    public function getResourceLink()
    {	
    	return $this->_resourceLink;
    }

    public function getContent()
    {
    	return $this->_content;
    }

    protected function getElementByClassName($cssClassName){
        $xpath = new \DOMXpath($this->_dom);
        $nodes = $xpath->query('//div[@class="'.$cssClassName.'"]');    
        return $nodes;
    }

    //**
    //Print the results of crawling
    //**
    public function printResult()
    {
        ob_end_flush();
        //$count = count($this->_seen);
        $url = $this->_resourceLink->getUrl();
        echo "CODE::$this->_httpCode,TIME::$this->_time,DEPTH::URL::$url <br>";
        ob_start();
        flush();
    }
}