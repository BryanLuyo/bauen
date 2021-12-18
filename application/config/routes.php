<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// WEB
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
// custom route section 
$route['Admin']='Admin/admins/index';
$route['Admin/login']='Admin/admins/login';
$route['Admin/dashboard']='Admin/admins/dashboard';
$route['Admin/logout']='Admin/admins/logout';

//transporter route 
$route['Transporter']='Transporter/transporters/index';
$route['Transporter/dashboard']='Transporter/transporters/dashboard';
$route['Transporter/propuestas']='Transporter/transporters/propuestas';
$route['Transporter/login']='Transporter/transporters/login';
$route['Transporter/verification']='Transporter/transporters/verification';
$route['Transporter/logout']='Transporter/transporters/logout';
$route['Transporter/profile']='Transporter/transporters/profile';
//$route['Transporter/drivers']='Transporter/transporters/drivers';
//$route['Transporter/adddriver']='Transporter/transporters/adddriver';
$route['Transporter/subadmins']='Transporter/transporters/subadmins';
$route['Transporter/addsubadmin']='Transporter/transporters/addsubadmin';
$route['Transporter/editsubadmin/(:any)']='Transporter/transporters/editsubadmin/$1';

$route['Transporter/requests/completed']='Transporter/Requests/completed';
// about us 
$route['aboutus']='users/aboutus';




//API'S REST REQUEST

$route['api/request/listpagination/(:num)']['POST']='Apis/Request/list_request_pagination/$1';
$route['api/request/listpagination/(:any)']['POST']='Apis/Request/list_request_pagination/0';
$route['api/request/listpagination']['POST']='Apis/Request/list_request_pagination/0';
$route['api/request/listpagination']['POST']='Apis/Request/list_request_pagination/0';
$route['api/request/distance/(:any)/(:any)/(:any)/(:any)']['GET']='Apis/Request/distance/$1/$2/$3/$4';

$route['api/placerequest']['POST']='Apis/Request/placerequest';
    //CRON
$route['cron/request/delete/automatic']['GET'] = 'Apis/Request/delete_request';
$route['cron/request/completed/automatic']['GET'] = 'Apis/Request/completed_request';

$route['api/request_id']['POST'] = 'Apis/Request/request_id';
$route['api/notifications/request']['GET'] = 'Apis/Request/notifications_request';
//TEST
$route['api/request_idtest']['POST'] = 'Apis/Request/request_idtest';
$route['api/sendmsm']['GET'] = 'Apis/Users/sendin_sms';


//API'S REST USERS
$route['api/notification/blockestatuschange/(:num)/(:num)']['POST'] = 'Apis/Users/blockestatuschange/$1/$2';
$route['api/user/logout']['POST'] = 'Apis/Users/logout';
$route['api/user/chats']['POST'] = 'Apis/Users/chats';
$route['api/user/newrequest']['POST'] = 'Apis/Users/new_request';
$route['api/user/profile']['POST'] = 'Apis/Users/user_profile';
$route['api/sendsmsapi/(:num)/(:num)/(:num)/(:any)']['GET'] = 'Apis/Users/sendsmsapi/$1/$2/$3/$4';
