<?php

namespace App\Console\Commands;

use App\Jobs\ImportProductsJob;
use Illuminate\Console\Command;

class ImportProductsCommand extends Command
{
    protected $signature = 'products:import';

    protected $description = 'Busca produtos da FakeStoreAPI e salva no banco de dados';

    public function handle(): void
    {
        $this->info('Despachando job de importação de produtos...');

        ImportProductsJob::dispatch();

        $this->info('Job despachado! Se o driver da fila for "sync", a importação já foi concluída.');
    }
}
