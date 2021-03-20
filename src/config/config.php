<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Alert Levels
    |--------------------------------------------------------------------------
    |
    | The default sort of alert levels which can be called as functions on
    | the Alerts class. This gives a convenient way to add certain type's
    | of messages.
    |
    | For example:
    |
    |     Alerts::success($message);
    |
    */

    'levels' => array(
        'warning',
        'error',
        'success',
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | The session key which is used to store flashed messages into the current
    | session. This can be changed if it conflicts with another key.
    |
    */

    'session_key' => 'alert_messages',

);
