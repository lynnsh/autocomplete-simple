<?php
require_once 'DAOCitySearch.php';
require_once 'DAOUser.php';
init();

/**
 * Creates cities, users and searches tables. 
 * Populates cities table using the information from the file.
 */
function init() {
    $cities = fillCitiesArray();     
    try {
        $daoCity = new DAOCitySearch();
        $daoUser = new DAOUser();
        $daoUser -> createUsersTable();
        echo 'Users table is created successfully.'.PHP_EOL;
        $daoCity -> createCitiesTable();
        echo 'Cities table is created successfully.'.PHP_EOL;
        echo 'Inserting values.'.PHP_EOL;
        $daoCity -> insertCities($cities);
        echo PHP_EOL.'Cities are saved to the database.'.PHP_EOL;       
        $daoCity -> createSearchesTable();
        echo 'Searches table is created successfully.'.PHP_EOL;
    }
    catch(PDOException $e) {
        echo $e -> getMessage();
    }
}

/**
 * Populates cities array using the information from the cities.txt file
 * @return cities array with data from the file.
 */
function  fillCitiesArray() {
    $res = fopen('cities.txt', 'r');
    while(! feof($res)) {
        $array = fgetcsv($res, 0, ';');
        if(!empty($array[0]))
            $cities[] = ['weight' => trim($array[0]), 
                         'city' => trim($array[1])];
    }
    fclose($res);
    
    return $cities;
}

