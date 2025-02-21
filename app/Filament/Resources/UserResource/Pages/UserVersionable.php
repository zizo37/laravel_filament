<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Mansoor\FilamentVersionable\RevisionsPage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Overtrue\LaravelVersionable\Version;

class UserVersionable extends RevisionsPage
{
    protected static string $resource = UserResource::class;

    public function getRevisionTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Date')
                ->dateTime()
                ->sortable(),
            TextColumn::make('user.name')
                ->label('Modified By')
                ->sortable(),
            TextColumn::make('field')
                ->label('Field')
                ->sortable(),
            TextColumn::make('old_value')
                ->label('Old Value')
                ->wrap(),
            TextColumn::make('new_value')
                ->label('New Value')
                ->wrap(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            TableAction::make('accept')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Accept this change?')
                ->modalDescription('Are you sure you want to accept this change? This will permanently delete this revision.')
                ->action(function (Version $record) {
                    try {
                        // Force delete the version
                        DB::table('versions')
                            ->where('id', $record->id)
                            ->delete();

                        $this->refresh();

                        Notification::make()
                            ->title('Change accepted and deleted')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error deleting change')
                            ->danger()
                            ->send();
                    }
                }),

            TableAction::make('revert')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Revert this change?')
                ->modalDescription('Are you sure you want to revert to the old value? This will update the record and delete this revision.')
                ->action(function (Version $record) {
                    try {
                        DB::beginTransaction();

                        $user = $record->versionable;
                        $user->update([
                            $record->field => $record->old_value
                        ]);


                        DB::table('versions')
                            ->where('id', $record->id)
                            ->delete();

                        DB::commit();

                        $this->refresh();

                        Notification::make()
                            ->title('Change reverted and deleted')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();

                        Notification::make()
                            ->title('Error reverting change')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept_all')
                ->label('Accept All Changes')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Accept all changes?')
                ->modalDescription('Are you sure you want to accept all changes? This will permanently delete all revision history.')
                ->action(function () {
                    try {
                        $userId = request()->route('record');


                        DB::table('versions')
                            ->where('versionable_type', 'App\Models\User')
                            ->where('versionable_id', $userId)
                            ->delete();

                        $this->refresh();

                        Notification::make()
                            ->title('All changes permanently deleted')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error deleting changes')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function refresh(): void
    {
        $this->redirect(request()->header('Referer'));
    }
}
