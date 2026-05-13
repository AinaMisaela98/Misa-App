<?php

namespace App\Exports;

use App\Models\Etudiant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EtudiantsExport implements FromCollection, WithHeadings
{
    protected $niveau;

    public function __construct($niveau)
    {
        $this->niveau = $niveau;
    }

    public function collection()
    {
        return Etudiant::where('niveau', $this->niveau)
            ->select('nom', 'email', 'telephone', 'niveau')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Email',
            'Téléphone',
            'Niveau'
        ];
    }
}