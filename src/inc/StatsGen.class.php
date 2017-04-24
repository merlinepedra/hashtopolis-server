<?php

use DBA\Hash;
use DBA\Hashlist;
use DBA\HashlistStats;
use DBA\QueryFilter;

class StatsGen {
  private $stats         = array(
    StatsGen::LENGTHS => array(),
    StatsGen::CHARSETS => array(),
    StatsGen::TOTAL_COUNTER => 0
  );
  private $hashlistStats = null;
  private $hashlist      = null;
  
  /**
   * StatsGen constructor.
   * @param $hashlist Hashlist
   */
  public function __construct($hashlist) {
    global $FACTORIES;
    
    $this->hashlist = $hashlist;
    $qF = new QueryFilter(HashlistStats::HASHLIST_ID, $hashlist->getId(), "=");
    $hashlistStats = $FACTORIES::getHashlistStatsFactory()->filter(array($FACTORIES::FILTER => $qF), true);
    if ($hashlistStats != null) {
      $this->hashlistStats = $hashlistStats;
      $this->stats = json_decode($hashlistStats->getData(), true);
    }
  }
  
  /**
   * @param $hashes Hash[]
   */
  public function updateStats($hashes) {
    global $FACTORIES;
    
    foreach ($hashes as $hash) {
      $plain = $hash->getPlaintext();
      if (strpos($plain, "\$HEX[") === 0 && strpos($plain, "]", strlen($plain) - 2) === strlen($plain) - 1) {
        // we have a hex plain here
        $plain = Util::hexToStr(substr($plain, 5, strlen($plain) - 6));
      }
      $val = $this->processPassword($plain);
      
      // count length
      if (isset($this->stats[StatsGen::LENGTHS][$val[StatsGen::PASSWORD_LENGTH]])) {
        $this->stats[StatsGen::LENGTHS][$val[StatsGen::PASSWORD_LENGTH]]++;
      }
      else {
        $this->stats[StatsGen::LENGTHS][$val[StatsGen::PASSWORD_LENGTH]] = 1;
      }
      
      // count charset
      if (isset($this->stats[StatsGen::CHARSETS][$val[StatsGen::PASSWORD_CHARSET]])) {
        $this->stats[StatsGen::CHARSETS][$val[StatsGen::PASSWORD_CHARSET]]++;
      }
      else {
        $this->stats[StatsGen::CHARSETS][$val[StatsGen::PASSWORD_CHARSET]] = 1;
      }
      
      // update total count
      $this->stats[StatsGen::TOTAL_COUNTER]++;
    }
    
    // save/update stats
    if ($this->hashlistStats == null) {
      $this->hashlistStats = new HashlistStats(0, $this->hashlist->getId(), json_encode($this->stats));
      $FACTORIES::getHashlistStatsFactory()->save($this->hashlistStats);
    }
    else {
      $this->hashlistStats->setData(json_encode($this->stats));
      $FACTORIES::getHashlistStatsFactory()->update($this->hashlistStats);
    }
  }
  
  /**
   * @return int[]
   */
  public function getStats() {
    return $this->stats;
  }
  
  private function processPassword($password) {
    $passLength = strlen($password);
    
    $digit = 0;
    $lower = 0;
    $upper = 0;
    $special = 0;
    
    for ($i = 0; $i < $passLength; $i++) {
      if (ctype_upper($password[$i])) {
        $upper++;
      }
      else if (ctype_lower($password[$i])) {
        $lower++;
      }
      else if (ctype_digit($password[$i])) {
        $digit++;
      }
      else {
        $special++;
      }
    }
    
    $charset = StatsGen::CHARSET_ALL;
    if ($digit && !$lower && !$upper && !$special) {
      $charset = StatsGen::CHARSET_NUMERIC;
    }
    else if (!$digit && $lower && !$upper && !$special) {
      $charset = StatsGen::CHARSET_LOWERALPHA;
    }
    else if (!$digit && !$lower && $upper && !$special) {
      $charset = StatsGen::CHARSET_UPPERALPHA;
    }
    else if (!$digit && $lower && $upper && !$special) {
      $charset = StatsGen::CHARSET_MIXEDALPHA;
    }
    else if ($digit && $lower && !$upper && !$special) {
      $charset = StatsGen::CHARSET_LOWERALPHANUM;
    }
    else if ($digit && !$lower && $upper && !$special) {
      $charset = StatsGen::CHARSET_UPPERALPHANUM;
    }
    else if (!$digit && $lower && !$upper && $special) {
      $charset = StatsGen::CHARSET_LOWERALPHASPECIAL;
    }
    else if (!$digit && !$lower && $upper && $special) {
      $charset = StatsGen::CHARSET_UPPERALPHASPECIAL;
    }
    else if ($digit && !$lower && !$upper && $special) {
      $charset = StatsGen::CHARSET_SPECIALNUM;
    }
    else if (!$digit && $lower && $upper && $special) {
      $charset = StatsGen::CHARSET_MIXEDALPHASPECIAL;
    }
    else if ($digit && !$lower && $upper && $special) {
      $charset = StatsGen::CHARSET_UPPERALPHASPECIAL;
    }
    else if ($digit && $lower && !$upper && $special) {
      $charset = StatsGen::CHARSET_LOWERALPHASPECIALNUM;
    }
    else if ($digit && $lower && $upper && !$special) {
      $charset = StatsGen::CHARSET_MIXEDALPHANUM;
    }
    
    return array($passLength, $charset);
  }
  
  const LENGTHS       = "lengths";
  const CHARSETS      = "charsets";
  const TOTAL_COUNTER = "totalCounter";
  
  const PASSWORD_LENGTH  = 0;
  const PASSWORD_CHARSET = 1;
  
  const CHARSET_NUMERIC              = "numeric";
  const CHARSET_LOWERALPHA           = "loweralpha";
  const CHARSET_UPPERALPHA           = "upperalpha";
  const CHARSET_SPECIAL              = "special";
  const CHARSET_MIXEDALPHA           = "mixedalpha";
  const CHARSET_LOWERALPHANUM        = "loweralphanum";
  const CHARSET_UPPERALPHANUM        = "upperalphanum";
  const CHARSET_LOWERALPHASPECIAL    = "loweralphaspecial";
  const CHARSET_UPPERALPHASPECIAL    = "upperalphaspecial";
  const CHARSET_SPECIALNUM           = "specialnum";
  const CHARSET_MIXEDALPHASPECIAL    = "mixedalphaspecial";
  const CHARSET_UPPERALPHASPECIALNUM = "upperalphaspecialnum";
  const CHARSET_LOWERALPHASPECIALNUM = "loweralphaspecialnum";
  const CHARSET_MIXEDALPHANUM        = "mixedalphanum";
  const CHARSET_ALL                  = "all";
}