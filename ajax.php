<?php    
    require 'DAOCitySearch.php';
    header('Content-Type: application/json');
    init();
    
    /**
     * Depending on the query string, either find the cities that 
     * correspond to user's request, or saves the selected city
     * as a user's choice to the database.
     */
    function init() {
        $daoCity = new DAOCitySearch();
        session_start();
        session_regenerate_id();
        $user = isset($_SESSION['user']) ? $_SESSION['user']: null;
        //find cities according to user's input
        if(!empty($_GET['city'])) {  
            $city = htmlentities($_GET['city']);
            $cities = $daoCity -> findCitiesWithSearch($city, $user);
            echo json_encode($cities, JSON_PRETTY_PRINT);
        }
        //save selected city to the database
        elseif(!empty($_GET['selected']) && strlen($_GET['selected']) < 255) {
            $value = htmlentities($_GET['selected']);
            $daoCity -> insertSearch($value, $user);
        }
    }

