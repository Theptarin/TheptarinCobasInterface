<?php

require_once "./hl7_2_db.php";
require_once "cobas_hl7.php";
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
 * 5. ย้ายไฟล์ HL7 ไปที่โฟลเดอร์เตรียมส่ง HIMS "www/mount/hims-doc/cobas/RESForHims"
 * @author suchart bunhachirat
 */
class CobasInterface {

    protected $patient = array();

    public function __construct($cobas_foder) {
        /**
         * ถ้าใส่พาทไฟล์ผิดจะไม่มี error ครับ
         */
        $list_files = glob($cobas_foder);
        foreach ($list_files as $filename) {
            printf("$filename size " . filesize($filename ) . "  " . date('Ymd H:i:s') . "\n");
            $this->create_message($filename);
            $hl7_2_db = new hl7_2_db($filename,$this->patient);
            if ($hl7_2_db->error_message == null) {
                $this->move_done_file($filename);
            } else {
                $this->move_error_file($filename);
                echo $hl7_2_db->error_message . "\n";
            }
        }
    }

    /**
     * ย้ายไฟล์ที่ประมาลผลสำเร็จ
     * @param string $filename
     */
    private function move_done_file($filename) {
        try {
            rename($filename, "/var/www/mount/hims-doc/cobas/RESForHims/" . basename($filename));
        } catch (Exception $ex) {
            echo 'Caught exception: ', $ex->getMessage(), "\n";
        }
    }

    /**
     * ย้ายไฟล์ที่ประมาลผลไม่สำเร็จ
     * @param string $filename
     */
    private function move_error_file($filename) {
        try {
            rename($filename, "/var/www/mount/hims-doc/cobas/RESForHims/" . basename($filename));
        } catch (Exception $ex) {
            echo 'Caught exception: ', $ex->getMessage(), "\n";
        }
    }

    /**
     * สร้างไฟล์ ADT Message ส่งกลับ Cobas
     * @param string $filename
     */
    protected function create_message($filename) {
        if (fopen($filename, "r")) {
            $myfile = fopen($filename, "r") or die("Unable to open file!");
            $hl7 = new cobas_hl7(fread($myfile, filesize($filename)));
            $message = $hl7->get_message();
            if ($hl7->valid) {
                $hn = $message["PID"][3];
                $this->get_patient($hn);
                fclose($myfile);
                $this->set_message();
                /**
                 * โฟลเดอร์ที่ต้องย้ายไปหลังจากสร้าง ADT Message แล้ว
                 * $hims_foder = "/var/www/mount/hims-doc/cobas/RES/" . basename($filename);
                 */
                /* $hims_foder = "/var/www/mount/cobas-it-1000/his/ResultForTheptarin/" . basename($filename);
                  echo "Move To " . $hims_foder . "\n";
                  rename($filename, $hims_foder);
                  unlink($filename); */
            } else {
                echo "Unable to read file!";
            }
        } else {
            echo "Unable to open file!";
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

    protected function set_message() {
        $patient = $this->patient;
        $fname = iconv("UTF-8", "tis-620", $patient['fname']);
        $lname = iconv("UTF-8", "tis-620", $patient['lname']);
        $today = date("YmdHi");
        $myfile = fopen("/var/www/mount/cobas-it-1000/his/REQ/$today$patient[hn].hl7", "w") or die("Unable to open file!");
        $myfile_hims = fopen("/var/www/mount/hims-doc/cobas/ADT/$today$patient[hn].hl7", "w") or die("Unable to open file!");
        $segment = "MSH|^~\&||HIS||cobasIT1000|$today||ADT^A01|1027|P|2.3|||NE|NE|AU|ASCII\n";
        $segment .= "EVN|A01|$today\n";
        $segment .= "PID|1||$patient[hn]^^^100^A||$fname^$lname||$patient[birthday]|$patient[sex]||4|^^^^3121||||1201||||||||1100|||||||||AAA\n";
        $segment .= "PV1|1|||||||||||||||||||\n";
        fwrite($myfile, $segment);
        fwrite($myfile_hims, $segment);
        fclose($myfile);
    }

}

$my = new CobasInterface("/var/www/mount/cobas-it-1000/his/RES/*.hl7");
