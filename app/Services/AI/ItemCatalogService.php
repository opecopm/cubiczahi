<?php

namespace App\Services\AI;

use function Laravel\Ai\agent;

class ItemCatalogService
{
    /**
     * Generate product descriptions based on a prompt.
     * 
     * @param string $requestPrompt
     * @return array|null An array containing 'short_description' and 'description' keys.
     */
    public function generateItemDescriptions(string $requestPrompt): ?array
    {
        if (empty(trim($requestPrompt))) {
            return null;
        }

        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $prompt = "Based on the following request, generate a product description and a short description in English. Return the response strictly as valid JSON without any markdown formatting or code blocks. The JSON should have two keys: 'short_description' (a plain text summary) and 'description' (a detailed description formatted with HTML tags like <p>, <ul>, <li>, <strong>). Request: " . $requestPrompt;

        try {
            $response = agent()->prompt($prompt, provider: 'gemini', model: $model);
            $text = trim($response->text);
            
            // Strip markdown block if returned
            if (str_starts_with($text, '```json')) {
                $text = substr($text, 7);
                if (str_ends_with($text, '```')) {
                    $text = substr($text, 0, -3);
                }
            } elseif (str_starts_with($text, '```')) {
                $text = substr($text, 3);
                if (str_ends_with($text, '```')) {
                    $text = substr($text, 0, -3);
                }
            }
            $text = trim($text);

            $data = json_decode($text, true);

            return $data;
        } catch (\Exception $e) {
            \Log::error('Item Catalog generation failed: ' . $e->getMessage(), [
                'prompt' => $requestPrompt,
            ]);
            throw $e;
        }
    }

    /**
     * Bulk translate all Item Categories.
     *
     * @param bool $overwrite Whether to overwrite existing translations.
     * @param callable|null $logger Optional callback to log errors.
     * @return int The number of successful translations.
     */
    public function translateCategories(bool $overwrite = false, ?callable $logger = null): int
    {
        $activeLanguages = system_setting('active_languages', ['ar']);
        if (is_string($activeLanguages)) {
            $activeLanguages = json_decode($activeLanguages, true) ?? [$activeLanguages];
        }

        $categories = \Modules\Inventory\Models\ItemCategory::all();
        $translatedCount = 0;
        
        $translationService = app(TranslationService::class);

        foreach ($categories as $category) {
            $nameTranslations = $category->getTranslations('name');
            $englishName = $nameTranslations['en'] ?? null;

            if (empty($englishName)) {
                continue;
            }

            foreach ($activeLanguages as $targetLang) {
                if ($targetLang === 'en') {
                    continue;
                }

                if (!$overwrite && !empty($nameTranslations[$targetLang])) {
                    continue;
                }

                try {
                    $translatedText = $translationService->translate($englishName, $targetLang, 'inventory item category name');

                    if (!empty($translatedText)) {
                        $category->setTranslation('name', $targetLang, $translatedText);
                        $category->save();
                        $translatedCount++;
                    }
                } catch (\Exception $e) {
                    if ($logger) {
                        $logger("Error translating category ID {$category->id} to {$targetLang}: " . $e->getMessage());
                    } else {
                        \Log::error("Error translating category ID {$category->id} to {$targetLang}: " . $e->getMessage());
                    }
                }

                // Small delay to respect rate limits
                usleep(500000); // 0.5s
            }
        }

        return $translatedCount;
    }
}
