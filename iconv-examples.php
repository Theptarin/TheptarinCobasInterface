<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$text = "This is the Thai 'ทดสอบ'.";

echo 'Original : ', $text, PHP_EOL;
echo 'TRANSLIT : ', iconv("UTF-8", "tis-620//TRANSLIT", $text), PHP_EOL;
echo 'IGNORE   : ', iconv("UTF-8", "tis-620//IGNORE", $text), PHP_EOL;
echo 'Plain    : ', iconv("UTF-8", "tis-620", $text), PHP_EOL;

