<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static $sort = 25;

    public function mount(): void
    {
        $this->form->fill([
            'nip'            => Auth::user()->nip,
            'nomor_whatsapp' => Auth::user()->nomor_whatsapp,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Lainnya')
                    ->aside()
                    ->description('Perbarui informasi tambahan terkait profil Anda')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->nullable()
                            ->numeric()
                            ->maxLength(18)
                            ->unique(ignoreRecord: Auth::id()),
                        Forms\Components\TextInput::make('nomor_whatsapp')
                            ->label('Nomor WhatsApp')
                            ->nullable()
                            ->numeric()
                            ->maxLength(15),
                    ]),
            ])
            ->statePath('data')
            ->model(Auth::user());
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'nip'            => $data['nip'] ?? null,
            'nomor_whatsapp' => $data['nomor_whatsapp'] ?? null,
        ]);

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
