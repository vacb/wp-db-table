<?php 

class GetPets {
  function __construct() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'pets';
    
    // Pull query params from url query string
    $this->args = $this->getArgs();

    $query = "SELECT * FROM $tablename ";
    $query .= $this->createWhereText();
    $query .= " LIMIT 100";

    $countQuery = "SELECT COUNT(*) FROM $tablename ";
    $countQuery .= $this->createWhereText();

    
    $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->args));
    // Use prepare to sanitise input. Placeholders %s for string, %d for digit/number
    // $ourQuery = $wpdb->prepare("SELECT * FROM wp_pets WHERE species = %s AND birthyear > %d LIMIT 10", array('hamster', '2019'));
    $this->pets = $wpdb->get_results($wpdb->prepare($query, $this->args));
    // var_dump($pets);
  }

  function getArgs() {
    $temp = array(
      'favcolor' => sanitize_text_field($_GET['favcolor']),
      'favhobby' => sanitize_text_field($_GET['favhobby']),
      'favfood' => sanitize_text_field($_GET['favfood']),
      'minyear' => sanitize_text_field($_GET['minyear']),
      'maxyear' => sanitize_text_field($_GET['maxyear']),
      'minweight' => sanitize_text_field($_GET['minweight']),
      'maxweight' => sanitize_text_field($_GET['maxweight']),
      'species' => sanitize_text_field($_GET['species']),
    );

    return array_filter($temp, function($x) {
      // If return a value of true, that item will be included in the new array, i.e. filters out args that are empty
      return $x;
    });
  }

  function createWhereText() {
    $whereQuery = "";

    if (count($this->args)) {
      $whereQuery = "WHERE ";
    }

    // Track position to decide whether or not an AND needs appending
    $currentPosition = 0;
    foreach($this->args as $index => $item) {
      // Hand off to switch statement to add correct field name, comparators and decide whether you need to append %s or %d
      $whereQuery .= $this->specificQuery($index);
      if ($currentPosition != count($this->args) - 1) {
        $whereQuery .= " AND ";
      }
      $currentPosition++;
    }
    return $whereQuery;
  }

  function specificQuery($index) {
    switch ($index) {
      case "minweight":
        return "petweight >= %d";
      case "maxweight":
        return "petweight <= %d";
      case "minyear":
        return "birthyear >= %d";
      case "maxyear":
        return "birthyear <= %d";
      default:
      return $index . " = %s";
    }
  }

}
    
