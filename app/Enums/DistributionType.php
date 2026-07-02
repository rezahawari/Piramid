<?php

namespace App\Enums;

enum DistributionType: string
{
    case PtYayasan = 'pt_yayasan';
    case AlamatMandiri = 'alamat_mandiri';

    public function label(): string
    {
        return match ($this) {
            self::PtYayasan => 'Disalurkan oleh PT/Yayasan',
            self::AlamatMandiri => 'Kirim ke Alamat Sendiri',
        };
    }
}
