<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (): bool => $this->record->id === auth()->id())
                ->tooltip(fn (): ?string => $this->record->id === auth()->id()
                    ? 'Tidak bisa menghapus akun sendiri.'
                    : null),
        ];
    }
}
