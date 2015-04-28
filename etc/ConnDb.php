<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConnDb
 *
 * @author orr
 */
class ConnDb {

    //put your code here
    public function __construct() {
        $dsn = 'mysql:host=10.1.99.6;dbname=ttr_mse';
        $username = 'orr-projects';
        $password = 'orr-projects';
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        $db_conn = new PDO($dsn, $username, $password, $options);
        //$db_conn = new PDO("mysql:host=10.1.99.6;dbname=ttr_mse", "orr-projects", "orr-projects");
        $sql = 'SELECT * FROM ttr_mse.patient where hn = :hn';
        $stmt = $db_conn->prepare($sql);
        $stmt->execute(array("hn" => 365656));
        $patient = $stmt->fetch();
        print_r($patient);
        return $patient;
    }

}

$my = new ConnDb();
