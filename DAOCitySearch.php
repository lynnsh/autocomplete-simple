<?php

/**
 * Class responsible to handle CRUD actions for cities and searches tables.
 */
class DAOCitySearch {
    private $pdo;
    
    /**
     * Connects to the database and sets up PDO object.
     */
    function __construct() {
        include 'db.info.php';

        $this -> pdo = new PDO("mysql:host=$host;dbname=$dbname", 
                                $dbuser, $dbpassword);
        $this -> pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Creates cities table.
     */
    function createCitiesTable() {
        try {
            $sql = 'drop table if exists cities; '
                 . 'create table cities '
                 . '(id integer primary key AUTO_INCREMENT, '
                 . 'weight int(9) default 0 not null, '
                 . "city varchar(255) default '' not null)";
            $stmt = $this -> pdo ->prepare($sql);
            $stmt -> execute();
        }
        catch(PDOException $e) {
            echo $e -> getMessage();
        }
    }
    
    /**
     * Creates user searches table.
     */
    function createSearchesTable() {
        try {
            $sql = 'drop table if exists searches; '
                 . 'create table searches '
                 . '(id integer primary key AUTO_INCREMENT, '
                 . 'date datetime default now() not null, '
                 . "city varchar(255) default '' not null, "
                 . 'username varchar(50) default null, '
                 . 'foreign key (username) references users(username))';
            $stmt = $this -> pdo ->prepare($sql);
            $stmt -> execute();
        }
        catch(PDOException $e) {
            echo $e -> getMessage();
        }
    }
    
    /**
     * Inserts a user search to the searches table.
     * If such city already exists, time record is updated.
     * If there are already 5 records, the oldest one is replaced.
     * @param $city the city to save.
     * @param $username the username of the user; 
     *        null if the user is not logged in.
     */
    function insertSearch($city, $username) {
        try {
            $operator = isset($username)?'=':'is';
            $values = $this -> findAllSearches($username, $operator);
            $count = count($values);
            if(!in_array($city, $values)){
                if($count >= 5)
                    //replace oldest search with the newest city
                    $sql = "update searches, "
                        . "(select min(date) as mindate from searches "
                        . "where username $operator :user) "
                        . 'as t2 set city = :city, date = now() where date = '
                        . " t2.mindate and username $operator :user";
                else
                    //add new city search to the table
                    $sql = 'insert into searches (city, username) '
                         . 'values (:city, :user)';

                $stmt = $this -> pdo -> prepare($sql);
                $stmt -> bindValue(':city', $city);
                $stmt -> bindValue(':user', $username);
                $stmt -> execute();
            }
            //update timestamp if city is already in the table
            else {
                $sql = 'update searches set date = now() where city = ?';
                $stmt = $this -> pdo -> prepare($sql);
                $stmt -> bindValue(1, $city);
                $stmt -> execute();
            }
        }
        catch(PDOException $e) {
            echo $e -> getMessage();
        }
    }
       
    /**
     * Finds all searches belonging to the specific user.
     * If no searches found, empty array is returned.
     * @param $username the user whose searches are returned.
     * @param $operator the operator to compare usernames "=" for regular 
     *                  comparison; "is" when checking for null.
     * @return all searches belonging to the specific user.
     */
    private function findAllSearches($username, $operator) {
        try {
            $sql = "select city from searches where username $operator ?";
            $stmt = $this -> pdo -> prepare($sql);
            $stmt -> bindValue(1, $username);
            $stmt -> execute();
            $values = $stmt -> fetchAll();
            foreach($values as $i)
                $cities[] = $i['city'];
            return $cities === null ? [] : $cities;
        }
        catch(PDOException $e) {
            echo $e -> getMessage();
        }
    }
    
    /**
     * Updates cities table with the values in the cities array.
     * @param $cities the array used to populate cities table.
     */
    function insertCities($cities) {
        $index = 0;
        $entry = 0;
        try {
            $sql = 'insert into cities (city, weight) values (?, ?)';
            $stmt = $this -> pdo -> prepare($sql);
            $stmt -> bindParam(1, $city);
            $stmt -> bindParam(2, $weight, PDO::PARAM_INT);            
            echo 'Progress: [';
            foreach($cities as $entry) {
                $city = $entry['city'];
                $weight = $entry['weight'];
                $stmt -> execute();
                
                if($index % 2500 == 0)
                    echo '=';   
                $index++;  
            }
            echo ']';
        }
        catch(PDOException $e) {
            echo $e -> getMessage().PHP_EOL;
        }
    }
    
    /**
     * Returns 5 cities from cities and searches tables corresponding
     * to the specified user.
     * If the user is not logged in, the history will be shared
     * between all such users, thus displaying a trend (i.e. what are
     * the most searchable cities with this key).
     * @param $key the key based on which the cities are found.
     * @param $username the user that requests cities 
     *        (null if user is not logged in).
     * @return 5 cities from cities and searches tables corresponding
     *         to the specified user.
     */
    function findCitiesWithSearch($key, $username) {
        try {
            $operator = isset($username) ? '=' : 'is';
            //searches that user has made before that correspond to this key
            $history = $this -> findSearches($key, $username, $operator);
            //number of cities out of 5 that is left to query
            //from cities table
            $number = 5 - count($history);
            if($number > 0) {
                $sql = 'select city from cities where city like :key and city '
                     . 'not in (select city from searches where city like :key'
                     ." and username $operator :user) limit :num";
                $stmt = $this -> pdo -> prepare($sql);
                $stmt -> bindValue(':key', '%'.$key.'%');
                $stmt -> bindValue(':user', $username);
                $stmt -> bindValue(':num', $number, PDO::PARAM_INT);
                $stmt -> execute();
                $dbResult = $stmt -> fetchAll();
            }
            
            $values = isset($dbResult) ? $dbResult : [];
            $values = isset($history) ? 
                            array_merge($history, $dbResult) : $dbResult;
            
            foreach($values as $i)
                $cities[] = $i['city'];
            return $cities === null? [] : $cities;
        }
        catch(PDOException $e) {
            echo $e -> getMessage();
        }
    }
    
    /**
     * Finds cities in user's searches table that correspond
     * to the specified key.
     * @param $key the key based on which the cities are found;
     * @param $username the user whose histories are found (null if the user
     *                  is anonymous).
     * @param $operator the operator to compare usernames "=" for regular 
     *                  comparison; "is" when checking for null.
     * @return the searches belonging to the specified user and 
     *         that correspond to the key.
     */
    private function findSearches($key, $username, $operator) {
        $sql = "select city from searches "
             . "where city like ? and username $operator ? order by date desc";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> bindValue(1, '%'.$key.'%');
        $stmt -> bindValue(2, $username);
        $stmt -> execute();
        return $stmt -> fetchAll();
    }
    
    /**
     * Unsets PDO object value to get it garbage collected.
     */
    function __destruct() {
        unset($this -> pdo);
    }
}
