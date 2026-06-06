<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">{{ $page->name ?? $page->title ?? 'Untitled' }}</h2>
                    <div class="text-muted mt-1">
                        Slug: <code>{{ $page->slug }}</code> &nbsp;|&nbsp;
                        <strong>Status:</strong> {{ ucfirst($page->status) }} &nbsp;|&nbsp;
                        <strong>Template:</strong> {{ ucfirst($page->template_type ?? 'default') }}
                        @if ($page->published_at)
                            &nbsp;|&nbsp; <strong>Published At:</strong> {{ $page->published_at->format('d M, Y H:i') }}
                        @endif
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a class="btn btn-outline-primary me-2" href="{{ route('admin.cms.page-builder.builder', $page) }}">
                        <i class="ti ti-pencil me-1"></i> Edit Page
                    </a>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.cms.page-builder.index') }}">
                        <i class="ti ti-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">Page Preview</h3>
                </div>
                <div class="card-body">
                    <div class="border p-3 bg-light">
                        @if (method_exists($page, 'sections') && $page->sections && $page->sections->count() > 0)
                            @foreach ($page->sections as $section)
                                <div class="section mb-3" style="{{ $section->getStyleAttributes() }}">
                                    @foreach ($section->rows as $row)
                                        <div class="row" style="{{ $row->getStyleAttributes() }}">
                                            @foreach ($row->columns as $column)
                                                <div class="{{ $column->getBootstrapClass() }}" style="{{ $column->getStyleAttributes() }}">
                                                    @foreach ($column->blocks as $block)
                                                        <div class="block mb-2" style="{{ $block->getStyleAttributes() }}">
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
                                                                       class="btn btn-primary"
                                                                       target="{{ $block->content['target'] ?? '_self' }}"
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
                                                                @case('divider')
                                                                    <hr style="border-color: {{ $block->content['color'] ?? '#cccccc' }}; border-style: {{ $block->content['style'] ?? 'solid' }};">
                                                                    @break
                                                                @case('html')
                                                                    {!! $block->content['html'] ?? '' !!}
                                                                    @break
                                                            @endswitch
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @elseif($page->content)
                            <div class="content">
                                {!! is_array($page->content) ? $page->content['en'] ?? '' : $page->content !!}
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="ti ti-layout-grid fs-2 mb-2"></i>
                                <p class="mt-2">No content added yet. <a href="{{ route('admin.cms.page-builder.builder', $page) }}">Start building your page</a></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounceInWord {
            0% { opacity: 0; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.1); }
            100% { opacity: 1; transform: scale(1); }
        }

        @keyframes zoomInWord {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
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
    </style>

    <script>
        function initWordLetterAnimations() {
            document.querySelectorAll('.animated-text-wordByWord').forEach(function(element) {
                const text = element.getAttribute('data-text') || '';
                const delay = element.getAttribute('data-delay') || '0.3s';
                const effect = element.getAttribute('data-effect') || 'fade';
                element.innerHTML = '';
                const words = text.split(' ');
                words.forEach(function(word, index) {
                    const wordSpan = document.createElement('span');
                    wordSpan.className = 'word';
                    wordSpan.textContent = word + (index < words.length - 1 ? ' ' : '');
                    const delayMs = parseFloat(delay) * 1000;
                    wordSpan.style.animationDelay = (index * delayMs) + 'ms';
                    element.appendChild(wordSpan);
                });
                setTimeout(function() {
                    element.querySelectorAll('.word').forEach(function(word) {
                        word.classList.add(effect);
                    });
                }, 100);
            });

            document.querySelectorAll('.animated-text-letterByLetter').forEach(function(element) {
                const text = element.getAttribute('data-text') || '';
                const delay = element.getAttribute('data-delay') || '0.1s';
                const effect = element.getAttribute('data-effect') || 'fade';
                element.innerHTML = '';
                const characters = text.split('');
                characters.forEach(function(char, index) {
                    const letterSpan = document.createElement('span');
                    letterSpan.className = 'letter';
                    letterSpan.textContent = char;
                    const delayMs = parseFloat(delay) * 1000;
                    letterSpan.style.animationDelay = (index * delayMs) + 'ms';
                    element.appendChild(letterSpan);
                });
                setTimeout(function() {
                    element.querySelectorAll('.letter').forEach(function(letter) {
                        letter.classList.add(effect);
                    });
                }, 100);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initWordLetterAnimations();
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initWordLetterAnimations);
        } else {
            initWordLetterAnimations();
        }
    </script>
</div>
