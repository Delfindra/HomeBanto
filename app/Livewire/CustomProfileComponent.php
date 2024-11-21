<?php

namespace App\Livewire;

use App\Models\allergies;
use App\Models\Diet;
use App\Models\ingredients;
use App\Models\MasterData;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Setting Preference')
                    ->aside()
                    ->description('Update your setting preference.')
                    ->schema([
                        Forms\Components\Select::make('diet_id')
                            ->label('Diet Preference')
                            ->native(false)
                            ->options(Diet::all()->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('allergies')
                            ->multiple()
                            ->native(false)
                            ->label('Allergies')
                            ->options(MasterData::all()->pluck('name', 'id'))
                            ->searchable(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        /**
         * @var \App\Models\User $user
         */

        $user = Auth::user();
        $user->update([
            'diet_id' => $data['diet_id']
        ]);

        foreach ($data['allergies'] as $allergy) {
            allergies::create([
                'masterdata_id' => $allergy,
                'user_id' => $user -> id,
            ]);
        }
        
    }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
