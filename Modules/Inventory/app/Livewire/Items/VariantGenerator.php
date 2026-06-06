<?php

namespace Modules\Inventory\Livewire\Items;

use Livewire\Component;
use Modules\Inventory\Models\ItemAttributeName;
use Modules\Inventory\Models\ItemVariant;

class VariantGenerator extends Component
{
    public int $itemId;

    /** Each element: {id, attribute_id, name, note, price_difference, is_default, sort_order, status} */
    public array $rows = [];

    public array $availableAttributes = [];

    public function mount(int $itemId): void
    {
        $this->itemId = $itemId;
        $this->loadAvailableAttributes();
        $this->loadRows();
    }

    private function loadAvailableAttributes(): void
    {
        $this->availableAttributes = ItemAttributeName::active()
            ->ordered()
            ->get(['id', 'name', 'is_required', 'sort_order'])
            ->map(fn ($a) => [
                'id'          => $a->id,
                'name'        => $a->getTranslation('name', app()->getLocale()),
                'is_required' => $a->is_required,
            ])
            ->toArray();
    }

    private function loadRows(): void
    {
        $this->rows = ItemVariant::where('item_id', $this->itemId)
            ->orderBy('attribute_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($v) => [
                'id'               => $v->id,
                'attribute_id'     => (string) $v->attribute_id,
                'name'             => $v->getTranslation('name', app()->getLocale()),
                'note'             => $v->note ? $v->getTranslation('note', app()->getLocale()) : '',
                'price_difference' => (string) $v->price_difference,
                'is_default'       => (bool) $v->is_default,
                'sort_order'       => (string) $v->sort_order,
                'status'           => $v->status,
            ])
            ->toArray();
    }

    public function addRow(?int $attributeId = null): void
    {
        $nextSort = count(array_filter($this->rows, fn ($r) => $r['attribute_id'] == $attributeId));

        $this->rows[] = [
            'id'               => null,
            'attribute_id'     => $attributeId ? (string) $attributeId : '',
            'name'             => '',
            'note'             => '',
            'price_difference' => '0',
            'is_default'       => false,
            'sort_order'       => (string) $nextSort,
            'status'           => 'active',
        ];
    }

    public function removeRow(int $index): void
    {
        $row = $this->rows[$index] ?? null;

        if ($row && $row['id']) {
            ItemVariant::find($row['id'])?->delete();
        }

        array_splice($this->rows, $index, 1);
    }

    public function save(): void
    {
        $this->validate([
            'rows.*.attribute_id'     => 'required|integer|exists:attribute_names,id',
            'rows.*.name'             => 'required|string|max:255',
            'rows.*.note'             => 'nullable|string|max:500',
            'rows.*.price_difference' => 'required|numeric',
            'rows.*.sort_order'       => 'required|integer|min:0',
            'rows.*.status'           => 'required|in:active,inactive',
        ]);

        $keptIds = [];

        foreach ($this->rows as &$row) {
            $variant = ItemVariant::updateOrCreate(
                ['id' => $row['id'] ?? 0],
                [
                    'item_id'          => $this->itemId,
                    'attribute_id'     => $row['attribute_id'],
                    'name'             => ['en' => $row['name']],
                    'note'             => $row['note'] ? ['en' => $row['note']] : null,
                    'price_difference' => $row['price_difference'],
                    'is_default'       => $row['is_default'],
                    'sort_order'       => $row['sort_order'],
                    'status'           => $row['status'],
                ]
            );

            $row['id'] = $variant->id;
            $keptIds[] = $variant->id;
        }
        unset($row);

        // Remove variants deleted on the UI but not yet removed from DB
        ItemVariant::where('item_id', $this->itemId)
            ->whereNotIn('id', $keptIds)
            ->delete();

        session()->flash('message', 'Variants saved.');
        $this->dispatch('variantsSaved');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Returns rows grouped by attribute_id for the view. */
    public function groupedRows(): array
    {
        $grouped = [];

        foreach ($this->rows as $index => $row) {
            $grouped[$row['attribute_id'] ?? 0][] = array_merge($row, ['_index' => $index]);
        }

        return $grouped;
    }

    public function render()
    {
        return view('inventory::livewire.items.variant-generator', [
            'groupedRows'          => $this->groupedRows(),
            'availableAttributes'  => $this->availableAttributes,
        ]);
    }
}
