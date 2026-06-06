<!-- Block Settings -->
<div class="block-settings">
    <h6 class="mb-3">Block Settings</h6>

    @if ($blockType === 'text')
        <!-- Text Block Settings -->
        <div class="settings-field mb-3">
            <label class="form-label">Text Content</label>

            <!-- WordPress-style Editor Tabs -->
            <div class="wp-editor-tabs mb-2">
                <button type="button" class="wp-editor-tab active" id="visualTab" onclick="switchEditorMode('visual')">
                    Visual
                </button>
                <button type="button" class="wp-editor-tab" id="textTab" onclick="switchEditorMode('text')">
                    Text
                </button>
            </div>

            <!-- Visual Editor (WYSIWYG) -->
            <div id="visualEditor" class="editor-container">
                <div wire:ignore>
                    <div id="text-editor" style="min-height: 200px; border: 1px solid #ddd;"></div>
                </div>
            </div>

            <!-- Text Editor (HTML) -->
            <div id="textEditor" class="editor-container" style="display: none;">
                <textarea id="html-editor"
                          class="form-control"
                          rows="10"
                          style="font-family: 'Courier New', monospace; font-size: 12px;"
                          wire:model.live="content.text"
                          placeholder="Enter HTML content here..."></textarea>
            </div>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Text Color</label>
            <input type="color" class="form-control form-control-color"
                   wire:model.live="settings.text_color">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Font Size (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.font_size">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Text Alignment</label>
            <select class="form-control" wire:model.live="settings.text_align">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="justify">Justify</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Font Weight</label>
            <select class="form-control" wire:model.live="settings.font_weight">
                <option value="normal">Normal</option>
                <option value="bold">Bold</option>
                <option value="lighter">Lighter</option>
                <option value="bolder">Bolder</option>
            </select>
        </div>

    @elseif ($blockType === 'heading')
        <!-- Heading Block Settings -->
        <div class="settings-field mb-3">
            <label class="form-label">Heading Text</label>
            <input type="text" class="form-control"
                   wire:model.live="content.text"
                   placeholder="Enter your heading text">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Heading Level</label>
            <select class="form-control" wire:model.live="content.level">
                <option value="h1">H1 (Largest)</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
                <option value="h4">H4</option>
                <option value="h5">H5</option>
                <option value="h6">H6 (Smallest)</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Text Alignment</label>
            <select class="form-control" wire:model.live="content.alignment">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Text Color</label>
            <input type="color" class="form-control form-control-color"
                   wire:model.live="settings.text_color">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Font Size (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.font_size"
                   min="12" max="72">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Font Weight</label>
            <select class="form-control" wire:model.live="settings.font_weight">
                <option value="normal">Normal</option>
                <option value="bold">Bold</option>
                <option value="lighter">Lighter</option>
                <option value="bolder">Bolder</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="400">400</option>
                <option value="500">500</option>
                <option value="600">600</option>
                <option value="700">700</option>
                <option value="800">800</option>
                <option value="900">900</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Margin Top (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.margin_top"
                   min="0" max="100">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Margin Bottom (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.margin_bottom"
                   min="0" max="100">
        </div>

        <!-- Responsive Font Sizes -->
        <div class="settings-field mb-3">
            <label class="form-label">Responsive Font Sizes</label>
            <div class="row">
                <div class="col-4">
                    <label class="form-label small">Mobile</label>
                    <input type="number" class="form-control form-control-sm"
                           wire:model.live="settings.responsive_font_sizes.mobile"
                           min="12" max="48">
                </div>
                <div class="col-4">
                    <label class="form-label small">Tablet</label>
                    <input type="number" class="form-control form-control-sm"
                           wire:model.live="settings.responsive_font_sizes.tablet"
                           min="12" max="56">
                </div>
                <div class="col-4">
                    <label class="form-label small">Desktop</label>
                    <input type="number" class="form-control form-control-sm"
                           wire:model.live="settings.responsive_font_sizes.desktop"
                           min="12" max="72">
                </div>
            </div>
        </div>

    @elseif ($blockType === 'animated-heading')
        <!-- Animated Heading Block Settings -->
        <div class="settings-field mb-3">
            <label class="form-label">Heading Text</label>
            <input type="text" class="form-control"
                   wire:model.live="content.text"
                   placeholder="Enter your animated heading text">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Heading Level</label>
            <select class="form-control" wire:model.live="content.level">
                <option value="h1">H1 (Largest)</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
                <option value="h4">H4</option>
                <option value="h5">H5</option>
                <option value="h6">H6 (Smallest)</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Text Alignment</label>
            <select class="form-control" wire:model.live="content.alignment">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Animation Type</label>
            <select class="form-control" wire:model.live="content.animation_type">
                <option value="fadeIn">Fade In</option>
                <option value="slideInUp">Slide In Up</option>
                <option value="slideInDown">Slide In Down</option>
                <option value="slideInLeft">Slide In Left</option>
                <option value="slideInRight">Slide In Right</option>
                <option value="zoomIn">Zoom In</option>
                <option value="bounceIn">Bounce In</option>
                <option value="pulse">Pulse</option>
                <option value="wordByWord">Word by Word</option>
                <option value="letterByLetter">Letter by Letter</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Animation Duration</label>
            <select class="form-control" wire:model.live="content.animation_duration">
                <option value="0.5s">0.5s (Fast)</option>
                <option value="1s">1s (Normal)</option>
                <option value="1.5s">1.5s (Slow)</option>
                <option value="2s">2s (Very Slow)</option>
                <option value="3s">3s (Ultra Slow)</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Animation Delay</label>
            <select class="form-control" wire:model.live="content.animation_delay">
                <option value="0s">No Delay</option>
                <option value="0.2s">0.2s</option>
                <option value="0.5s">0.5s</option>
                <option value="1s">1s</option>
                <option value="1.5s">1.5s</option>
                <option value="2s">2s</option>
            </select>
        </div>

        <!-- Word/Letter Animation Settings -->
        <div class="settings-field mb-3" style="display: {{ in_array($content['animation_type'] ?? '', ['wordByWord', 'letterByLetter']) ? 'block' : 'none' }}">
            <label class="form-label">Word/Letter Delay</label>
            <select class="form-control" wire:model.live="content.word_letter_delay">
                <option value="0.1s">0.1s (Very Fast)</option>
                <option value="0.2s">0.2s (Fast)</option>
                <option value="0.3s">0.3s (Normal)</option>
                <option value="0.5s">0.5s (Slow)</option>
                <option value="0.8s">0.8s (Very Slow)</option>
                <option value="1s">1s (Ultra Slow)</option>
            </select>
        </div>

        <div class="settings-field mb-3" style="display: {{ in_array($content['animation_type'] ?? '', ['wordByWord', 'letterByLetter']) ? 'block' : 'none' }}">
            <label class="form-label">Animation Effect</label>
            <select class="form-control" wire:model.live="content.word_letter_effect">
                <option value="fade">Fade In</option>
                <option value="slide">Slide In</option>
                <option value="bounce">Bounce In</option>
                <option value="zoom">Zoom In</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Text Color</label>
            <input type="color" class="form-control form-control-color"
                   wire:model.live="settings.text_color">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Font Size (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.font_size"
                   min="12" max="72">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Font Weight</label>
            <select class="form-control" wire:model.live="settings.font_weight">
                <option value="normal">Normal</option>
                <option value="bold">Bold</option>
                <option value="lighter">Lighter</option>
                <option value="bolder">Bolder</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="400">400</option>
                <option value="500">500</option>
                <option value="600">600</option>
                <option value="700">700</option>
                <option value="800">800</option>
                <option value="900">900</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Margin Top (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.margin_top"
                   min="0" max="100">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Margin Bottom (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.margin_bottom"
                   min="0" max="100">
        </div>

        <!-- Animation Settings -->
        <div class="settings-field mb-3">
            <label class="form-label">Animation Direction</label>
            <select class="form-control" wire:model.live="settings.animation_settings.direction">
                <option value="normal">Normal</option>
                <option value="reverse">Reverse</option>
                <option value="alternate">Alternate</option>
                <option value="alternate-reverse">Alternate Reverse</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Animation Fill Mode</label>
            <select class="form-control" wire:model.live="settings.animation_settings.fill_mode">
                <option value="none">None</option>
                <option value="forwards">Forwards</option>
                <option value="backwards">Backwards</option>
                <option value="both">Both</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Animation Loop</label>
            <select class="form-control" wire:model.live="settings.animation_settings.loop">
                <option value="false">Normal (Play Once)</option>
                <option value="true">Loop (Repeat Continuously)</option>
            </select>
        </div>

        <!-- Responsive Font Sizes -->
        <div class="settings-field mb-3">
            <label class="form-label">Responsive Font Sizes</label>
            <div class="row">
                <div class="col-4">
                    <label class="form-label small">Mobile</label>
                    <input type="number" class="form-control form-control-sm"
                           wire:model.live="settings.responsive_font_sizes.mobile"
                           min="12" max="48">
                </div>
                <div class="col-4">
                    <label class="form-label small">Tablet</label>
                    <input type="number" class="form-control form-control-sm"
                           wire:model.live="settings.responsive_font_sizes.tablet"
                           min="12" max="56">
                </div>
                <div class="col-4">
                    <label class="form-label small">Desktop</label>
                    <input type="number" class="form-control form-control-sm"
                           wire:model.live="settings.responsive_font_sizes.desktop"
                           min="12" max="72">
                </div>
            </div>
        </div>

    @elseif ($blockType === 'image')
        <!-- Image Block Settings -->
        <div class="settings-field mb-3">
            <label class="form-label">Upload Image from Computer</label>
            <input type="file" class="form-control"
                   wire:model.live="imageFile"
                   accept="image/*">
            @if ($imageFile)
                <div class="mt-2">
                    <small class="text-success">File selected: {{ $imageFile->getClientOriginalName() }}</small>
                    @if($uploading)
                        <div class="mt-2">
                            <small class="text-info">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Uploading...
                            </small>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">OR Enter Image URL</label>
            <input type="url" class="form-control"
                   wire:model.live="content.image_url"
                   placeholder="https://example.com/image.jpg">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Alt Text</label>
            <input type="text" class="form-control"
                   wire:model.live="content.alt_text"
                   placeholder="Alternative text for image">
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Image Width</label>
            <select class="form-control" wire:model.live="settings.width">
                <option value="100%">Full Width</option>
                <option value="75%">75% Width</option>
                <option value="50%">50% Width</option>
                <option value="25%">25% Width</option>
                <option value="auto">Auto</option>
            </select>
        </div>

        <div class="settings-field mb-3">
            <label class="form-label">Image Alignment</label>
            <select class="form-control" wire:model.live="settings.alignment">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
            </select>
        </div>

        @if(isset($content['image_url']) && $content['image_url'])
            <div class="settings-field mb-3">
                <label class="form-label">Image Preview</label>
                <div class="image-preview-container">
                    <img src="{{ $content['image_url'] }}"
                         alt="{{ $content['alt_text'] ?? 'Preview' }}"
                         class="img-fluid rounded border"
                         style="max-width: 200px; max-height: 150px; object-fit: cover;">
                </div>
            </div>
        @endif

    @elseif ($blockType === 'button')
        <!-- Button Block Settings -->
        <div class="mb-3">
            <label class="form-label">Button Text</label>
            <input type="text" class="form-control"
                   wire:model.live="content.button_text">
        </div>

        <div class="mb-3">
            <label class="form-label">Button URL</label>
            <input type="url" class="form-control"
                   wire:model.live="content.button_url">
        </div>

        <div class="mb-3">
            <label class="form-label">Target</label>
            <select class="form-control" wire:model.live="content.target">
                <option value="_self">Same Window</option>
                <option value="_blank">New Window</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Background Color</label>
            <input type="color" class="form-control form-control-color"
                   wire:model.live="settings.background_color">
        </div>

        <div class="mb-3">
            <label class="form-label">Text Color</label>
            <input type="color" class="form-control form-control-color"
                   wire:model.live="settings.text_color">
        </div>

        <div class="mb-3">
            <label class="form-label">Border Radius (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.border_radius">
        </div>

        <div class="mb-3">
            <label class="form-label">Padding (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="settings.padding">
        </div>

    @elseif ($blockType === 'video')
        <!-- Video Block Settings -->
        <div class="mb-3">
            <label class="form-label">Video URL</label>
            <input type="url" class="form-control"
                   wire:model.live="content.video_url"
                   placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <div class="mb-3">
            <label class="form-label">Video Type</label>
            <select class="form-control" wire:model.live="content.video_type">
                <option value="youtube">YouTube</option>
                <option value="vimeo">Vimeo</option>
                <option value="embed">Embed Code</option>
            </select>
        </div>

    @elseif ($blockType === 'spacer')
        <!-- Spacer Block Settings -->
        <div class="mb-3">
            <label class="form-label">Height (px)</label>
            <input type="number" class="form-control"
                   wire:model.live="content.height">
        </div>

    @elseif ($blockType === 'divider')
        <!-- Divider Block Settings -->
        <div class="mb-3">
            <label class="form-label">Line Style</label>
            <select class="form-control" wire:model.live="content.style">
                <option value="solid">Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
                <option value="double">Double</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Line Color</label>
            <input type="color" class="form-control form-control-color"
                   wire:model.live="content.color">
        </div>

    @elseif ($blockType === 'html')
        <!-- HTML Block Settings -->
        <div class="mb-3">
            <label class="form-label">HTML Code</label>
            <textarea class="form-control" rows="8"
                      wire:model.live="content.html"
                      placeholder="<!-- Enter your HTML code here -->"></textarea>
        </div>
    @endif

    <!-- Common Block Settings -->
    <hr class="my-3">
    <h6 class="mb-3">Common Settings</h6>

    <div class="settings-field mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" class="form-control"
               wire:model.live="sort_order">
    </div>

    <div class="settings-field mb-3">
        <label class="form-label">Custom CSS Classes</label>
        <input type="text" class="form-control"
               wire:model.live="css_classes"
               placeholder="custom-class another-class">
    </div>

    <div class="settings-field mb-3">
        <label class="form-label">Custom CSS</label>
        <textarea class="form-control" rows="4"
                  wire:model.live="custom_css"
                  placeholder="/* Custom CSS */"></textarea>
    </div>

    <div class="d-flex gap-2">
        <button wire:click="updateElementSettings" class="btn btn-primary btn-sm">
            <i class="ti ti-device-floppy me-1"></i>
            Save Settings
        </button>
        <button wire:click="duplicateBlock({{ $selectedElement }})" class="btn btn-warning btn-sm">
            <i class="ti ti-copy me-1"></i>
            Duplicate
        </button>
    </div>
</div>
