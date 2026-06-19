<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Ai\agent;
use Modules\Inventory\Models\ItemCategory;

class TranslateCategoriesCommand extends Command
{
    protected $signature = 'inventory:translate-categories {--overwrite : Overwrite existing translations}';

    protected $description = 'Translate Item Categories using Laravel AI';

    public function handle()
    {
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $activeLanguages = system_setting('active_languages', ['ar']);
        if (is_string($activeLanguages)) {
            $activeLanguages = json_decode($activeLanguages, true) ?? [$activeLanguages];
        }
        $langsStr = implode(', ', $activeLanguages);
        $this->info("Translating categories to [{$langsStr}] using Laravel AI ({$model})...");

        $categories = ItemCategory::all();

        $translatedCount = 0;
        $overwrite = $this->option('overwrite');

        foreach ($categories as $category) {
            $nameTranslations = $category->getTranslations('name');
            $englishName = $nameTranslations['en'] ?? null;

            if (empty($englishName)) {
                $this->warn("Skipping Category ID {$category->id} - Missing English name.");
                continue;
            }

            foreach ($activeLanguages as $targetLang) {
                if (!$overwrite && !empty($nameTranslations[$targetLang])) {
                    $this->line("Skipping Category ID {$category->id} for {$targetLang} - Already translated.");
                    continue;
                }

                $this->info("Translating '{$englishName}' to {$targetLang} ...");

                $prompt = "Translate the following inventory item category name from English to the language code '{$targetLang}'. Return ONLY the translated string with no quotes, no markdown, and no extra text.\n\nName: {$englishName}";

                try {
                    $response = agent()
                        ->prompt($prompt, provider: 'gemini', model: $model);
                    
                    $translatedText = trim($response->text);

                    if (!empty($translatedText)) {
                        $category->setTranslation('name', $targetLang, $translatedText);
                        $category->save();
                        $this->info(" -> Success ({$targetLang}): {$translatedText}");
                        $translatedCount++;
                    } else {
                        $this->error(" -> Failed ({$targetLang}): Empty translation returned.");
                    }
                } catch (\Exception $e) {
                    $this->error(" -> Failed ({$targetLang}): API request error. Response: " . $e->getMessage());
                }

                // Small delay to respect rate limits
                usleep(500000); // 0.5s
            }
        }

        $this->info("Completed! Translated {$translatedCount} categories.");
        return Command::SUCCESS;
    }
}
