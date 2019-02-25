<?php
Route::group('user', function () {
    Route::rule('login', 'admin/auth/doLogin', 'POST');
    Route::rule('info', 'admin/auth/info', 'GET');
    Route::rule('logout', 'admin/auth/logout', 'POST');
})->allowCrossDomain();

Route::group('/auth', function () {
    Route::rule('/rule', 'admin/rbac/rules', 'GET');
    Route::rule('/rule', 'admin/rbac/addRule', 'POST');
    Route::rule('/rule/:id', 'admin/rbac/updateRule', 'PUT');
    Route::rule('/rule/:id', 'admin/rbac/deleteRule', 'DELETE');

    Route::rule('/group', 'admin/rbac/groups', 'GET');
    Route::rule('/group', 'admin/rbac/addGroup', 'POST');
    Route::rule('/group/:id', 'admin/rbac/updateGroup', 'PUT');
    Route::rule('/group/:id', 'admin/rbac/deleteGroup', 'DELETE');

    Route::rule('/user', 'admin/user/list', 'GET');
    Route::rule('/user', 'admin/user/add', 'POST');
    Route::rule('/user/:id', 'admin/user/update', 'PUT');
    Route::rule('/user/:id', 'admin/user/delete', 'DELETE');
})->allowCrossDomain();

