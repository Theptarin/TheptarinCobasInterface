<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WriteFile
 *
 * @author orr
 */
class WriteFile {

    //put your code here
    public function __construct() {
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        $txt = "ภาษาไทยที่อยู่\n";
        fwrite($myfile, $txt);
        $txt = "Minnie Mouse\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    }

}
$my = new WriteFile();
