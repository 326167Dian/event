<?php

namespace App\Filament\Resources\Registrations\Pages;

use App\Filament\Resources\Registrations\RegistrationResource;
use App\Models\Registration;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Forms;
use Filament\Actions\Action;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    // protected function getTableActions(): array
    // {
    //     return [
    //         // View Details (lihat bukti pembayaran)
    //         Action::make('view')
    //             ->label('View Details')
    //             ->icon('heroicon-o-eye')
    //             ->url(fn(Registration $record) => static::getResource()::getUrl('view', ['record' => $record]))
    //             ->openUrlInNewTab(),

    //         // Approve
    //         Action::make('approve')
    //             ->label('Approve')
    //             ->requiresConfirmation()
    //             ->action(fn(Registration $record, array $data) => $record->approve($data['note'] ?? null))
    //             ->form([
    //                 Forms\Components\Textarea::make('note')
    //                     ->label('Catatan Admin (opsional)'),
    //             ])
    //             ->visible(fn(Registration $record) => $record->status === 'waiting_approval'),

    //         // Reject
    //         Action::make('reject')
    //             ->label('Reject')
    //             ->color('danger')
    //             ->requiresConfirmation()
    //             ->action(fn(Registration $record, array $data) => $record->reject($data['reason']))
    //             ->form([
    //                 Forms\Components\Textarea::make('reason')
    //                     ->required()
    //                     ->label('Alasan penolakan'),
    //             ])
    //             ->visible(fn(Registration $record) => in_array($record->status, ['waiting_approval', 'waiting_payment'])),
    //     ];
    // // }

    // protected function getTableActionsColumnLabel(): ?string
    // {
    //     return 'Aksi'; // <<< ini kunci agar muncul header kolom "Aksi"
    // }
}
