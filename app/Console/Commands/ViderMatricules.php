<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ViderMatricules extends Command
{
    protected $signature = 'matricules:vider';

    protected $description = 'Vider les matricules des étudiants et inscriptions';

    public function handle()
    {
        DB::table('etudiants')->update([
            'numero' => null,
        ]);

        DB::table('inscriptions')->update([
            'numero_matricule' => null,
        ]);

        $this->info('Matricules vidés avec succès.');
    }
}
