# Media Manager Component - Usage Guide

## Overview

The `MediaManager` is a reusable Livewire component that handles uploading, linking, and managing media (images, files) for any entity in your application. It's designed to work with products, variations, items, assets, and any other model.

## Features

- ✅ Upload single or multiple media files
- ✅ Support for images and documents
- ✅ Set primary/featured media
- ✅ Edit media metadata (title, description)
- ✅ Delete media
- ✅ Grid and List view modes
- ✅ Automatic storage management
- ✅ Easy integration with any Livewire component
- ✅ Events system for parent component communication
- ✅ Responsive design with Bootstrap 5

## Installation

The component is already installed in your Laravel project at:
- **Component**: `app/Livewire/MediaManager.php`
- **View**: `resources/views/livewire/media-manager.blade.php`
- **Trait**: `app/Traits/HasMediaManagement.php`

## Quick Start

### Basic Usage in Livewire Component

```php
// In your component class
class ProductEdit extends Component
{
    public int $productId;
    
    public function mount($productId)
    {
        $this->productId = $productId;
    }
}
```

```blade
<!-- In your view -->
@livewire('media-manager', [
    'entityId' => $productId,
    'entityType' => 'product',
    'mediaType' => 'image',
    'title' => 'Product Images',
    'description' => 'Upload product gallery images'
])
```

## Advanced Usage with Trait

For cleaner integration, use the `HasMediaManagement` trait:

```php
use App\Traits\HasMediaManagement;

class ItemEdit extends Component
{
    use HasMediaManagement;
    
    public function mount($itemId)
    {
        // Initialize media manager
        $this->initializeMediaManager('item', $itemId, 'image', [
            'title' => 'Item Images',
            'description' => 'Upload item images for the catalog',
            'allowMultiple' => true,
            'allowPrimary' => true,
            'acceptedFormats' => 'image/*',
            'maxFileSize' => 2048,
        ]);
    }
}
```

## Configuration Options

When creating the media manager, you can pass these parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `entityId` | `int\|string` | required | ID of the entity |
| `entityType` | `string` | required | Type of entity: 'item', 'variant', 'product', 'asset', etc. |
| `mediaType` | `string` | 'image' | Type of media: 'image', 'document', 'file' |
| `title` | `string` | null | Section title |
| `description` | `string` | null | Section description |
| `allowMultiple` | `bool` | true | Allow uploading multiple files at once |
| `acceptedFormats` | `string` | 'image/*' | File input accept attribute (e.g., 'image/*', '.pdf,.doc') |
| `maxFileSize` | `int` | 2048 | Maximum file size in KB |
| `allowPrimary` | `bool` | true | Show "Set as Primary" button |
| `allowReorder` | `bool` | false | Enable drag-drop reordering (future) |
| `allowLinking` | `bool` | false | Allow linking to existing media (future) |
| `viewMode` | `string` | 'grid' | Initial view: 'grid' or 'list' |

## Supported Entity/Media Type Combinations

The component automatically maps entity and media types to database models:

| Entity | Media Type | Model |
|--------|-----------|-------|
| item | image | `Modules\Inventory\Models\ItemImage` |
| variant | image | `Modules\Inventory\Models\VariantImage` |
| asset | document | `Modules\Assets\Models\AssetDocument` |
| asset | photo | `Modules\Assets\Models\AssetPhoto` |

**To add new combinations**, extend the `getMediaModelClass()` method in `MediaManager.php`.

## Events

The media manager dispatches events that you can listen for in your parent component:

```php
#[\Livewire\Attributes\On('media-uploaded')]
public function onMediaUploaded($entityId): void
{
    // Handle media uploaded
    $this->message = 'Media uploaded successfully!';
}

#[\Livewire\Attributes\On('media-deleted')]
public function onMediaDeleted($id): void
{
    // Handle media deleted
    $this->message = 'Media deleted successfully!';
}

#[\Livewire\Attributes\On('media-primary-changed')]
public function onMediaPrimaryChanged($id): void
{
    // Handle primary media changed
    $this->message = 'Primary media changed!';
}
```

## Database Schema Requirements

Ensure your media tables have these columns:

```php
// For image-based media
Schema::create('item_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('item_id')->constrained()->cascadeOnDelete();
    $table->string('path');
    $table->string('title')->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_primary')->default(false);
    $table->integer('display_order')->default(0);
    $table->timestamps();
});

// Similar structure for variant_images, asset_documents, asset_photos, etc.
```

## Real-World Example

### In Items/Edit.php Component

```php
namespace Modules\Inventory\Livewire\Items;

use App\Livewire\WithAutoComplete;
use App\Traits\HasMediaManagement;
use Livewire\Component;

class Edit extends Component
{
    use HasMediaManagement;
    
    public $item;
    public $step = 1;
    
    public function mount($itemId)
    {
        $this->item = Item::findOrFail($itemId);
        
        // Initialize media managers for images and documents
        $this->initializeMediaManager('item', $this->item->id, 'image', [
            'title' => 'Product Images',
            'description' => 'Upload images for the product gallery',
            'allowPrimary' => true,
        ]);
    }
    
    // Handle media uploaded
    #[\Livewire\Attributes\On('media-uploaded')]
    public function onMediaUploaded($entityId): void
    {
        session()->flash('message', 'Images uploaded successfully!');
    }
}
```

### In the Blade View

```blade
@if ($step == 3)
    <div class="tab-pane active show">
        @livewire('media-manager', [
            'entityId' => $item->id,
            'entityType' => 'item',
            'mediaType' => 'image',
            'title' => 'Product Images',
            'description' => 'Upload product images for the gallery',
            'allowMultiple' => true,
            'allowPrimary' => true,
        ])
    </div>
@endif
```

## Extending the Component

### Adding Support for New Entity Types

Edit the `getMediaModelClass()` method:

```php
protected function getMediaModelClass(): ?string
{
    $models = [
        'item_image' => 'Modules\Inventory\Models\ItemImage',
        'variant_image' => 'Modules\Inventory\Models\VariantImage',
        'product_image' => 'Modules\Selling\Models\ProductImage', // Add this
        'custom_document' => 'App\Models\CustomDocument', // Add this
    ];
    
    $key = "{$this->entityType}_{$this->mediaType}";
    return $models[$key] ?? null;
}
```

### Customizing Storage Paths

Override the storage logic in your component:

```php
// In your component
public function saveMediaToDatabase($file): void
{
    $path = $file->store("my-custom-path/{$this->entityId}", 'public');
    // ... rest of logic
}
```

## Replacing Existing Code

### From Items/Edit.php

**Before (old approach):**
```php
public function uploadItemImages(): void
{
    $this->validate(['newItemImages.*' => 'image|max:2048']);

    $order = ItemImage::where('item_id', $this->item->id)->max('display_order') ?? -1;
    foreach ($this->newItemImages as $file) {
        $path = $file->store("item-images/{$this->item->id}", 'public');
        ItemImage::create([
            'item_id' => $this->item->id,
            'path' => $path,
            'display_order' => ++$order,
            'is_primary' => $order === 0,
        ]);
    }
    $this->newItemImages = [];
    $this->loadItemImages();
}
```

**After (using MediaManager):**
```php
// Just use @livewire('media-manager', [...]) in your view!
// All upload, delete, reorder logic is handled automatically
```

## View/Template Updates

Replace your image upload sections with:

```blade
<!-- Old code -->
<!-- @include('inventory::livewire.items.partials.form-step3-images') -->

<!-- New code -->
@livewire('media-manager', [
    'entityId' => $item->id,
    'entityType' => 'item',
    'mediaType' => 'image',
    'title' => 'Product Images',
])
```

## File Upload Storage

Files are stored in:
- Path: `storage/app/public/{entityType}-{mediaType}s/{entityId}/`
- URL: `/storage/{entityType}-{mediaType}s/{entityId}/{filename}`

Example: `/storage/item-images/5/product_photo.jpg`

## Troubleshooting

### Component Not Rendering
- Ensure Livewire is properly configured
- Check that the view file exists at `resources/views/livewire/media-manager.blade.php`
- Clear config cache: `php artisan config:clear`

### Files Not Uploading
- Check file size limits in `php.ini` and `.env`
- Verify storage permissions: `php artisan storage:link`
- Check validation rules match your requirements

### Model Not Found
- Add the model to `getMediaModelClass()` method
- Ensure the model table exists and has required columns

## Best Practices

1. **Always use the trait for consistency**
   ```php
   use HasMediaManagement;
   ```

2. **Initialize in mount() method**
   ```php
   public function mount($itemId)
   {
       $this->initializeMediaManager('item', $itemId, 'image');
   }
   ```

3. **Handle events in parent component**
   ```php
   #[\Livewire\Attributes\On('media-uploaded')]
   public function onMediaUploaded($entityId): void {}
   ```

4. **Use meaningful titles and descriptions**
   ```php
   'title' => 'Product Gallery Images',
   'description' => 'Upload high-quality images (max 2MB each)'
   ```

## Support for Multiple Media Types

You can use multiple media managers in one component:

```blade
@livewire('media-manager', ['entityId' => $item->id, 'entityType' => 'item', 'mediaType' => 'image', 'title' => 'Images'])
@livewire('media-manager', ['entityId' => $item->id, 'entityType' => 'item', 'mediaType' => 'document', 'title' => 'Documents'])
```

## Future Enhancements

Planned features:
- [ ] Drag-drop reordering
- [ ] Link to existing media from library
- [ ] Batch operations (delete multiple)
- [ ] Image cropping and filters
- [ ] Direct URL input for external media
- [ ] Media library integration
