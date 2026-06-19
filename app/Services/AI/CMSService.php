<?php

namespace App\Services\AI;

use function Laravel\Ai\agent;

class CMSService
{
    /**
     * Generate SEO metadata (title, description, keywords) for a given text or topic.
     *
     * @param string $content
     * @return array|null An array containing 'title', 'description', and 'keywords'.
     */
    public function generateSeoTags(string $content): ?array
    {
        if (empty(trim($content))) {
            return null;
        }

        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $prompt = "Based on the following content, generate SEO metadata. Return the response strictly as valid JSON without any markdown formatting or code blocks. The JSON should have three keys: 'title' (max 60 chars), 'description' (max 160 chars), and 'keywords' (comma separated string). Content: " . $content;

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
            \Log::error('SEO generation failed: ' . $e->getMessage(), [
                'content' => $content,
            ]);
            throw $e;
        }
    }

    /**
     * Generate Page content and SEO metadata based on a prompt.
     *
     * @param string $requestPrompt
     * @return array|null An array containing 'content', 'title', 'meta_description', and 'meta_keywords'.
     */
    public function generatePageContentAndSeo(string $requestPrompt): ?array
    {
        if (empty(trim($requestPrompt))) {
            return null;
        }

        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $prompt = "Based on the following request, generate website page content and SEO metadata. Return the response strictly as valid JSON without any markdown formatting or code blocks. The JSON should have four keys: 'content' (detailed HTML content formatted with tags like <h1>, <p>, <ul>, <strong>), 'title' (max 60 chars), 'meta_description' (max 160 chars), and 'meta_keywords' (comma separated string). Request: " . $requestPrompt;

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
            \Log::error('Page Content & SEO generation failed: ' . $e->getMessage(), [
                'prompt' => $requestPrompt,
            ]);
            throw $e;
        }
    }
}
