<?php

// Rekening bank PT untuk metode transfer manual (USR-04).
// Statis by design — PRD tidak meminta CRUD admin untuk data ini.
return [
    'bank_accounts' => [
        [
            'bank' => 'BCA',
            'account_number' => '8035676763',
            'account_name' => 'Delapan Penjuru Piramida',
        ],
        [
            'bank' => 'Bank Mandiri',
            'account_number' => '1350023452830',
            'account_name' => 'Delapan Penjuru Piramida',
        ],
        [
            'bank' => 'Bank Rakyat Indonesia (BRI)',
            'account_number' => '100601000863560',
            'account_name' => 'PT Delapan Penjuru Piramida',
        ],
    ],
];
