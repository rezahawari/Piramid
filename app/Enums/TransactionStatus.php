<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Menunggu = 'menunggu';
    case Dibayar = 'dibayar';
    case HewanDisiapkan = 'hewan_disiapkan';
    case Tersembelih = 'tersembelih';
    case Didistribusikan = 'didistribusikan';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Dibayar => 'Dibayar',
            self::HewanDisiapkan => 'Hewan Disiapkan',
            self::Tersembelih => 'Tersembelih',
            self::Didistribusikan => 'Didistribusikan',
        };
    }

    /**
     * Ordered pipeline of stages, used by the status stepper and
     * for validating that status only moves forward.
     *
     * @return array<int, self>
     */
    public static function pipeline(): array
    {
        return [
            self::Menunggu,
            self::Dibayar,
            self::HewanDisiapkan,
            self::Tersembelih,
            self::Didistribusikan,
        ];
    }

    public function order(): int
    {
        return array_search($this, self::pipeline(), true);
    }
}
