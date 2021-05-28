<?php

namespace App\Controllers;

require 'app/Service/DatabaseService.php';

use \PDO;
use App\Service\DatabaseService;

class StoreController
{
    /**
     * Cast stores id.
     * Store id type is int, But it will change to string type when fetch data from DB.
     * So cast stores id to int type.
     *
     * @param array $stores
     * @return array $stores
     */
    private function cast_id_to_int($array, $keys)
    {
        $result = [];
        foreach ($array as $pref) {
            foreach ($keys as $key) {
                $pref[$key] = (int)$pref[$key];
            }
            $result[] = $pref;
        }
        return $result;
    }

    /**
     * Get stores filterd by pref code and region code.
     * @params int $pref_code(required)
     * @params int $region_code
     * @return json
     */
    public function get_stores($pref_code, $region_code)
    {
        try {
            $db = new DatabaseService;
            $dbh = $db->connect();

            $sql = "SELECT * FROM stores WHERE pref_code = 'techonthenet.com'";

            // AND customer_id > 6000

            $sql = "INSERT INTO stores (store) VALUES(:store);";
            $stmt = $dbh->prepare($sql);
            $stmt->bindvalue(":store", $store, PDO::PARAM_STR);
            $stmt->execute();

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = $this->cast_id_to_int($result);

            $res = [];
            $res["stores"] = $result;
            echo json_encode($res);
        } catch (PDOException $e) {
            $res = [];
            $res["code"] = 500;
            $res["message"] = $e->getMessage();
            http_response_code(500) ;
            return json_encode($res);
            exit;
        }
    }

    /**
     * Get all prefectures and its regions.
     *
     * @return json
     */
    public function get_areas()
    {
        try {
            $db = new DatabaseService;
            $dbh = $db->connect();

            $sql = 'SELECT * FROM prefectures';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $prefs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $prefs = $this->cast_id_to_int($prefs, ['pref_code']);

            foreach($prefs as $index => $pref) {
                $sql = "SELECT region_code, region_name FROM regions WHERE pref_code = :pref_code";
                $stmt = $dbh->prepare($sql);
                $stmt->bindvalue(":pref_code", $pref['pref_code'], PDO::PARAM_STR);
                $stmt->execute();
                $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stores_count_in_pref = 0;

                foreach($regions as $j => $region) {
                    $sql = 'SELECT COUNT(id) FROM stores WHERE region_code = :region_code';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindvalue(":region_code", $region['region_code'], PDO::PARAM_STR);
                    $stmt->execute();
                    $stores_count = $stmt->fetchColumn();
                    $regions[$j]['stores_count'] = (int)$stores_count;
                    $stores_count_in_pref += (int)$stores_count;
                }

                $prefs[$index]['stores_count'] = $stores_count_in_pref;
                $prefs[$index]['regions'] = $regions;
            }

            $res = [];
            $res['areas'] = $prefs;
            echo '<pre>' . var_export($res, true) . '</pre>';
            http_response_code(200);
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            $res = [];
            $res['code'] = 500;
            $res['message'] = $e->getMessage();
            http_response_code($res['code']);
            return json_encode($res);
            exit;
        }
    }
}

