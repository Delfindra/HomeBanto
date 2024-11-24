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
use Filament\Notifications\Notification;


class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 0;

    public function mount(): void
    {

        $user = Auth::user();

        // Fetch user's current diet and allergies
        $this->data = [
            'diet_id' => $user->diet_id, // Assuming diet_id is saved in the User model
            'allergies' => $user->allergies->pluck('masterdata_id')->toArray(), // Assuming relationship exists
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        $user = Auth::user();

        return $form
            ->schema([
                Section::make('Setting Preference')
                    ->aside()
                    ->description('Update your setting preference.')
                    ->schema([
                        Forms\Components\Select::make('diet_id')
                            ->label('Diet Preference')
                            ->default($user->diet_id)
                            ->native(false)
                            ->options(Diet::all()->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('allergies')
                            ->multiple()
                            ->native(false)
                            ->default(allergies::where('user_id', $user->id
                            )->get()->pluck('masterdata_id')->toArray())
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

        // Sync allergies: remove deselected and add new ones
        $newAllergies = $data['allergies']; // The selected allergies from the form
        $existingAllergies = $user->allergies->pluck('masterdata_id')->toArray();

        // Remove allergies not in the selected list
        $allergiesToDelete = array_diff($existingAllergies, $newAllergies);
        if (!empty($allergiesToDelete)) {
            allergies::where('user_id', $user->id)
                ->whereIn('masterdata_id', $allergiesToDelete)
                ->delete();
        }

        // Add new allergies not already in the database
        $allergiesToAdd = array_diff($newAllergies, $existingAllergies);
        foreach ($allergiesToAdd as $allergy) {
            allergies::create([
                'masterdata_id' => $allergy,
                'user_id' => $user->id,
            ]);
        }

        Notification::make()
        ->title('Your preference information has been saved successfully.')
        ->success()
        ->send();
    }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
