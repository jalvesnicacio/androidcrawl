<?php
class Crawler
{
    protected $_url;
    protected $_depth;
    protected $_host;
    protected $_useHttpAuth = false;
    protected $_seen = array();
    protected $_filter = array();
    protected $_cssClassName;

    
    //**
    // Construtor do crawler. 
    // Parâmetros: 
    //      - a url inicial;
    //      - a profundidade da busca;
    //      - a classe CSS que o crawler deve procurar
    //  Cria um objeto Crawler e instancia o atributo $_host com o domínio do site (url).
    // **
    public function __construct($url, $depth = 5, $cssClassName = "code-list-item")
    {
        $this->_url = $url;
        $this->_depth = $depth;
        $parse = parse_url($url);
        $this->_host = $parse['host'];
        $this->_cssClassName = $cssClassName;
    }

    protected function makeAbsoluteHref($url, $href){
        // making absolute href 
        if (0 !== strpos($href, 'http')) {
            $path = '/' . ltrim($href, '/');
            if (extension_loaded('http')) {
                $href = http_build_url($url, array('path' => $path));
            } else {
                $parts = parse_url($url);
                $href = $parts['scheme'] . '://';
                if (isset($parts['user']) && isset($parts['pass'])) {
                    $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                }
                $href .= $parts['host'];
                if (isset($parts['port'])) {
                    $href .= ':' . $parts['port'];
                }
                $href .= $path;
            }
        }
        return $href;
    }


    //**
    // Esta função que deveria encontrar todos links dos repositórios, mas 
    protected function processAnchors($content, $url, $depth)
    {
        $dom = new DOMDocument('1.0');
        @$dom->loadHTML($content);
        $anchors = $dom->getElementsByTagName('a');

        //------ Testes
        //
        //O título da página com os resultados da query deveria ser: "Search · targetsdkversion"
        
        $pageTitle = $dom->getElementsByTagName('title');
       
       
        foreach ($pageTitle as $pt) {
            print_r("<br>------<br>");
            echo "page: " . $pt->nodeValue, PHP_EOL;
            echo " | anchors: " . $anchors->length;
            print_r("<br>------<br>");
        }
        if($pageTitle->length == 0){
            print_r("<br>------<br>");
            echo "There is no title hear...URL: " . $url . " | anchors: " . $anchors->length;
            print_r("<br>------<br>");
        }

        //----------------

        foreach ($anchors as $element) {
            $href = $this->makeAbsoluteHref($url, $element->getAttribute('href'));
            
            // Crawl only link that belongs to the start domain
            $this->crawlPage($href, $depth - 1);
        }
    }

    protected function getElementByClassName($dom, $cssClassName){
        $xpath = new \DOMXpath($dom);
        $nodes = $xpath->query('//div[@class="'.$cssClassName.'"]');    
        return $nodes;
    }

    //**
    //parameters: $url
    //out: array with three informations: 
    //  $response = html or xml or whatever...
    //  $httpCode = the code of response
    //  $time = response total time
    //**
    protected function getContent($url)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);
        // response total time
        $time = curl_getinfo($handle, CURLINFO_TOTAL_TIME);
        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);
        return array($response, $httpCode, $time);
    }

    //**
    //Print the results of crawling
    //**
    protected function printResult($url, $depth, $httpcode, $time)
    {
        ob_end_flush();
        $currentDepth = $this->_depth - $depth;
        $count = count($this->_seen);
        echo "N::$count,CODE::$httpcode,TIME::$time,DEPTH::$currentDepth URL::$url <br>";
        ob_start();
        flush();
    }

    //**
    //Return false if:
    //  1) the url is not in _host;
    //  2) the $depth is 0;
    //  3) the url is alread seen;
    //  4) the url is excluded by filter
    //  **
    protected function isValid($url, $depth)
    {
        if (strpos($url, $this->_host) === false
            || $depth === 0
            || isset($this->_seen[$url])
        ) {
            return false;
        }
        foreach ($this->_filter as $excludePath) {
            if (strpos($url, $excludePath) !== false) {
                return false;
            }
        }
        return true;
    }


    //**
    // crawling the page
    //**
    public function crawlPage($url, $depth)
    {
        if (!$this->isValid($url, $depth)) {
            return;
        }
        // add to the seen URL
        $this->_seen[$url] = true;
        // get Content and Return Code
        list($content, $httpcode, $time) = $this->getContent($url);
        
        // print Result for current Page: <------ TO DO
        //$this->printResult($url, $depth, $httpcode, $time);
        
        // process subPages
        $this->processAnchors($content, $url, $depth);
    }

    public function addFilterPath($path)
    {
        $this->_filter = explode(";", $path);
    }

    public function run()
    {
        $this->crawlPage($this->_url, $this->_depth);
    }
}
?>