<?php

class image{

    private $save_path = 'images/';
    private $image_string = '';
    private $image_name = '';
    private $image;
    private $response = array();

    public $loaded = false;

    public function __construct($name, $inp)
    {
        $this->response = array(
            'success' => 0,
            'message' => 'unknown error.'
        );
        $this->image_name = $name;
        $this->image_string = $inp;
        
        if(!empty($this->image_name) && !empty($this->image_string))
        {
            $this->loaded = true;
        }
    }

    public function save(){
        if(!empty($this->image_name) && !empty($this->image_string))
        {
            return $this->progress();
        }
        else{
            $this->response['message'] = 'Error. Not all required infor is given.';
            return $this->response;
        }
    }

    private function progress(){
        $imgarr = explode(',', $this->image_string);
        if(!isset($imgarr[1])){
            $this->response['message'] = 'Error on post data image. String is not the expected string.';
            return $this->response;
        }
        $this->image = base64_decode($imgarr[1]);
        if(!is_null($this->image)){
            $file = $this->save_path . $this->image_name;
            if(file_exists($file)){
                $this->response['message'] = 'Image already exists on server.';
                return $this->response;
            }
            if(file_put_contents($file, $this->image) !== false){
                $this->response['error'] = 1;
                $this->response['message'] = 'Image saved to server' . $this->image;
                return $this->response;
            }
            else{
                echo 'error escribiendo en disco';
                $this->response['message'] = 'Error writing file to disk';
                return $this->response;
            }
        }
        else{
            $this->response['message'] = 'Error decoding base64 string.';
            return $this->response;
        }
    }
}