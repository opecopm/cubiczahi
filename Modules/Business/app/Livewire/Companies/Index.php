<?php

namespace Modules\Business\Livewire\Companies;

use App\Livewire\WithFilters;
use App\Livewire\WithModalTrait;
use App\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Business\Models\Company;

// use Modules\HRM\Models\Employee;

class Index extends Component
{
    use WithFileUploads, WithFilters, WithModalTrait, WithPagination, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public int $perPage = 100;

    public array $activeLanguages = [];

    public $model;

    // Bilingual name fields and other attributes
    public $name = []; // Changed to array to hold translations

    public $code = '';

    public $parent_id = null;

    public $crn = '';

    public $trn = '';

    public $email = '';

    public $phone = '';

    public $website = '';

    public $invoice_code = '';

    public $currency = '';

    public $is_group = false;

    public $is_active = true;

    public $hr_id = null;

    public $vp_id = null;

    public $logoUpload;

    public $headerUpload;

    public $footerUpload;

    public $stampUpload;

    public $companyId;

    public $updateMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortDirection' => ['except' => ''],
        'perPage' => ['except' => 100],
        'filters' => ['except' => []],
    ];

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $langs = system_setting('active_languages', ['ar']);
        $this->activeLanguages = is_string($langs) ? (json_decode($langs, true) ?? [$langs]) : $langs;
        $this->model = new Company;
        $this->orderable = ['id', 'name', 'code', 'is_active'];
        $this->initFilters($this->model);
    }

    public function rules()
    {
        $rules = [
            'name.en' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:companies,code,'.$this->companyId,
            'crn' => 'nullable|string|max:50',
            'trn' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'invoice_code' => 'nullable|string|max:50',
            'currency' => 'required|string|max:3',
            'parent_id' => 'nullable|exists:companies,id',
            'is_group' => 'boolean|nullable',
            'is_active' => 'boolean|nullable',
            'hr_id' => 'nullable|integer|exists:employees,id',
            'vp_id' => 'nullable|integer|exists:employees,id',
            'logoUpload' => 'nullable|image|max:5120',
            'headerUpload' => 'nullable|image|max:5120',
            'footerUpload' => 'nullable|image|max:5120',
            'stampUpload' => 'nullable|image|max:5120',
        ];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $rules["name.{$lang}"] = 'nullable|string|max:255';
            }
        }
        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetInputFields()
    {
        $nameArr = ['en' => ''];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $nameArr[$lang] = '';
            }
        }
        $this->name = $nameArr;
        $this->code = '';
        $this->parent_id = null;
        $this->crn = '';
        $this->trn = '';
        $this->email = '';
        $this->phone = '';
        $this->website = '';
        $this->invoice_code = '';
        $this->currency = '';
        $this->is_group = false;
        $this->is_active = true;
        $this->hr_id = null;
        $this->vp_id = null;
        $this->logoUpload = null;
        $this->headerUpload = null;
        $this->footerUpload = null;
        $this->stampUpload = null;
        $this->companyId = null;
        $this->updateMode = false;
        $this->resetErrorBag();
    }

    private function syncCompanyMedia(Company $company): void
    {
        if ($this->logoUpload) {
            $company->clearMediaCollection('company_logo');
            $company->addMedia($this->logoUpload->getRealPath())->toMediaCollection('company_logo');
        }

        if ($this->headerUpload) {
            $company->clearMediaCollection('company_header');
            $company->addMedia($this->headerUpload->getRealPath())->toMediaCollection('company_header');
        }

        if ($this->footerUpload) {
            $company->clearMediaCollection('company_footer');
            $company->addMedia($this->footerUpload->getRealPath())->toMediaCollection('company_footer');
        }

        if ($this->stampUpload) {
            $company->clearMediaCollection('company_stamp');
            $company->addMedia($this->stampUpload->getRealPath())->toMediaCollection('company_stamp');
        }
    }

    public function store()
    {
        $this->validate();

        // Prepare translations
        $nameTranslations = [
            'en' => $this->name['en'],
        ];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $nameTranslations[$lang] = $this->name[$lang] ?? $this->name['en'];
            }
        }

        $company = Company::create([
            'name' => $nameTranslations,
            'code' => $this->code,
            'parent_id' => $this->parent_id,
            'crn' => $this->crn,
            'trn' => $this->trn,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'invoice_code' => $this->invoice_code,
            'currency' => $this->currency,
            'is_group' => $this->is_group,
            'is_active' => $this->is_active,
            // 'hr_id' => $this->hr_id,
            // 'vp_id' => $this->vp_id,
        ]);

        $this->syncCompanyMedia($company);

        session()->flash('message', 'Company created successfully.');

        $this->resetInputFields();
        $this->closeModal();
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $this->companyId = $id;

        $this->name = [
            'en' => $company->getTranslation('name', 'en'),
        ];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $this->name[$lang] = $company->getTranslation('name', $lang);
            }
        }

        $this->code = $company->code;
        $this->parent_id = $company->parent_id;
        $this->crn = $company->crn;
        $this->trn = $company->trn;
        $this->email = $company->email;
        $this->phone = $company->phone;
        $this->website = $company->website;
        $this->invoice_code = $company->invoice_code;
        $this->currency = $company->currency;
        $this->is_group = (bool) $company->is_group;
        $this->is_active = (bool) $company->is_active;
        $this->hr_id = $company->hr_id;
        $this->vp_id = $company->vp_id;

        $this->updateMode = true;
        $this->openModal();
    }

    public function update()
    {
        $validated = $this->validate();
        $company = Company::find($this->companyId);

        // Prepare translations
        $nameTranslations = [
            'en' => $this->name['en'],
        ];
        foreach ($this->activeLanguages as $lang) {
            if ($lang !== 'en') {
                $nameTranslations[$lang] = $this->name[$lang] ?? $this->name['en'];
            }
        }

        $company->update([
            'name' => $nameTranslations,
            'code' => $this->code,
            'parent_id' => $this->parent_id,
            'crn' => $this->crn,
            'trn' => $this->trn,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'invoice_code' => $this->invoice_code,
            'currency' => $this->currency,
            'is_group' => $this->is_group,
            'is_active' => $this->is_active,
            'hr_id' => $this->hr_id,
            'vp_id' => $this->vp_id,
        ]);

        $this->syncCompanyMedia($company);

        $this->updateMode = false;

        session()->flash('message', 'Company updated successfully.');
        $this->resetInputFields();
        $this->closeModal();
    }

    public function delete()
    {
        Company::find($this->deleteId)?->delete();
        session()->flash('message', 'Company deleted successfully.');
    }

    public function render()
    {
        $activeLanguages = $this->activeLanguages;

        $baseQuery = Company::query()->whereNull('parent_id');

        $companiesCount = $baseQuery->clone()->count();
        $activeCompaniesCount = $baseQuery->clone()->where('is_active', true)->count();
        $inactiveCompaniesCount = $baseQuery->clone()->where('is_active', false)->count();

        $query = $baseQuery->clone()->with(['children']);

        if ($this->search !== '') {
            $search = $this->search;

            $query->where(function ($q) use ($activeLanguages, $search) {
                $q->where('name->en', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('crn', 'like', "%{$search}%")
                    ->orWhere('trn', 'like', "%{$search}%");
                foreach ($activeLanguages as $lang) {
                    if ($lang !== 'en') {
                        $q->orWhere("name->{$lang}", 'like', "%{$search}%");
                    }
                }
            });
        }

        $query = $this->applyFilters($query, $this->model);

        $companies = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $parent_companies = Company::with('children')
            ->whereNull('parent_id')
            ->get();

        $employees = [];

        /*Employee::query()
            ->select('id', 'employee_id', 'first_name', 'middle_name', 'last_name')
            ->orderBy('employee_id')
            ->get();*/

        $editingCompany = $this->companyId ? Company::find($this->companyId) : new Company;

        return view('business::livewire.companies.index', [
            'companies' => $companies,
            'parent_companies' => $parent_companies,
            'activeLanguages' => $activeLanguages,
            'companiesCount' => $companiesCount,
            'activeCompaniesCount' => $activeCompaniesCount,
            'inactiveCompaniesCount' => $inactiveCompaniesCount,
            'model' => $this->model,
            'employees' => $employees,
            'editingCompany' => $editingCompany,
        ]);
    }
}
