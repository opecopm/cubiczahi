# WordPress-Style Media Library - Setup & Usage

## Overview

The **Media Picker** is a WordPress-style modal that allows you to:

1. ✅ **Browse** all uploaded media in your library
2. ✅ **Search** by name, title, alt text, or tags
3. ✅ **Filter** by type (images, videos, documents, etc.)
4. ✅ **Select** one or multiple items
5. ✅ **Upload** new files directly to the library
6. ✅ **Link** any media to any entity (items, variants, products, etc.)

## How It Works

```
┌─────────────────────────────────────────────────────┐
│  Media Library Modal                                │
│  ┌───────────────────────────────────────────────┐ │
│  │ 🔍 Search...  │ Type: All ▼ │ Sort: Date ▼   │ │
│  └───────────────────────────────────────────────┘ │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ │
│  │     │ │  ✓  │ │     │ │     │ │     │ │     │ │
│  │ IMG │ │ IMG │ │ IMG │ │ VID │ │ DOC │ │ IMG │ │
│  │     │ │     │ │     │ │     │ │     │ │     │ │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ │
│                                                      │
│  ┌───────────────────────────────────────────────┐ │
│  │ 📁 Upload New Media                           │ │
│  │ [Choose Files] [Upload]                       │ │
│  └───────────────────────────────────────────────┘ │
│                                                      │
│           [Cancel]  [Select Items (3)]              │
└─────────────────────────────────────────────────────┘
```

## Quick Setup

### Step 1: Add MediaPicker Component to Layout

Make sure Livewire components are properly discovered. Run:

```bash
php artisan livewire:discover
```

### Step 2: Start Using

Go to **Items → Edit → Step 3 (Images)** and you'll see a new **"Media Library (Browse All)"** button:

```
┌──────────────────────────────────────────────────────┐
│  [Upload Image]                                     │
│                                                      │
│  ┌────────────────────────────────────────────────┐ │
│  │  Image 1  │  Image 2  │  Image 3  │  Image 4   │ │
│  └────────────────────────────────────────────────┘ │
│                                                      │
│  ─────────────── or ───────────────                 │
│                                                      │
│  [Media Library (Browse All)] ← NEW!                 │
└──────────────────────────────────────────────────────┘
```

## How to Use

### Option 1: Upload New Images

1. Click **"Upload Image"** button
2. Select file(s) from your computer
3. Images are uploaded and displayed

### Option 2: Browse Media Library

1. Click **"Media Library (Browse All)"** button
2. A modal opens showing ALL images in your media library
3. **Search** by name, title, or tags
4. **Filter** by type (Images, Videos, Documents)
5. **Sort** by Date (newest/oldest) or Name
6. **Click** on images to select them (✓ appears)
7. Click **"Select Items"** to link them

## Benefits Over Previous Approach

| Feature | Before | Now (WordPress-Style) |
|---------|--------|------------------------|
| Link to variants | Only same item's images | ANY image from library |
| Search | None | Full text search |
| Filter | None | By type |
| Browse | Not available | All media accessible |
| Reuse images | Limited | Unlimited reuse |

## Architecture

### Components Involved

1. **`MediaPicker.php`** - Main picker modal component
2. **`MediaManager.php`** - Integration with entity management
3. **`MediaAsset`** - Model for uploaded files
4. **`MediaLink`** - Polymorphic link table

### Database Flow

```
MediaAsset (single source of truth)
    │
    └── MediaLink (polymorphic)
            │
            ├── Item #1 ──→ Product image
            ├── Item #2 ──→ Same image reused!
            ├── Variant #3 ──→ Another reuse
            └── Product #5 ──→ Yet another use
```

**Same image file, unlimited reuse!**

## Integration with MediaGallery Module

The MediaPicker uses your existing **MediaGallery module**:

- Reads from `media_assets` table
- Uses Spatie Media Library for storage
- Creates `media_links` for relationships
- Leverages existing MediaAsset model

## Features

### Search
- Search by name, title, alt text, or tags
- Real-time filtering as you type
- Debounced for performance

### Filters
- **Type**: All, Images, Videos, Audio, Documents
- **Sort**: Date (newest/oldest), Name (A-Z/Z-A), Size
- **Per Page**: 12, 24, 48, 96 items

### Selection
- **Single mode**: Click replaces selection
- **Multiple mode**: Click toggles selection
- **Max selection**: Optional limit (configurable)
- **Select All**: One-click select all visible

### Upload
- Drag & drop or click to select
- Multiple file upload
- Auto-detect file type
- Progress indicator

## Configuration

### In MediaManager

```php
@livewire('media-manager', [
    'entityId' => $item->id,
    'entityType' => 'item',
    'mediaType' => 'image',
    // WordPress-style picker is always available
])
```

### Direct MediaPicker Usage

```php
@livewire('media-gallery::media-picker', [
    'allowedTypes' => ['image'],
    'multiple' => true,
    'maxSelection' => 10,
    'buttonText' => 'Select from Library',
    'usage' => 'gallery',
])
```

## Events

When media is selected, these events are dispatched:

```javascript
// Listen in your component
window.addEventListener('mediaSelected', (event) => {
    const { media, mediaIds, usage, entityType, entityId } = event.detail;
    console.log('Selected:', media);
    console.log('IDs:', mediaIds);
});
```

## Example Workflow

### Scenario: Reuse Same Image for Multiple Products

**Before (Old Way):**
1. Upload image to Product A
2. Can't easily reuse for Product B
3. Have to upload again

**After (WordPress-Style):**
1. Upload image once to Media Library
2. Go to Product A → Media Library → Select image → Link
3. Go to Product B → Media Library → Select same image → Link
4. Same file, two products, instant linking!

### Scenario: Organize Product Photos

1. Upload all product photos to Media Library
2. Tag them appropriately (e.g., "front-view", "side-view", "detail")
3. When editing any product, search by tag
4. Select and link needed images
5. Same library, instant access everywhere

## Troubleshooting

### Media Picker button not working

**Solution:**
1. Run: `php artisan livewire:discover`
2. Clear cache: `php artisan cache:clear`
3. Check console for JavaScript errors

### No media showing in picker

**Possible causes:**
- No media uploaded yet
- All media has `status` = 'inactive'
- Media has `visibility` = 'private'

**Solution:**
1. Upload some images first
2. Check MediaAsset model scopes
3. Verify visibility settings

### Upload not working

**Check:**
1. PHP `upload_max_filesize` in php.ini
2. Laravel's `max_file_size` in config
3. Storage permissions: `php artisan storage:link`

## Best Practices

✅ **DO:**
- Upload high-quality images once to library
- Use meaningful names and titles
- Add tags for easy search
- Reuse images across products/variants

❌ **DON'T:**
- Upload same image multiple times
- Use generic names like "image1.jpg"
- Delete media that is linked elsewhere

## Performance

- **Lazy loading**: Media loads paginated (12-96 per page)
- **Debounced search**: 300ms delay to reduce queries
- **Optimized thumbnails**: Uses Spatie's conversion presets
- **Eager loading**: Media links are efficiently queried

## Future Enhancements

Planned features:
- [ ] Drag-drop reordering
- [ ] Bulk linking
- [ ] Image editing (crop, resize)
- [ ] Folder organization
- [ ] Favorite/starred media
- [ ] Recent media quick access

## Support

For issues:
1. Check `storage/logs/laravel.log` for errors
2. Verify database migrations ran: `php artisan migrate:status`
3. Check MediaAsset model has correct fillable fields
4. Ensure Spatie Media Library is configured
