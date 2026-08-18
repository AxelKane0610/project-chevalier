<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public string $modelClass = '';

    public array $searchFields = [];

    public string $displayField = '';

    public string $valueField = 'id';

    public string $search = '';

    public string $name = '';

    public string $selectedValue = '';

    public bool $showDropdown = false;
    public bool $hasSelected = false;

    public array $results = [];


    public function updatedSearch()
    {
        $this->hasSelected = false;

        $this->showDropdown = true;

        if (trim($this->search) === '') {
            $this->results = [];
            return;
        }

        $query = $this->modelClass::query();

        $query->where(function ($q) {

            foreach ($this->searchFields as $field) {

                $q->orWhere(
                    $field,
                    'like',
                    '%' . trim($this->search) . '%'
                );

            }

        });

        $this->results = $query
            ->limit(10)
            ->get()
            ->toArray();
    }


    public function selectItem($value)
    {
        $item = $this->modelClass::query()
            ->where($this->valueField, $value)
            ->first();

        if (!$item) {
            return;
        }

        // Giá trị hiển thị
        $this->search = $item->{$this->displayField};

        // Giá trị thực tế submit
        $this->selectedValue = $item->{$this->valueField};

        $this->showDropdown = false;

        $this->results = [];

        $this->hasSelected = true;

        $this->dispatch(
            'common-search-selected',
            item: $item->toArray()
        );
    }


    public function clearSearch()
    {
        if ($this->hasSelected) {
            $this->showDropdown = false;
            $this->results = [];

            return;
        }

        $this->search = '';

        $this->results = [];

        $this->showDropdown = false;
    }

    #[On('set-user-owner')]
    public function setUserOwner($userId)
    {
        // Không có User Owner
        if (!$userId) {
            $this->search = '';
            $this->selectedValue = '';
            $this->hasSelected = false;
            $this->showDropdown = false;
            $this->results = [];

            return;
        }

        // Tìm user
        $item = $this->modelClass::query()
            ->where($this->valueField, $userId)
            ->first();

        if (!$item) {
            $this->search = '';
            $this->selectedValue = '';
            $this->hasSelected = false;

            return;
        }

        // Text hiển thị
        $this->search = $item->{$this->displayField};

        // ID thực tế submit
        $this->selectedValue = (string) $item->{$this->valueField};

        $this->hasSelected = true;

        $this->showDropdown = false;
        $this->results = [];
    }
};
?>

<div
    class="position-relative"
    @click.outside="$wire.clearSearch()"
>
    {{-- Input hiển thị cho user --}}
    <input
        type="text"
        class="form-control"
        wire:model.live.debounce.300ms="search"
        wire:focus="$set('showDropdown', true)"
        placeholder="Search user..."
        autocomplete="off"
    >

    {{-- Input thực sự được submit --}}
    <input
        type="hidden"
        name="{{ $name }}"
        value="{{ $selectedValue }}"
    >

    {{-- Dropdown --}}
    @if($showDropdown && count($results) > 0)

        <div
            class="position-absolute w-100 bg-white border rounded shadow-sm mt-1"
            style="z-index: 1050;"
        >

            @foreach($results as $item)

                <button
                    type="button"
                    class="dropdown-item px-3 py-2 text-start"
                    wire:click="selectItem('{{ $item[$valueField] }}')"
                >
                    {{ $item[$displayField] }}
                </button>

            @endforeach

        </div>

    @endif
</div>