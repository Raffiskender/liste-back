<?php

namespace Liste_de_course\Models;

class CoreModel
{
    protected $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
}