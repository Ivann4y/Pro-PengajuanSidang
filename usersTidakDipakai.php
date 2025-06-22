<!-- ?php
$users = [
    'admin' => [
// <<<<<<< HEAD
        ['username' => 'admin1', 'password' => 'adminpass']
    ],
    'dosen' => [
        ['username' => 'dosen1', 'password' => 'dosenpass']
    ],
    'mahasiswa' => [
        ['username' => 'mahasiswa1', 'password' => 'mahasiswapass'],
// =======
        ['username' => 'a1', 'password' => 'ap', 'email' => 'a@gmail.com']
    ],
    'dosen' => [
        ['username' => 'd1', 'password' => 'dp', 'email' => 'd@gmail.com']
    ],
    'mahasiswa' => [
        ['username' => 'm1', 'password' => 'mp', 'email' => 'm@gmail.com']
// >>>>>>> f3e35a794bba0f28378b6b3cec778fd6bbe6f7ca
    ]
]; -->


<?php
$users = [
    'admin' => [
        ['username' => 'a1', 'password' => 'ap', 'email' => 'a@gmail.com']
    ],
    'dosen' => [
        ['username' => 'd1', 'password' => 'dp', 'email' => 'd@gmail.com']
    ],
    'mahasiswa' => [
        ['username' => 'm1', 'password' => 'mp', 'email' => 'm@gmail.com']
    ]
];  