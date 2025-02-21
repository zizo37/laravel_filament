<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Traits\HasActiveIcon;
// use Barryvdh\DomPDF\PDF;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;



class InvoiceResource extends Resource
{

    use HasActiveIcon;

    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'fas-file-download';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('order_id')
                ->relationship('order', 'id')
                ->required(),
            Forms\Components\TextInput::make('invoice_number')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\DateTimePicker::make('invoice_date')
                ->required(),
            Forms\Components\DateTimePicker::make('due_date')
                ->required(),
            Forms\Components\TextInput::make('subtotal')
                ->required()
                ->numeric()
                ->prefix('MAD'),
            Forms\Components\TextInput::make('tax')
                ->required()
                ->numeric()
                ->prefix('MAD'),
            Forms\Components\TextInput::make('total')
                ->required()
                ->numeric()
                ->prefix('MAD'),
            Forms\Components\Select::make('status')
                ->options([
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                    'overdue' => 'Overdue',
                ])
                ->required(),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('invoice_number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('order.id')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('invoice_date')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('due_date')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('total')
                ->money('MAD')
                ->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'success' => 'paid',
                    'warning' => 'unpaid',
                    'danger' => 'overdue',
                ]),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\Action::make('view')
                ->label('')
                // ->label('View Invoice')
                ->icon('heroicon-o-eye')
                ->color('danger')
                ->modalContent(fn (Invoice $record) => view('pdf.invoice', ['invoice' => $record]))
                ->modalSubmitAction(false)
                ->modalCancelAction(false),
            // Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('download')
                ->label('Download PDF')
                // ->icon('heroicon-o-download')
                ->action(function (Invoice $record) {
                    $pdf = PDF::loadView('pdf.invoice', [
                        'invoice' => $record,
                    ]);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $record->invoice_number . '.pdf');
                }),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
