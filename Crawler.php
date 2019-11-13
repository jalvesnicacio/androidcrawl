<?php
include 'Document.php';
class Crawler
{
    protected $_iResourceLink;
    protected $_depth;
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
    public function __construct($resourceLink, $depth = 5, $cssClassName = "code-list-item")
    {
        $this->_iResourceLink = $resourceLink;
        $this->_depth = $depth;
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
    protected function processAnchors($document, $depth)
    {
        
        $url = $document->getResourceLink()->getUrl();
        $anchors = $document->getDOM()->getElementsByTagName('a');

        //------ Testes
        //
        //O título da página com os resultados da query deveria ser: "Search · targetsdkversion"
        
        /*$pageTitle = $dom->getElementsByTagName('title');
       
       
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
        }*/

        //----------------

        foreach ($anchors as $element) {
            $href = $this->makeAbsoluteHref($url, $element->getAttribute('href'));
            //print_r($element->getAttribute('href'));
            //print_r($document->getContent());
            //exit();
            
            // Crawl only link that belongs to the start domain
            $this->crawlPage($href, $depth - 1);
        }
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
        if (strpos($url, $this->_iResourceLink->getHost()) === false
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
    // crawling engine
    //**
    public function crawlPage($link, $depth)
    {

        $url = $link->getUrl();

        if (!$this->isValid($url, $depth)) {
            return;
        }
        
        // add to the seen URL
        $this->_seen[$url] = true;
        
        // get Content and Return Code
        $document = new Document($link);

        // print Result for current Page:
       
        // TO DO:
        $document->printResult();
        //$currentDepth = $this->_depth - $depth;
        //$this->printDepth();
        
        // process subPages
        $this->processAnchors($document, $depth);
    }

    //**
    // This is a bleck list of url's...
    // **
    public function addFilterPath($path)
    {
        $this->_filter = explode(";", $path);
    }


    //**
    // run the crawler
    //** 
    public function run()
    {
        $this->crawlPage($this->_iResourceLink, $this->_depth);
    }
}
?>