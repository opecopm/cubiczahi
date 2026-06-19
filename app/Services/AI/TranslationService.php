<?php

namespace App\Services\AI;

use function Laravel\Ai\agent;

class TranslationService
{
    /**
     * Translate the given text to the target language code.
     *
     * @param string $text
     * @param string $targetLanguageCode (e.g. 'ar', 'fr')
     * @param string $context (optional) E.g., 'product name', 'product description'
     * @return string
     */
    public function translate(string $text, string $targetLanguageCode, string $context = 'text'): string
    {
        if (empty(trim($text))) {
            return '';
        }

        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        
        $prompt = "Translate the following {$context} from English to the language code '{$targetLanguageCode}'. Return ONLY the translated string with no quotes, no markdown blocks, and keep any HTML tags intact if present.\n\nText: {$text}";

        try {
            $response = agent()->prompt($prompt, provider: 'gemini', model: $model);
            return trim($response->text);
        } catch (\Exception $e) {
            \Log::error('Translation failed: ' . $e->getMessage(), [
                'text' => $text,
                'target' => $targetLanguageCode,
            ]);
            throw $e;
        }
    }
}
