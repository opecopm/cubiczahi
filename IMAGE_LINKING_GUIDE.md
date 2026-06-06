# Image Linking Feature - Setup & Usage

## Overview

The new **Image Linking** feature allows you to link previously uploaded product images directly to variants without re-uploading them. This saves time and storage when multiple variants use the same images.

## Installation

### Step 1: Run Migration

A migration has been created to add the `item_image_id` column to the `variant_images` table. Run it with:

```bash
php artisan migrate
```

This migration:
- Adds `item_image_id` column to `variant_images` table
- Creates a foreign key relationship to `item_images` table
- Allows tracking which product image each variant image uses

### Step 2: Verify Models

The following models have been updated:
- ✅ `ItemImage.php` - Added `variantImages()` relationship
- ✅ `VariantImage.php` - Added `item_image_id` to fillable and `itemImage()` relationship

## How to Use

### Upload Product Images First

1. Go to **Items → Edit**
2. Navigate to **Step 3 (Images)**
3. Upload product images to the main product gallery
4. These images are stored with the item

### Link Images to Variants

1. Still in **Step 3 (Images)**
2. Under each variant, you'll see a **"Link Existing Images"** section
3. This section shows all available images from the product gallery
4. **Check the images** you want to link to the variant
5. Click **"Link Selected"** button
6. The images appear in the variant's gallery (they reference the same file path)

### Result

- ✅ No need to re-upload the same image multiple times
- ✅ All variants can share the same image files
- ✅ Saves storage space
- ✅ Faster workflow

## Features

### Available Actions

| Action | Description |
|--------|-------------|
| **Link Images** | Select and link existing product images to a variant |
| **View Count** | Badge shows total available images |
| **Linked Badge** | Already linked images are marked and disabled |
| **Multiple Select** | Select multiple images at once |
| **Show/Hide** | Toggle the linking panel to keep UI clean |

### Status Indicators

| Indicator | Meaning |
|-----------|---------|
| **Green "Linked" Badge** | This image is already linked to this variant |
| **Checkbox Disabled** | Already linked - can't relink |
| **Opacity 0.6** | Already linked image (visual feedback) |

## Example Workflow

### Scenario: Product with 3 Variants

**Before (without linking):**
1. Upload 5 product images (for main product) - 5 files stored
2. Upload same 5 images for Red variant - 5 more files (total: 10)
3. Upload same 5 images for Blue variant - 5 more files (total: 15)
4. Upload same 5 images for Green variant - 5 more files (total: 20)
5. **Total storage: 20 image files**

**After (with linking):**
1. Upload 5 product images (for main product) - 5 files stored
2. Link those 5 images to Red variant - 0 new files (shared references)
3. Link those 5 images to Blue variant - 0 new files (shared references)
4. Link those 5 images to Green variant - 0 new files (shared references)
5. **Total storage: 5 image files** ✅ 75% savings!

## Database Structure

### variant_images Table

```sql
id                  bigint(20) PRIMARY
variant_id          bigint(20) FOREIGN (item_variants)
item_image_id       bigint(20) FOREIGN (item_images) -- NEW!
path                varchar(255)
alt_text            text
display_order       int(11)
is_primary          boolean
created_at          timestamp
updated_at          timestamp
```

The `item_image_id` column tracks which product image this variant image is linked to.

## API/Relationships

### ItemImage Model

```php
// Get all variants using this image
$itemImage->variantImages(); // Returns collection of VariantImage models
```

### VariantImage Model

```php
// Get the source product image
$variantImage->itemImage(); // Returns ItemImage model
$variantImage->itemImage()->path; // Get the file path
```

## Technical Details

### How Linking Works

1. **Reference, Not Copy**: When you link an image, a new `VariantImage` record is created that references the `ItemImage` via `item_image_id`
2. **Same File Path**: Both records point to the same file in storage
3. **Independent Metadata**: Each `VariantImage` can have its own `is_primary` and `display_order`
4. **Cascade Delete**: If parent `ItemImage` is deleted, all linked `VariantImage` records are also deleted

### Storage Location

Files are stored at:
```
storage/app/public/item-images/{item_id}/{filename}
```

Both the main product and linked variants reference this same path.

## Limitations & Notes

### Can Only Link Within Same Item

- You can only link images that belong to the same product (item)
- Variants automatically show images from their parent item
- Cross-item linking is not supported (by design)

### Linked Images are Read-Only

- You cannot delete a variant image if it's linked to a product image
- Deleting a product image deletes all linked variant images
- This maintains referential integrity

### Display Order

- Each variant has independent display order
- Linked images can appear in different orders for different variants
- Use the interface to set primary image per variant

## Troubleshooting

### "Link Existing Images" section not showing

**Possible causes:**
- Migration not run yet (`php artisan migrate`)
- No product images uploaded yet
- Entity type is not "variant"

**Solution:**
1. Run migration: `php artisan migrate`
2. Upload product images first
3. Refresh the page

### "No images to link"

**Possible causes:**
- Product has no images yet
- All product images are already linked

**Solution:**
1. Upload more images to the product
2. Or unlink images you don't need and re-link them

### Migration errors

**If you get "table already exists" error:**

Your `variant_images` table already has `item_image_id`. Check with:
```bash
php artisan tinker
# In Tinker:
DB::select('SHOW COLUMNS FROM variant_images;')
```

**If migration fails:**
- Ensure your database connection is working
- Check file permissions on `database/migrations/`
- Run: `php artisan migrate:status`

## Best Practices

✅ **DO:**
- Upload high-quality images to the product once
- Link those images to all variants
- Use different display orders per variant if needed
- Keep product images as the "source of truth"

❌ **DON'T:**
- Upload the same image to both product AND variant separately
- Delete product images that are linked to variants (they cascade delete)
- Try to link images from different products (not supported)
- Rename files in storage after linking (breaks the reference)

## Performance Impact

- **Positive**: Reduced storage requirements (30-75% savings in typical scenarios)
- **Positive**: Faster upload times (link instead of upload)
- **Neutral**: Database queries are minimal (one foreign key lookup)
- **No negative impact** on page load or display

## Future Enhancements

Planned improvements:
- [ ] Bulk link all images with one click
- [ ] Copy variant's images to another variant
- [ ] Link images from other items (if needed)
- [ ] Drag-drop to reorder linked images
- [ ] Image cropping per variant

## Support

For issues or questions:
1. Check this guide first
2. Review the MEDIA_MANAGER_GUIDE.md
3. Check migration status: `php artisan migrate:status`
4. Review logs: `storage/logs/laravel.log`
