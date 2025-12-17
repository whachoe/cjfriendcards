<?php

namespace App\Console\Commands;

use App\Models\Card;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportCardsVcard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:export-vcard {--output=storage/exports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all cards as vCard format files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $outputDir = $this->option('output');
        $outputPath = base_path($outputDir);

        // Create output directory if it doesn't exist
        if (!File::exists($outputPath)) {
            File::makeDirectory($outputPath, 0755, true);
            $this->info("Created output directory: {$outputPath}");
        }

        $cards = Card::all();

        if ($cards->isEmpty()) {
            $this->warn('No cards found to export.');
            return 0;
        }

        $this->info("Exporting {$cards->count()} card(s) to {$outputPath}");

        $progressBar = $this->output->createProgressBar($cards->count());
        $progressBar->start();

        foreach ($cards as $card) {
            $vcard = $card->toVcard();
            $filename = $card->unique_name . '.vcf';
            $filePath = $outputPath . DIRECTORY_SEPARATOR . $filename;

            File::put($filePath, $vcard);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Successfully exported {$cards->count()} card(s) to: {$outputPath}");

        return 0;
    }
}
