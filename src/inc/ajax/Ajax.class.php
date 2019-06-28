<?php

class Ajax {
  private $section;
  private $data;
  private $status;
  
  public function __construct($section = null) {
    $this->section = $section;
  }
  
  public function setStatus($status, $message = "") {
    $this->status = $status;
    if (strlen($message) > 0) {
      $this->addData(["message" => $message]);
    }
  }
  
  public function addData($data) {
    foreach ($data as $key => $val) {
      $this->data[$key] = $val;
    }
  }
  
  public function send() {
    $this->addData(["status" => $this->status, "section" => $this->section]);
    header("Content-Type: application/json");
    echo json_encode($this->data);
    die();
  }
}



