<?php
require 'emailService.php';
require_once 'debugHelper.php';

// Test with one small file
$testAttachments = [
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/sp_otherdocs/sp_otherdocs-6929b095af57c-My_Schedule.png',
        'name' => 'test1.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test2.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test3.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test4.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test5.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test6.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test7.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test8.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test9.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test10.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test11.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test12.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test13.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test14.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test15.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test16.png'
    ],
    [
        'path' => 'C:/xampp/htdocs/paragonafs1/uploads/tax/user_18/app_id_proof/6928fe7292531-My_Schedule__1_.png',
        'name' => 'test17.png'
    ],
    [
        'path' => 'C:\xampp\htdocs\paragonafs1\uploads\tax\user_18\sp_otherdocs\sp_otherdocs-692938e684e9b-My_Schedule__1_.png',
        'name' => 'test1800000.png'
    ]
];

$result = sendEmail(
    ['lance.canadianwebdesigns@gmail.com', 'lanceruzel2@gmail.com'], 
    'Test with Many attachment', 
    '<h1>Testing Many attachment</h1>',
    $testAttachments
);

$result = sendEmail(
    ['lance.canadianwebdesigns@gmail.com'], 
    'Test with Many attachment', 
    '<h1>Testing Many attachment</h1>',
    []
);

echo $result === true ? "Email sent!" : "Failed: $result";
?>