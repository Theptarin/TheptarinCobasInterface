<?php

require_once 'HL7.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CobasInterface
 * การเชื่อมข้อมูล CobasIT1000 เพื่อใช้ข้อมูลผู้ป่วยจาก HIMS ซึ่งต้องเตรียมเมาส์แชร์ไฟล์ cobas ไว้ที่โฟลเดอร์ HIS และ hims-doc ที่โฟลเดอร์ HIMS
 * 1. อ่านไฟล์ HL7 ที่อยู่ในโฟลเดอร์
 * 2. วิเคราะห์ไฟล์แยก HN. มาใช้งาน
 * 3. นำ HN. ไปค้นข้อมูล
 * 4. สร้างไฟล์ ADT Message ไปไว้ที่โฟลเดอร์
 * 5. ย้ายไฟล์ HL7 ไปที่โฟลเดอร์ HIMS
 * @author suchart bunhachirat
 */
class CobasInterface {

    protected $patient = array();
    
    public function __construct($path_foder) {
        $list_files = glob($path_foder);
        foreach ($list_files as $filename) {
            //echo "$filename size " . filesize($filename) . "\n";
            printf("$filename size " . filesize($filename) . "  " . date('Ymd H:i:s') ."\n");
            //echo date("Ymd h:i:s");
            if (fopen($filename, "r")) {
                $myfile = fopen($filename, "r") or die("Unable to open file!");
                $hl7 = new HL7(fread($myfile, filesize($filename)));
                $message = $hl7->get_message();
                if ($hl7->valid) {
                    $hn = $message["PID"][3];
                    $this->get_patient($hn);
                    fclose($myfile);
                    $this->set_message();
                    rename($filename, "./HIMS/RES/" . basename($filename));
                    unlink($filename);
                }  else {
                    echo "Unable to read file!";
                }
            } else {
                echo "Unable to open file!";
            }
        }
    }
    
    
    protected function get_patient($hn) {
        $dsn = 'mysql:host=10.1.99.6;dbname=ttr_mse';
        $username = 'orr-projects';
        $password = 'orr-projects';
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        $db_conn = new PDO($dsn, $username, $password, $options);
        //$db_conn = new PDO("mysql:host=10.1.99.6;dbname=ttr_mse", "orr-projects", "orr-projects");
        $sql = "SELECT hn,fname,lname,DATE_FORMAT(birthday_date,'%Y%m%d') AS birthday ,sex FROM ttr_mse.patient where hn = :hn";
        $stmt = $db_conn->prepare($sql);
        $stmt->execute(array("hn" => $hn));
        $this->patient = $stmt->fetch();
        print_r($this->patient);
        return;
    }
    
    protected function set_message(){
        $patient = $this->patient;
        $fname = iconv("UTF-8", "tis-620", $patient[fname]);
        $lname = iconv("UTF-8", "tis-620", $patient[lname]);
        $today = date("YmdHi");
        $myfile = fopen("./HIS/REQ/$today$patient[hn].hl7", "w") or die("Unable to open file!");
        $myfile_clone = fopen("./HIMS/REQ/$today$patient[hn].hl7", "w") or die("Unable to open file!");
        $segment = "MSH|^~\&||HIS||cobasIT1000|$today||ADT^A01|1027|P|2.3|||NE|NE|AU|ASCII\n";
        $segment .= "EVN|A01|$today\n";
        $segment .= "PID|1||$patient[hn]^^^100^A||$fname^$lname||$patient[birthday]|$patient[sex]||4|^^^^3121||||1201||||||||1100|||||||||AAA\n";
        $segment .= "PV1|1|||||||||||||||||||\n";
        fwrite($myfile, $segment);
        fwrite($myfile_clone, $segment);
        fclose($myfile);
    }
}

$my = new CobasInterface("./HIS/RES/*.hl7");
