<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReadInFolder
 * อ่านไฟล์ที่มีในโฟลเดอร์ทั้งหมด
 * @author orr
 */
class ReadInFolder {

    public $list_files;

    //put your code here
    public function __construct($path_foder = "./RES/*.txt") {
        $this->list_files = glob($path_foder);
    }

    public function open_file() {
        
    }

    public function parsemsg($string) {

        $segs = explode("\n", $string);
        $out = array();
        //get delimiting characters
        if (substr($segs[0], 0, 3) != 'MSH') {
            $out['ERROR'][0] = 'Invalid HL7 Message.';
            $out['ERROR'][1] = 'Must start with MSH';
            return $out;
            exit;
        }

        $delbarpos = strpos($segs[0], '|', 4);  //looks for the closing bar of the delimiting characters
        $delchar = substr($segs[0], 4, ($delbarpos - 4));
        define('FLD_SEP', substr($delchar, 0, 1));
        define('SFLD_SEP', substr($delchar, 1, 1));
        define('REP_SEP', substr($delchar, 2, 1));
        define('ESC_CHAR', substr($delchar, 3, 1));

        foreach ($segs as $fseg) {
            $segments = explode('|', $fseg);
            $segname = $segments[0];
            $i = 0;
            foreach ($segments as $seg) {
                if (strpos($seg, FLD_SEP) == false) {
                    $out[$segname][$i] = $seg;
                } else {
                    $j = 0;
                    $sf = explode(FLD_SEP, $seg);

                    foreach ($sf as $f) {
                        $out[$segname][$i][$j] = $f;
                        $j++;
                    }
                }

                $i++;
            }
        }
        //define('PT_NAME',$out['PID'][5][0],true);
        return $out;
    }

}

$my = new ReadInFolder();
foreach ($my->list_files as $filename) {
    echo "$filename size " . filesize($filename) . "\n";
    $myfile = fopen($filename, "r") or die("Unable to open file!");
    $hl7 = $my->parsemsg(fread($myfile, filesize($filename)));
    $pid = ($hl7["PID"]);
    print_r($pid[3]); //HN. ในไฟล์ HL7
    /*foreach ($hl7 as $key => $value) {
        echo $key . " : " . print_r($value);
    }*/
    
    //echo fread($myfile, filesize($filename));
    fclose($myfile);
//echo $my->parsemsg($filename);
}

