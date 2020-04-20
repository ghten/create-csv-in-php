<?php

class Library {
    
    private $position;
    
    public function in_array_r($needle, $haystack, $strict = false) {
        $i = 0;
        foreach ($haystack as $item) {
            if ((in_array($needle, $item, $strict))) {
                $this->position = $i;
                return true;
            }
            $i++;
        }

        return false;
    }
    
    public function dateFin($agenda, $dates) {
        foreach ($agenda as $item) {
            if($this->in_array_r($item[3], $dates)) {
                $item[1] = $dates[$this->position]['date'];
                echo $item[1].'ok <br><br>';
            }
        }
        return $agenda;
    }
    
    public function getPosition() {
        return $this->position;
    }
}
