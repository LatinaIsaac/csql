<?php
namespace Core;

class CStatement
{

    private $keyGEN = "sdhvY6232GBE3JH@sj2";
    private $st; //almacena la sentencia SQL
    private $dml; // almacena el tipo de sentencia select.. updtate.. etc..
    private $p;

    private $parameterCount; //almacena la cantidad de ? en una sentencia.
    
    //190 invalid session
    //191 error
    //...
    //200 - sleect
    //201 - no rows founded
    //202 - success
    //
    //300 - update or insert or delet (uid)
    //301 - No object founded for update
    //302 - success
    
    
    public function __construct($key, $statement, $p = []) 
    {
        if (strcmp($key, $this->keyGEN) != 0) 
            $this->response(190, "Invalid Session $key", 0, NULL);
        
        $this->st=$statement;
        $this->dml= strtolower(explode(" ", $statement)[0]);
        $this->p = $p;
        $this->parameterCount = substr_count($statement, "?");
        
        $p = empty(explode(",", $p)[0]) ? null : explode(",", $p);
        
        
        if (is_array($p) && $this->parameterCount != $this->sizeof($p)) {
            $this->response(191, "error - la cantidad de parametros no coinciden ($this->parameterCount, ".$this->sizeof($p).")", 0, NULL);
        }
        
        if (in_array($this->dml, ['select', 'update', 'delete', 'insert'])) 
        {
            $rs = \Core\S_DATABASE::execute($this->st, $p);            
            $this->parseResponse($rs);
        }
    }
    
    // como se llena por explode, el sizeof de PHP siempre devolverá
    // almenos 1 valor. esto lo fixea
    protected function sizeof($array) {
        $i = 0;
        foreach ($array as $val) {
            if (!empty($val))
                $i++;
        }
        return $i;
    }
    
    function parseResponse($rs) 
    {
        switch($this->dml) 
        {
            case 'select':
                if ($rs->rowCount() <= 0)
                    echo $this->response(201, "No Rows founded", 0, $rs->fetchAll());
                else
                    echo $this->response(202, "success", $rs->rowCount(), $rs->fetchAll());    
            case 'update':
            case 'insert':
            case 'delete':
                if ($rs[0] > 0)
                    echo $this->response(302, "success", null, $rs[0]);
                else
                    echo $this->response(301, "No Object founded for (uid) $this->p; RS: $rs[1]", null, $rs);         
            default:
                echo $this->response(191, "dml $this->dml not founded", null, null);       
        }

    }
    
    protected function response($status, $status_message, $data_lenght, $data) {
        header("HTTP/1.1 " . $status);
        header('Content-Type: application/json');

        $response['status'] = $status;
        $response['status_message'] = $status_message;
        $response['columnas'] = $data_lenght;
        $response['data'] = $data;

        $json_response = json_encode($response, JSON_PRETTY_PRINT);
        echo $json_response;
        exit;
    }
}