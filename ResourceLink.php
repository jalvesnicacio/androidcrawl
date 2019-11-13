<?php
class ResourceLink
{
	protected $_url;
	protected $_host;
	protected $_useHttpAuth = false;
	protected $_user;
    protected $_pass;

    public function __construct($url)
    {
    	$parse = parse_url($url);
    	$this->_url = $url;
    	$this->_host = $parse['host'];
    }

    public function getUrl()
    {
    	return $this->_url;
    }

    public function getHost()
    {
    	return $this->_host;
    }

    public function getUseHttpAuth()
    {
    	return $this->_useHttpAuth;
    }

    public function getUser()
    {
    	return $this->_user;
    }

    public function getPass()
    {
    	return $this->_pass;
    }

    public function setHttpAuth($user, $pass)
    {
        $this->_useHttpAuth = true;
        $this->_user = $user;
        $this->_pass = $pass;
    }


}