<?php

/**
 * Created by IntelliJ IDEA.
 * User: sein
 * Date: 02.01.17
 * Time: 23:57
 */

namespace DBA;

class HashlistStats extends AbstractModel {
  private $hashlistStatsId;
  private $hashlistId;
  private $data;
  
  function __construct($hashlistStatsId, $hashlistId, $data) {
    $this->hashlistStatsId = $hashlistStatsId;
    $this->hashlistId = $hashlistId;
    $this->data = $data;
  }
  
  function getKeyValueDict() {
    $dict = array();
    $dict['hashlistStatsId'] = $this->hashlistStatsId;
    $dict['hashlistId'] = $this->hashlistId;
    $dict['data'] = $this->data;
    
    return $dict;
  }
  
  function getPrimaryKey() {
    return "hashlistStatsId";
  }
  
  function getPrimaryKeyValue() {
    return $this->hashlistStatsId;
  }
  
  function getId() {
    return $this->hashlistStatsId;
  }
  
  function setId($id) {
    $this->hashlistStatsId = $id;
  }
  
  function getHashlistId(){
    return $this->hashlistId;
  }
  
  function setHashlistId($hashlistId){
    $this->hashlistId = $hashlistId;
  }
  
  function getData(){
    return $this->data;
  }
  
  function setData($data){
    $this->data = $data;
  }

  const HASHLIST_STATS_ID = "hashlistStatsId";
  const HASHLIST_ID = "hashlistId";
  const DATA = "data";
}
