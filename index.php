<?php
include 'Crawler.php';
include 'ResourceLink.php';

$startURL = 'https://github.com/search?l=Gradle&q=targetsdkversion&type=Code';
//$startURL = 'https://jalvesnicacio:j22e0778@github.com/search?l=Gradle&q=targetsdkversion&type=Code';
//$startURL = 'http://smartse.ca/';
$depth = 1;
$cssClassName = "code-list-item";
$username = 'jalvesnicacio';
$password = 'j22e0778';

$link = new ResourceLink($startURL);
$link->setHttpAuth($username, $password);
$crawler = new Crawler($link, $depth, $cssClassName);

// Exclude path with the following structure to be processed 
$crawler->addFilterPath('customer;account;referer;password_reset;about;personal;contact;help.github.com');
$crawler->run();
