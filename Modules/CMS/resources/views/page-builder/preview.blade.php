<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->name }} - Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .preview-header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 0 0 10px 10px;
        }
        
        .preview-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .preview-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        /* Reset styles for page content */
        .page-content * {
            all: unset !important;
            display: revert !important;
            box-sizing: border-box !important;
        }
        
        .page-content {
            line-height: 1.6;
        }
        
        /* Text alignment classes */
        .text-left { text-align: left !important; }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        
        /* Word and Letter Animation Styles */
        .animated-text-wordByWord .word,
        .animated-text-letterByLetter .letter {
            opacity: 0;
            display: inline-block;
        }
        
        .animated-text-wordByWord .word.fade,
        .animated-text-letterByLetter .letter.fade {
            animation: fadeInWord 0.5s ease-in-out forwards;
        }
        
        .animated-text-wordByWord .word.slide,
        .animated-text-letterByLetter .letter.slide {
            animation: slideInWord 0.5s ease-in-out forwards;
        }
        
        .animated-text-wordByWord .word.bounce,
        .animated-text-letterByLetter .letter.bounce {
            animation: bounceInWord 0.5s ease-in-out forwards;
        }
        
        .animated-text-wordByWord .word.zoom,
        .animated-text-letterByLetter .letter.zoom {
            animation: zoomInWord 0.5s ease-in-out forwards;
        }
        
        @keyframes fadeInWord {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInWord {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounceInWord {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            50% {
                opacity: 1;
                transform: scale(1.1);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes zoomInWord {
            from { 
                opacity: 0;
                transform: scale(0.5);
            }
            to { 
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Animation Keyframes */
        @keyframes fadeIn { from { opacity: 0.8; } to { opacity: 1; } }
        @keyframes slideInUp { from { opacity: 0.8; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInDown { from { opacity: 0.8; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInLeft { from { opacity: 0.8; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes slideInRight { from { opacity: 0.8; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes zoomIn { from { opacity: 0.8; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
        @keyframes bounceIn { 0% { opacity: 0.8; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.05); } 70% { transform: scale(0.95); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.8; } 100% { transform: scale(1); opacity: 1; } }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .preview-container {
                padding: 10px;
                margin: 0;
            }
            
            .preview-header {
                margin: -10px -10px 15px -10px;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>{{ $page->name }}</h1>
            <p>Preview Mode - This is how your page will appear to visitors</p>
        </div>
        
        <div class="page-content">
            @if(isset($page->sections) && $page->sections && count($page->sections) > 0)
                @foreach($page->sections as $section)
                    <div class="section" style="{{ $section->settings ? $section->getStyleAttributes() : '' }}">
                        @if($section->rows && count($section->rows) > 0)
                            @foreach($section->rows as $row)
                                <div class="row" style="{{ $row->settings ? $row->getStyleAttributes() : '' }}">
                                    @if($row->columns && count($row->columns) > 0)
                                        @foreach($row->columns as $column)
                                            <div class="col-md-{{ $column->width }}" style="{{ $column->settings ? $column->getStyleAttributes() : '' }}">
                                                @if($column->blocks && count($column->blocks) > 0)
                                                    @foreach($column->blocks as $block)
                                                        <div class="block" style="{{ $block->settings ? $block->getStyleAttributes() : '' }}">
                                                            @switch($block->block_type)
                                                                @case('text')
                                                                    <div style="color: {{ $block->settings['text_color'] ?? '#000000' }} !important; font-size: {{ $block->settings['font_size'] ?? 16 }}px !important; font-weight: {{ $block->settings['font_weight'] ?? 'normal' }} !important;">
                                                                        {!! $block->content['text'] ?? '' !!}
                                                                    </div>
                                                                    @break
                                                                @case('heading')
                                                                    <div class="heading-block text-{{ $block->content['alignment'] ?? 'left' }}" 
                                                                         style="color: {{ $block->settings['text_color'] ?? '#000000' }} !important; 
                                                                                font-size: {{ $block->settings['font_size'] ?? 32 }}px !important; 
                                                                                font-weight: {{ $block->settings['font_weight'] ?? 'bold' }} !important; 
                                                                                margin-top: {{ $block->settings['margin_top'] ?? 0 }}px !important; 
                                                                                margin-bottom: {{ $block->settings['margin_bottom'] ?? 20 }}px !important;">
                                                                        <{{ $block->content['level'] ?? 'h2' }} style="color: {{ $block->settings['text_color'] ?? '#000000' }} !important; font-size: {{ $block->settings['font_size'] ?? 32 }}px !important; font-weight: {{ $block->settings['font_weight'] ?? 'bold' }} !important;">{{ $block->content['text'] ?? 'Your Heading Here' }}</{{ $block->content['level'] ?? 'h2' }}>
                                                                    </div>
                                                                    @break
                                                                @case('animated-heading')
                                                                    @if(in_array($block->content['animation_type'] ?? '', ['wordByWord', 'letterByLetter']))
                                                                        <div class="animated-heading-block text-{{ $block->content['alignment'] ?? 'center' }}" 
                                                                             style="color: {{ $block->settings['text_color'] ?? '#000000' }} !important; 
                                                                                    font-size: {{ $block->settings['font_size'] ?? 36 }}px !important; 
                                                                                    font-weight: {{ $block->settings['font_weight'] ?? 'bold' }} !important; 
                                                                                    margin-top: {{ $block->settings['margin_top'] ?? 0 }}px !important; 
                                                                                    margin-bottom: {{ $block->settings['margin_bottom'] ?? 30 }}px !important;">
                                                                            <{{ $block->content['level'] ?? 'h2' }} class="animated-text-{{ $block->content['animation_type'] ?? 'wordByWord' }}" 
                                                                                 data-text="{{ $block->content['text'] ?? 'Animated Heading' }}"
                                                                                 data-delay="{{ $block->content['word_letter_delay'] ?? '0.3s' }}"
                                                                                 data-effect="{{ $block->content['word_letter_effect'] ?? 'fade' }}"
                                                                                 style="color: {{ $block->settings['text_color'] ?? '#000000' }} !important; font-size: {{ $block->settings['font_size'] ?? 36 }}px !important; font-weight: {{ $block->settings['font_weight'] ?? 'bold' }} !important;">
                                                                            </{{ $block->content['level'] ?? 'h2' }}>
                                                                        </div>
                                                                    @else
                                                                        <div class="animated-heading-block text-{{ $block->content['alignment'] ?? 'center' }}" 
                                                                             style="color: {{ $block->settings['text_color'] ?? '#000000' }} !important; 
                                                                                    font-size: {{ $block->settings['font_size'] ?? 36 }}px !important; 
                                                                                    font-weight: {{ $block->settings['font_weight'] ?? 'bold' }} !important; 
                                                                                    margin-top: {{ $block->settings['margin_top'] ?? 0 }}px !important; 
                                                                                    margin-bottom: {{ $block->settings['margin_bottom'] ?? 30 }}px !important;
                                                                                    animation-name: {{ $block->content['animation_type'] ?? 'fadeIn' }} !important;
                                                                                    animation-duration: {{ $block->content['animation_duration'] ?? '1s' }} !important;
                                                                                    animation-delay: {{ $block->content['animation_delay'] ?? '0s' }} !important;
                                                                                    animation-direction: {{ $block->settings['animation_settings']['direction'] ?? 'normal' }} !important;
                                                                                    animation-fill-mode: {{ ($block->settings['animation_settings']['loop'] === 'true' || $block->settings['animation_settings']['loop'] === true) ? 'both' : 'forwards' }} !important;
                                                                                    animation-iteration-count: {{ ($block->settings['animation_settings']['loop'] === 'true' || $block->settings['animation_settings']['loop'] === true) ? 'infinite' : '1' }} !important;
                                                                                    animation-timing-function: ease-in-out !important;">
                                                                            <{{ $block->content['level'] ?? 'h2' }} style="color: {{ $block->settings['text_color'] ?? '#000000' }} !important; font-size: {{ $block->settings['font_size'] ?? 36 }}px !important; font-weight: {{ $block->settings['font_weight'] ?? 'bold' }} !important;">{{ $block->content['text'] ?? 'Animated Heading' }}</{{ $block->content['level'] ?? 'h2' }}>
                                                                        </div>
                                                                    @endif
                                                                    @break
                                                                @case('image')
                                                                    @if($block->content['image_url'])
                                                                        <img src="{{ $block->content['image_url'] }}" alt="{{ $block->content['alt_text'] ?? '' }}" class="img-fluid">
                                                                    @endif
                                                                    @break
                                                                @case('button')
                                                                    <a href="{{ $block->content['button_url'] ?? '#' }}" 
                                                                       target="{{ $block->content['target'] ?? '_self' }}"
                                                                       class="btn btn-primary"
                                                                       style="color: {{ $block->settings['text_color'] ?? '#ffffff' }} !important; 
                                                                              background-color: {{ $block->settings['background_color'] ?? '#007bff' }} !important; 
                                                                              font-size: {{ $block->settings['font_size'] ?? 16 }}px !important; 
                                                                              padding: {{ $block->settings['padding'] ?? '10px 20px' }} !important;">
                                                                        {{ $block->content['button_text'] ?? 'Click Me' }}
                                                                    </a>
                                                                    @break
                                                                @case('video')
                                                                    @if($block->content['video_url'])
                                                                        <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                                                            <iframe src="{{ $block->content['video_url'] }}" 
                                                                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                                                                    frameborder="0" allowfullscreen></iframe>
                                                                        </div>
                                                                    @endif
                                                                    @break
                                                                @case('spacer')
                                                                    <div style="height: {{ $block->settings['height'] ?? 50 }}px;"></div>
                                                                    @break
                                                                @default
                                                                    <div class="alert alert-warning">
                                                                        Unknown block type: {{ $block->block_type }}
                                                                    </div>
                                                            @endswitch
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    <h4>No Content Yet</h4>
                    <p>This page doesn't have any content yet. Start building your page using the page builder.</p>
                    <a href="{{ route('admin.cms.page-builder.builder', $page->id) }}" class="btn btn-primary">Start Building</a>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Word and Letter Animation JavaScript
        function initWordLetterAnimations() {
            // Handle word-by-word animations
            document.querySelectorAll('.animated-text-wordByWord').forEach(function(element) {
                const text = element.getAttribute('data-text') || '';
                const delay = element.getAttribute('data-delay') || '0.3s';
                const effect = element.getAttribute('data-effect') || 'fade';
                
                // Clear existing content
                element.innerHTML = '';
                
                // Split text into words
                const words = text.split(' ');
                
                words.forEach(function(word, index) {
                    const wordSpan = document.createElement('span');
                    wordSpan.className = 'word';
                    wordSpan.textContent = word + (index < words.length - 1 ? ' ' : '');
                    
                    // Set animation delay
                    const delayMs = parseFloat(delay) * 1000;
                    wordSpan.style.animationDelay = (index * delayMs) + 'ms';
                    
                    element.appendChild(wordSpan);
                });
                
                // Trigger animations
                setTimeout(function() {
                    element.querySelectorAll('.word').forEach(function(word) {
                        word.classList.add(effect);
                    });
                }, 100);
            });
            
            // Handle letter-by-letter animations
            document.querySelectorAll('.animated-text-letterByLetter').forEach(function(element) {
                const text = element.getAttribute('data-text') || '';
                const delay = element.getAttribute('data-delay') || '0.1s';
                const effect = element.getAttribute('data-effect') || 'fade';
                
                // Clear existing content
                element.innerHTML = '';
                
                // Split text into characters
                const characters = text.split('');
                
                characters.forEach(function(char, index) {
                    const letterSpan = document.createElement('span');
                    letterSpan.className = 'letter';
                    letterSpan.textContent = char;
                    
                    // Set animation delay
                    const delayMs = parseFloat(delay) * 1000;
                    letterSpan.style.animationDelay = (index * delayMs) + 'ms';
                    
                    element.appendChild(letterSpan);
                });
                
                // Trigger animations
                setTimeout(function() {
                    element.querySelectorAll('.letter').forEach(function(letter) {
                        letter.classList.add(effect);
                    });
                }, 100);
            });
        }
        
        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initWordLetterAnimations();
        });
        
        // Also initialize immediately in case DOMContentLoaded already fired
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initWordLetterAnimations);
        } else {
            initWordLetterAnimations();
        }
    </script>
</body>
</html>
