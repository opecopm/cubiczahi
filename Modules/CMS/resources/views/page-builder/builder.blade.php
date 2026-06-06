@extends('admin.layouts.app')
@section('subnav')
@include('cms::livewire.partials.cms-nav-tabs')
@endsection

@section('head')
<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endsection

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Page Builder</a></li>
        <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
            {{ $page->name ?? $page->title ?? 'Page Builder' }}
        </li>
    </ol>
</nav>
@endsection

@section('content')
@livewire('cms::page-builder.builder', ['id' => $id])
@endsection

@push('js')
<!-- Quill.js JavaScript -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let quill = null;
    
    console.log('DOM loaded, initializing...');
    
    // Initialize Livewire
    document.addEventListener('livewire:init', function() {
        console.log('Livewire initialized successfully');
    });
    
    // Initialize Quill editor when settings panel opens
    Livewire.on('settingsOpened', () => {
        console.log('Settings opened, initializing Quill...');
        setTimeout(() => {
            const editorElement = document.getElementById('text-editor');
            if (editorElement && !quill) {
                try {
                    console.log('Creating Quill editor...');
                    quill = new Quill('#text-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'align': [] }],
                                ['link', 'image'],
                                ['clean']
                            ]
                        }
                    });
                    
                    console.log('Quill editor created successfully');
                    
                    // Load existing content from Livewire component
                    const livewireElement = document.querySelector('[wire\\:id]');
                    if (livewireElement) {
                        const livewireComponent = Livewire.find(livewireElement.getAttribute('wire:id'));
                        if (livewireComponent && livewireComponent.get('content.text')) {
                            quill.root.innerHTML = livewireComponent.get('content.text');
                        }
                    }
                    
                    // Save content when editor changes
                    quill.on('text-change', function() {
                        const livewireElement = document.querySelector('[wire\\:id]');
                        if (livewireElement) {
                            const livewireComponent = Livewire.find(livewireElement.getAttribute('wire:id'));
                            if (livewireComponent) {
                                livewireComponent.call('updateTextContent', quill.root.innerHTML);
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error initializing Quill:', error);
                }
            } else if (!editorElement) {
                console.log('Text editor element not found');
            } else if (quill) {
                console.log('Quill already initialized');
            }
        }, 200);
    });
    
    // Clean up when settings panel closes
    Livewire.on('settingsClosed', () => {
        console.log('Settings closed, cleaning up Quill...');
        if (quill) {
            quill = null;
        }
    });
    
    // Sync HTML editor changes with Livewire
    document.addEventListener('input', function(e) {
        if (e.target.id === 'html-editor') {
            const livewireElement = document.querySelector('[wire\\:id]');
            if (livewireElement) {
                const livewireComponent = Livewire.find(livewireElement.getAttribute('wire:id'));
                if (livewireComponent) {
                    livewireComponent.call('updateTextContent', e.target.value);
                }
            }
        }
    });
    
    // Make functions globally available
    window.switchEditorMode = switchEditorMode;
});
</script>
@endpush
