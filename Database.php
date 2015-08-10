<?php
/**
 * Created by PhpStorm.
 * User: avariya
 * Date: 8/10/15
 * Time: 11:29 AM
 */

namespace WalDB;


class SimpleDB
{
    protected $db;

    public function __construct()
    {
        $this->db = array();
    }

    public function insert($val){
        $this->db[] = $val;
        end($this->db);
        $key = key($this->db);
        return $key;
    }

    public function update($pos, $val)
    {
        $this->db[$pos] = $val;
        return true;
    }

    public function delete($pos)
    {
        $cou = count($this->db);
        unset($this->db[$pos]);
        if (count($this->db) < $cou){
            return true;
        }
        return false;
    }

    public function showAll(){
        print_r($this->db);
    }

    public function showPos($pos){
        echo $this->db[$pos],PHP_EOL;
    }

    public function find($val){
        return array_search($val, $this->db);
    }


}