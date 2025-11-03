<?php

namespace iutnc\netvod\action;

abstract class Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct(){

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    public function execute() : string {
        if ($this->http_method === "GET") {
            return $this->executeGet();
        } else {
            return $this->executePost();
        }
    }

    public abstract function executeGet() : string;

    public abstract function executePost() : string;


}