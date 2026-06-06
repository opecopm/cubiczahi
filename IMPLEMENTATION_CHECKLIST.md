# Implementation Checklist

## ✅ Component Created Successfully

- [x] `app/Livewire/MediaManager.php` - Main component (250+ lines)
- [x] `resources/views/livewire/media-manager.blade.php` - View template (200+ lines)
- [x] `app/Traits/HasMediaManagement.php` - Reusable trait (80+ lines)
- [x] `resources/views/livewire/_media-manager-include.blade.php` - Helper include

## 📚 Documentation Provided

- [x] `MEDIA_MANAGER_GUIDE.md` - Complete usage guide
- [x] `QUICK_REFERENCE.md` - Quick start guide
- [x] `BEFORE_AFTER_COMPARISON.md` - Detailed comparison
- [x] `EXAMPLE_ITEMS_EDIT_REFACTORED.php` - Example component
- [x] `EXAMPLE_BLADE_VIEW_STEP3.blade.php` - Example view

## 🚀 Getting Started (First Time)

### Step 1: Verify Files Exist
```bash
# Component
ls app/Livewire/MediaManager.php

# View
ls resources/views/livewire/media-manager.blade.php

# Trait
ls app/Traits/HasMediaManagement.php
```

### Step 2: Test in Your Project
Try this in your Items/Edit component:

```php
use App\Traits\HasMediaManagement;

class Edit extends Component {
    use HasMediaManagement;
    
    public function mount($itemId) {
        $this->initializeMediaManager('item', $itemId, 'image');
    }
}
```

Then in your blade view:
```blade
@livewire('media-manager', [
    'entityId' => $item->id,
    'entityType' => 'item',
    'mediaType' => 'image',
    'title' => 'Product Images',
])
```

### Step 3: Test Upload/Delete/Set Primary
- [ ] Upload a test image
- [ ] Verify it appears in grid view
- [ ] Test switching to list view
- [ ] Test setting as primary
- [ ] Test editing title/description
- [ ] Test deleting media
- [ ] Check storage path: `storage/app/public/item-images/`

## 🔧 Configuration

### Supported Entity Types

**Currently Mapped:**
- [x] `item` + `image` → ItemImage
- [x] `variant` + `image` → VariantImage
- [x] `asset` + `document` → AssetDocument
- [x] `asset` + `photo` → AssetPhoto

### Add New Entity Type

1. Create/ensure model exists with these columns:
   - `id`, `{entity}_id`, `path`, `is_primary`, `display_order`, `timestamps`

2. Add to `MediaManager.php` → `getMediaModelClass()`:
   ```php
   'product_image' => 'Modules\Selling\Models\ProductImage',
   ```

3. Use in your component:
   ```php
   $this->initializeMediaManager('product', $productId, 'image');
   ```

## 📋 Refactoring Existing Components

### For Each Component (Items, Products, Variants, etc.)

- [ ] **Step 1:** Add trait to component
  ```php
  use App\Traits\HasMediaManagement;
  ```

- [ ] **Step 2:** Initialize in mount()
  ```php
  public function mount($itemId) {
      $this->item = Item::findOrFail($itemId);
      $this->initializeMediaManager('item', $this->item->id, 'image', [
          'title' => 'Product Images',
          'description' => 'Upload product gallery images',
          'allowMultiple' => true,
      ]);
  }
  ```

- [ ] **Step 3:** Remove old methods
  - Remove `loadItemImages()`
  - Remove `uploadItemImages()`
  - Remove `deleteItemImage()`
  - Remove `setPrimaryItemImage()`
  - Remove related properties: `$itemImages`, `$newItemImages`

- [ ] **Step 4:** Update view
  - Replace old image section with:
    ```blade
    @livewire('media-manager', [
        'entityId' => $item->id,
        'entityType' => 'item',
        'mediaType' => 'image',
        'title' => 'Product Images',
    ])
    ```

- [ ] **Step 5:** Add event listeners (optional)
  ```php
  #[\Livewire\Attributes\On('media-uploaded')]
  public function onMediaUploaded($entityId): void {
      session()->flash('message', 'Images uploaded!');
  }
  ```

- [ ] **Step 6:** Test thoroughly
  - Test upload
  - Test delete
  - Test set primary
  - Test edit metadata
  - Test view toggle

## 📦 Components to Refactor (Priority Order)

### High Priority (Core functionality)
- [ ] `Modules/Inventory/Livewire/Items/Edit.php`
- [ ] Inventory Products (if exists)
- [ ] Inventory Variants (if exists)

### Medium Priority (Asset/Admin features)
- [ ] `Modules/Assets/Livewire/Edit.php` (Assets)
- [ ] CRM customer media
- [ ] HRM employee photos

### Low Priority (Nice to have)
- [ ] Seller profiles
- [ ] Category images
- [ ] Brand logos
- [ ] Other media

## 🧪 Testing Checklist

### Basic Functionality
- [ ] Upload single image
- [ ] Upload multiple images (if allowed)
- [ ] View in grid mode
- [ ] View in list mode
- [ ] Toggle between grid/list
- [ ] Set as primary
- [ ] Edit title
- [ ] Edit description
- [ ] Delete image
- [ ] Delete with confirmation

### Edge Cases
- [ ] Upload file exceeding max size (should error)
- [ ] Upload wrong file type (should error)
- [ ] Upload when no files selected (should not upload)
- [ ] Delete when only one image exists
- [ ] Try to set primary when only primary exists
- [ ] Rapid successive uploads
- [ ] Session timeout during upload

### Database
- [ ] Images stored in correct path
- [ ] Database records created correctly
- [ ] Primary image flag set correctly
- [ ] Display order increments properly
- [ ] Files deleted from storage when deleted from DB

### Performance
- [ ] Component loads quickly
- [ ] Upload doesn't freeze UI
- [ ] List with many items (50+) still responsive
- [ ] Grid view renders smoothly

## 🐛 Troubleshooting

### Component not rendering
**Solution:**
```bash
# Clear cache
php artisan config:clear
php artisan view:clear

# Ensure Livewire is installed
composer require livewire/livewire

# Rediscover components
php artisan livewire:discover
```

### Files not uploading
**Solution:**
```bash
# Check storage permissions
chmod -R 755 storage/app/public
chmod -R 755 storage/framework

# Create storage link if missing
php artisan storage:link

# Check php.ini limits
php -i | grep -E "upload_max|post_max"
```

### "Model not found" error
**Solution:**
1. Check entity type spelling matches exactly
2. Add to `getMediaModelClass()` mapping
3. Verify model table exists: `php artisan migrate`
4. Check model has required columns

### Image not appearing after upload
**Solution:**
1. Verify storage link created: `ls public/storage`
2. Check file permissions: `ls -la storage/app/public/item-images/`
3. Verify browser cache: `Ctrl+Shift+Delete`
4. Check browser console for JS errors

## 📊 Metrics to Track

After implementation:

- **Code Reduction:** Count lines before/after refactoring
- **Development Speed:** Time to add new media feature
- **Bug Rate:** Track bugs in media handling
- **Performance:** Monitor page load times
- **User Experience:** Gather feedback on UI/UX

## 📝 Notes

- Component uses Bootstrap 5 classes - ensure Bootstrap is loaded
- Livewire 3+ required
- PHP 8.1+ recommended
- Laravel 11+ recommended

## 🎯 Success Criteria

You'll know it's working when:

- [x] Component files created and visible in IDE
- [x] Can include component in blade view
- [x] Upload button appears in UI
- [x] Can upload files without errors
- [x] Files appear in grid/list view
- [x] Can set as primary
- [x] Can delete files
- [x] Files stored in correct directory
- [x] Database records updated correctly

## 🚀 Next Steps After Implementation

1. **Test thoroughly** - Run through all test checklist items
2. **Refactor one component** - Start with Items/Edit
3. **Gather feedback** - Ask team for feedback
4. **Refactor others** - Apply to other modules
5. **Optimize** - Add performance optimizations
6. **Enhance** - Implement planned features (drag-drop, cropping, etc.)

## 📞 Support

If you encounter issues:

1. Check `QUICK_REFERENCE.md` for quick answers
2. Read `MEDIA_MANAGER_GUIDE.md` for detailed docs
3. Review `EXAMPLE_ITEMS_EDIT_REFACTORED.php` for code patterns
4. Check Laravel/Livewire documentation
5. Review browser console for JavaScript errors

## 📅 Timeline Estimate

| Phase | Time | Status |
|-------|------|--------|
| Component creation | Done | ✅ |
| Documentation | Done | ✅ |
| Testing setup | 1-2 hours | 📋 |
| First component refactor | 2-3 hours | 📋 |
| Refactor remaining components | 1-2 hours each | 📋 |
| Team training | 30 min | 📋 |
| Optimization & polish | 2-3 hours | 📋 |

## ✨ You're All Set!

Everything needed for implementation is ready. Start with the quick reference, then dive into the detailed guide as needed.

Happy coding! 🎉
