<?php
// USAGE

include 'Crawler.php';

$startURL = 'https://github.com/search?l=Gradle&q=targetsdkversion&type=Code';
//$startURL = 'http://smartse.ca/';
$depth = 10;
$cssClassName = "code-list-item";


$crawler = new Crawler($startURL, $depth, $cssClassName);

// Exclude path with the following structure to be processed 
$crawler->addFilterPath('customer;account;referer;password_reset;about;personal;contact;help.github.com');
$crawler->run();
