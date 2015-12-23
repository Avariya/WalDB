<?php
/**
 * Created by PhpStorm.
 * User: avariya
 * Date: 8/10/15
 * Time: 12:25 PM
 */
namespace WalDB;

include 'Database.php';

class wallDB extends SimpleDB
{
    private $walPath;
    private $fileWrite = false;
    private $dbPath;
    private $dbFileVer;
    private $opNumber = 0;
    private $maxOps = 10;

    public function __construct(array $conf, $autoSaveAfter = 10)
    {
        parent::__construct();

        $this->walPath = $conf['wal_path'];

        $this->dbPath = $conf['db_file'];
        if (!file_exists($this->dbPath)){
            touch($this->dbPath);
        }
        $this->maxOps = $autoSaveAfter;

        $this->loadDBFromFile();


        if (file_exists($conf['wal_path'])) {//if log file exist
            $this->saveLoggedData($this->dbFileVer);
        } else {
            touch($conf['wal_path']);//else create it
        }
    }

    private function loadDBFromFile()
    {
        if ($file = file_get_contents($this->dbPath)) {//file not found
            $data = json_decode($file, true);

            $this->dbFileVer = $data['ver'];
            unset($data['ver']);
            if ($this->dbFileVer > 0) {//db saved once before
                $this->db = $data;
            } else {
                $this->db = array();
            }
        } else {
            $this->db = array();
            touch($this->dbPath);//create file
        }
    }

    private function saveLoggedData($lastLine)
    {
        $this->fileWrite = true;//not log this actions
        $this->opNumber = 0;
        $this->loadDBFromFile();//load old version of DB;
        $line = 0;
        $file = @fopen($this->walPath, "r");
        if ($file) {
            while (($string = fgets($file)) !== false) {//read log line by line
                ++$line;
                if ($line > $lastLine) {
                    $string = trim($string);
                    //parse line
                    $toDo = explode(';', $string);
                    switch ($toDo[0]) {
                        case 0://remove
                            $this->delete($toDo[1]);
                            break;
                        case 1://update
                            $this->update($toDo[1], $toDo[2]);
                            break;
                        case 2://insert
                            $this->insert($toDo[1]);
                            break;
                        default:
                            //error;
                    }
                }
            }
        }
        $this->db['ver'] = $line;
        $this->dbFileVer = $line;
        $this->fileWrite = false;
        $success = file_put_contents($this->dbPath, json_encode($this->db));
        unset($this->db['ver']);
        return $success;
    }

    public function insert($val)
    {
        if ($this->fileWrite == false) {//if not saving changes to file
            if (file_put_contents($this->walPath, '2;' . $val . PHP_EOL, FILE_APPEND) === false) {
                return false;
            } else {
                if (++$this->opNumber > $this->maxOps) {
                    return $this->saveLoggedData($this->dbFileVer);
                }
            }
        }
        return parent::insert($val);
    }

    public function update($pos, $val)
    {
        if ($this->fileWrite == false) {//if not saving changes to file
            if (file_put_contents($this->walPath, '1;' . $pos . ';' . $val . PHP_EOL, FILE_APPEND) === false) {
                return false;
            } else {
                if (++$this->opNumber > $this->maxOps) {
                    return $this->saveLoggedData($this->dbFileVer);
                }
            }
        }
        return parent::update($pos, $val);
    }

    public function delete($pos)
    {
        if ($this->fileWrite == false) {//if not saving changes to file
            if (file_put_contents($this->walPath, '0;' . $pos . PHP_EOL, FILE_APPEND) === false) {
                return false;
            } else {
                if (++$this->opNumber > $this->maxOps) {
                    return $this->saveLoggedData($this->dbFileVer);
                }
            }
        }
        return parent::delete($pos);
    }
}