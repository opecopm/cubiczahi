<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$page = \Modules\CMS\Models\Page::find(2);
if ($page) {
    $content = $page->getTranslation('content', 'en');
    
    // Use regex to strip out any inline monospace fonts or inline code styles inside that paragraph
    $dirty = 'beaut<font color="#000000" face="monospace"><span style="font-size: 11.008px; white-space-collapse: preserve; background-color: color(srgb 0.98451 0.98451 0.98451);">y </span></font><span style="background-color: color(srgb 0.98451 0.98451 0.98451); color: rgb(0, 0, 0); font-family: monospace; font-size: 11.008px; white-space: pre-wrap; letter-spacing: 0px;">essentials.</span>';
    $clean = 'beauty essentials.';
    
    $newContent = str_replace($dirty, $clean, $content);
    
    // Also clean up any general inline monospace background colors in case there are others
    $newContent = preg_replace('/<font[^>]*face="monospace"[^>]*>([\s\S]*?)<\/font>/i', '$1', $newContent);
    
    $page->setTranslation('content', 'en', $newContent);
    $page->save();
    echo "Content cleaned and saved successfully!\n";
} else {
    echo "Page not found.\n";
}
