<?php

namespace App\Filament\Resources\VoterResource\Pages;

use App\Filament\Resources\VoterResource;
use App\Models\Voter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\TemporaryUploadedFile;
use Illuminate\Http\UploadedFile;

class ListVoters extends ListRecords
{
    protected static string $resource = VoterResource::class;

    protected function getHeaderActions(): array
{
    return [
        // ✅ keep the default "New Voter" create button
        \Filament\Actions\CreateAction::make(),

        // ✅ add your custom CSV upload button
        \Filament\Actions\Action::make('importCsv')
            ->label('Upload CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->form([
                \Filament\Forms\Components\FileUpload::make('file')
                    ->label('Select CSV File')
                    ->required()
                    ->acceptedFileTypes(['text/csv'])
                    ->directory('imports')
                    ->visibility('private'),
            ])
            ->action(function (array $data) {
                $file = $data['file'];
                $path = Storage::path($file);

                if (!file_exists($path)) {
                    \Filament\Notifications\Notification::make()
                        ->title('Upload failed')
                        ->body('Uploaded file not found.')
                        ->danger()
                        ->send();
                    return;
                }

                $handle = fopen($path, 'r');
                $header = fgetcsv($handle);
                $expected = ['name', 'email', 'phone'];

                if ($header !== $expected) {
                    \Filament\Notifications\Notification::make()
                        ->title('Invalid CSV format')
                        ->body('The CSV must contain: name, email, phone.')
                        ->danger()
                        ->send();
                    fclose($handle);
                    return;
                }

                $count = 0;
                while (($row = fgetcsv($handle)) !== false) {
                    [$name, $email, $phone] = $row;

                    \App\Models\Voter::create([
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'token' => \Illuminate\Support\Str::random(40),
                    ]);

                    $count++;
                }

                fclose($handle);

                \Filament\Notifications\Notification::make()
                    ->title('Import Successful')
                    ->body("Imported {$count} voters successfully.")
                    ->success()
                    ->send();
            }),
    ];
}

}
