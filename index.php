<?php

print 'ทดสอบ';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$myfile = fopen("./RES/02171102016705.txt", "r") or die("Unable to open file!");
echo fread($myfile, filesize("02171102016705.txt"));
fclose($myfile);
