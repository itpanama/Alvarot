<?php

Route::get('/', 'HomeController@index');
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::get('solicitud-transportista', 'TruckerController@solicitudTransportista')->name('solicitud-transportista');

Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => ['auth'], 'prefix' => 'api'], function () {
    Route::get('customers', 'CustomerController@index');
    Route::post('customers/new', 'CustomerController@newCustomer');
    Route::delete('customers/delete/{id}', 'CustomerController@deleteCustomer');
    Route::get('customers/{id}', 'CustomerController@getCustomer');
    Route::put('customers/{id}', 'CustomerController@editCustomer');

    Route::get('tickets', 'TicketController@index');
    Route::get('tickets/assigned-to-trucker', 'TicketController@indexTruckerToTickets');

    Route::get('ticket/show/{id}', 'TicketController@show');
    Route::get('ticket/show/{id}/trucker/{trucker_id}', 'TicketController@showTrucker');

    Route::get('ticket/{ticket_id}/attachment/{attachment_id}/download', 'TicketController@downloadAttachment');

    Route::post('tickets/change_assignation', 'TicketController@changeAssignationToUser');
    Route::post('tickets/change_to_completed', 'TicketController@changeToCompleted');
    Route::post('ticket/new', 'TicketController@new');
    Route::delete('ticket/delete/{id}', 'TicketController@deleteTicket');
    Route::post('ticket/upload_attachment', 'TicketController@uploadAttachment');
    Route::post('tickets/report/export-excel', 'TicketController@exportExcel');


    Route::get('ticket/{ticket_id}/message_attachment/{attachment_id}/download', 'TicketController@downloadMessageAttachment');
    Route::post('messages/new', 'MessageController@new');
    Route::post('ticket/{ticket_id}/new-message', 'MessageController@newMessageFromTicket');
    Route::delete('messages/delete/{id}', 'MessageController@deleteMessage');
    Route::post('messages/upload_attachment', 'MessageController@uploadAttachment');

    Route::get('user/my-profile', 'UserController@getInfo');
    Route::get('users', 'UserController@index');
    Route::get('users/roles', 'UserController@getRoles');
    Route::post('users/new', 'UserController@newUser');
    Route::get('users/{id}', 'UserController@getUser');
    Route::put('users/{id}', 'UserController@editUser');
    Route::delete('users/delete/{id}', 'UserController@deleteUser');

    Route::get('status', 'StatusController@index');
    Route::post('status/new', 'StatusController@newStatus');
    Route::get('status/{id}', 'StatusController@getStatus');
    Route::put('status/{id}', 'StatusController@editStatus');
    Route::delete('status/delete/{id}', 'StatusController@deleteStatus');

    Route::get('general/options', 'GeneralController@getOptions');
    Route::get('general/options_trucker_status', 'GeneralController@getOptionsTruckerStatus');
    Route::get('monitor', 'MonitorController@index');
    Route::get('comunicado/{archivo}', 'UserController@comunicado');

    Route::get('truckers', 'TruckerController@index');
    Route::post('truckers/change_status', 'TruckerController@changeTruckerStatus');
    Route::get('truckers/show/{id}', 'TruckerController@show');
    Route::put('truckers/{id}', 'TruckerController@editTrucker');
    Route::get('truckers/{transporter_id}/attachment/{attachment_id}/download', 'TruckerController@downloadAttachment');
    Route::get('truckers/active', 'TruckerController@getActiveTruckers');
    Route::post('trucker/{id}/messages/upload_attachment', 'TruckerController@uploadAttachmentFromMessage');
    Route::post('trucker/{trucker_id}/new-message', 'TruckerController@newMessage');
    Route::get('trucker/{trucker_id}/message_attachment/{attachment_id}/download', 'TruckerController@downloadMessageAttachment');
    Route::delete('trucker/delete/{id}', 'TruckerController@deleteTrucker');

    Route::get('trucker-request', 'TruckerController@getTruckerRequest');
    Route::put('trucker-request', 'TruckerController@editTruckerRequest');
    Route::post('trucker-request/upload_attachment', 'TruckerController@uploadAttachment');
    Route::post('trucker/save-attachments', 'TruckerController@saveAttachments');
    Route::delete('trucker/delete-attachment/{attachment_id}', 'TruckerController@deleteAttachment');

    Route::get('newsletter', 'NewsletterController@index');
    Route::get('newsletter/active', 'NewsletterController@indexActive');
    Route::delete('newsletter/delete/{id}', 'NewsletterController@deleteNewsletter');
    Route::get('newsletter/{id}', 'NewsletterController@getNewsletter');
    Route::post('newsletter/edit/{id}', 'NewsletterController@editNewsletter');
    Route::post('newsletter/new', 'NewsletterController@new');
    Route::post('newsletter/upload_attachment', 'NewsletterController@uploadAttachment');
    Route::get('newsletter/{id}/attachment-download', 'NewsletterController@downloadAttachment');
    Route::get('newsletter/{id}/attachment-inline', 'NewsletterController@inlineAttachment');

    Route::get('settings', 'SettingsController@index');
    Route::put('settings', 'SettingsController@update');
});

Auth::routes();

Route::get('registro-transportista', 'Auth\RegisterController@showRegistrationForm')->name('registro_transportista');
Route::post('registro-transportista', 'Auth\RegisterController@registerTrucker');


Route::group(['middleware' => ['auth'], 'prefix' => 'web'], function () {
    Route::get('email-test/{ticketID}/send', 'EmailTestController@sentEmail');
    Route::get('email-test/{ticketID}/html', 'EmailTestController@sentHtmlEmail');
});