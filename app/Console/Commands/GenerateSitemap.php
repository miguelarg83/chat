<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generando el sitemap de la web';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
       parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int 
     */
    public function handle()
    {
        // modify this to your own needs 

        SitemapGenerator::create('https://chatconfoto.com')

        ->writeToFile(public_path('sitemap.xml'));
    }
}
