<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relationship;

class FixMissingRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'relationships:fix-missing
                            {--dry-run : Display what would be created without actually creating relationships}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing opposite relationships (e.g., if A is parent of B, ensure B is child of A)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
            $this->newLine();
        }

        $this->info('Scanning for missing opposite relationships...');
        $this->newLine();

        $relationships = Relationship::with(['card', 'relatedCard'])->get();
        $created = 0;
        $skipped = 0;
        $totalChecked = 0;

        foreach ($relationships as $relationship) {
            $totalChecked++;
            
            // Skip if this relationship type doesn't have an opposite
            if (!Relationship::hasOpposite($relationship->relationship_type)) {
                $skipped++;
                continue;
            }

            // Check if the opposite relationship already exists
            $oppositeExists = Relationship::where('card_id', $relationship->related_card_id)
                ->where('related_card_id', $relationship->card_id)
                ->exists();

            if ($oppositeExists) {
                continue;
            }

            // Create the missing opposite relationship
            $oppositeType = Relationship::getOppositeType($relationship->relationship_type);
            
            $this->line(sprintf(
                '<fg=yellow>Missing:</> %s (%s) → %s [%s] (should have reverse: %s [%s])',
                $relationship->card->full_name,
                $relationship->card->unique_name,
                $relationship->relatedCard->full_name,
                $relationship->relationship_type,
                $relationship->relatedCard->full_name,
                $oppositeType
            ));

            if (!$isDryRun) {
                Relationship::create([
                    'card_id' => $relationship->related_card_id,
                    'related_card_id' => $relationship->card_id,
                    'relationship_type' => $oppositeType,
                    'notes' => $relationship->notes,
                ]);

                $this->line(sprintf(
                    '<fg=green>Created:</> %s → %s [%s]',
                    $relationship->relatedCard->full_name,
                    $relationship->card->full_name,
                    $oppositeType
                ));
            } else {
                $this->line(sprintf(
                    '<fg=cyan>Would create:</> %s → %s [%s]',
                    $relationship->relatedCard->full_name,
                    $relationship->card->full_name,
                    $oppositeType
                ));
            }

            $created++;
            $this->newLine();
        }

        $this->newLine();
        $this->info('Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total relationships checked', $totalChecked],
                ['Skipped (no opposite type)', $skipped],
                ['Valid relationships checked', $totalChecked - $skipped],
                ['Missing opposites ' . ($isDryRun ? 'found' : 'created'), $created],
            ]
        );

        if ($isDryRun && $created > 0) {
            $this->newLine();
            $this->warn('This was a DRY RUN. Run without --dry-run to actually create the missing relationships.');
        } elseif ($created === 0) {
            $this->newLine();
            $this->info('✓ All relationships are properly mirrored!');
        } else {
            $this->newLine();
            $this->info("✓ Successfully created {$created} missing relationship(s)!");
        }

        return Command::SUCCESS;
    }
}
