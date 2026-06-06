{{-- 
  Media Manager Include Helper
  Use this in your Blade views to easily include the media manager component
  
  Example in your parent view:
  @include('livewire._media-manager-include', ['params' => [
      'entityType' => 'item',
      'entityId' => $item->id,
      'mediaType' => 'image',
      'title' => 'Product Images',
      'description' => 'Upload product images for the gallery'
  ]])
--}}

@livewire('media-manager', $params ?? [], key('media-manager-' . ($params['entityType'] ?? 'default') . '-' . ($params['entityId'] ?? 0)))
