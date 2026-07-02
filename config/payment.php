<?php

// Rekening bank PT untuk metode transfer manual (USR-04).
// Statis by design — PRD tidak meminta CRUD admin untuk data ini.
return [
    'bank_accounts' => [
        [
            'bank' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'PT Pyramid Amanah Indonesia',
        ],
        [
            'bank' => 'Bank Syariah Indonesia (BSI)',
            'account_number' => '9876543210',
            'account_name' => 'PT Pyramid Amanah Indonesia',
        ],
    ],
];
