<?php

return [
    // Email approver tiket baru (Lead Development & Lead Infra)
    'approvers' => [
        'iwan.hermawan@proenergi.co.id',
        'reno.oktavian@proenergi.com',
    ],

    // Aturan pembelian: di bawah batas ini (per unit) pada dasarnya bukan Asset (CAPEX) melainkan Inventory (OPEX).
    // Pengecualian ada di tangan GA, misalnya barang habis pakai di atas batas tetap Inventory.
    'capex_threshold' => 2000000,
];
