<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Request extends BaseModel {
    protected $table_name = 'trns_requests';
    protected $primary_key = 'request_id';
    protected $fillable = 
    ['request_id', 'pickup_location'];

	function __construct() {
    }
}
