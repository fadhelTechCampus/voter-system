<?php
// app/Filament/Resources/VoterResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\VoterResource\Pages;
use App\Models\Voter;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class VoterResource extends Resource
{
    protected static ?string $model = Voter::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Voters';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\IconColumn::make('has_voted')->boolean()->label('Voted'),
                Tables\Columns\TextColumn::make('token')->copyable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import_csv')
                    ->label('Import CSV')
                    ->form([
                        Forms\Components\FileUpload::make('csv')
                            ->label('CSV File (name,email,phone)')
                            ->acceptedFileTypes(['text/csv'])
                            ->preserveFilenames()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        /** @var TemporaryUploadedFile $file */
                        $file = $data['csv'];
                        $path = $file->store('imports');
                        $full = Storage::path($path);

                        $rows = array_map('str_getcsv', file($full));
                        $header = array_map('trim', array_shift($rows));
                        $idxName = array_search('name', $header);
                        $idxEmail = array_search('email', $header);
                        $idxPhone = array_search('phone', $header);

                        foreach ($rows as $r) {
                            $name  = $r[$idxName] ?? null;
                            $email = $r[$idxEmail] ?? null;
                            $phone = $r[$idxPhone] ?? null;
                            if (!$name) continue;

                            Voter::updateOrCreate(
                                ['email' => $email],
                                [
                                    'name' => $name,
                                    'phone' => $phone,
                                    'token' => Str::random(40),
                                ]
                            );
                        }
                    })
                    ->successNotificationTitle('Import Completed!'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVoters::route('/'),
            'create' => Pages\CreateVoter::route('/create'),
            'edit' => Pages\EditVoter::route('/{record}/edit'),
        ];
    }
}