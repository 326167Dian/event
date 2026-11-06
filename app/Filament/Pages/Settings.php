<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Halaman Pengaturan: metode pembayaran, kunci Midtrans, dan Meta Pixel.
 * Semua nilai disimpan di tabel `settings`, jadi tidak perlu edit kode / .env.
 *
 * @property-read Schema $form
 */
class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan Website';

    protected static ?string $slug = 'pengaturan';

    protected static ?int $navigationSort = 99;

    /**
     * Field yang dikelola halaman ini.
     */
    protected const FIELDS = [
        'whatsapp_number',
        'payment_mode',
        'manual_payment_info',
        'qris_static_image_path',
        'midtrans_server_key',
        'midtrans_client_key',
        'midtrans_is_production',
        'meta_pixel_enabled',
        'meta_pixel_id',
        'meta_capi_token',
        'meta_test_event_code',
    ];

    public ?array $data = [];

    public function mount(): void
    {
        $values = Setting::allValues();

        $this->form->fill([
            'whatsapp_number' => $values['whatsapp_number'] ?? null,
            'payment_mode' => Setting::paymentMode(),
            'manual_payment_info' => $values['manual_payment_info'] ?? null,
            'qris_static_image_path' => $values['qris_static_image_path'] ?? null,
            'midtrans_server_key' => $values['midtrans_server_key'] ?? config('services.midtrans.server_key'),
            'midtrans_client_key' => $values['midtrans_client_key'] ?? config('services.midtrans.client_key'),
            'midtrans_is_production' => Setting::midtransIsProduction(),
            'meta_pixel_enabled' => Setting::bool('meta_pixel_enabled'),
            'meta_pixel_id' => $values['meta_pixel_id'] ?? null,
            'meta_capi_token' => $values['meta_capi_token'] ?? null,
            'meta_test_event_code' => $values['meta_test_event_code'] ?? null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Kontak WhatsApp')
                    ->description('Tombol WhatsApp melayang akan muncul di semua halaman customer (daftar, bayar, dll) kalau nomor ini diisi, supaya customer yang ragu setelah transfer bisa langsung terhubung ke admin.')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->schema([
                        TextInput::make('whatsapp_number')
                            ->label('Nomor WhatsApp Admin/CS')
                            ->placeholder('Contoh: 08123456789')
                            ->helperText('Boleh diawali 0 atau +62, otomatis dirapikan. Kosongkan untuk menyembunyikan tombol WhatsApp.')
                            ->tel(),
                    ]),

                Section::make('Metode Pembayaran')
                    ->description('Berlaku untuk semua event berbayar.')
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->schema([
                        Radio::make('payment_mode')
                            ->label('Cara peserta membayar')
                            ->options([
                                'qris' => 'QRIS Dinamis otomatis (GDC Pay) — satu tampilan, lunas terkonfirmasi otomatis. Kalau gagal dibuat, otomatis beralih ke QRIS statis, lalu transfer manual.',
                                'manual' => 'Transfer manual + upload bukti saja — admin verifikasi',
                            ])
                            ->required(),
                        FileUpload::make('qris_static_image_path')
                            ->label('Gambar QRIS statis (fallback)')
                            ->helperText('Ditampilkan kalau QRIS Dinamis (GDC) sedang gagal/tidak dikonfigurasi. Kalau dikosongkan, fallback langsung ke transfer manual.')
                            ->disk('public')
                            ->directory('qris')
                            ->image()
                            ->visibility('public'),
                        Textarea::make('manual_payment_info')
                            ->label('Info rekening untuk transfer manual')
                            ->rows(3)
                            ->placeholder("Contoh:\nBank BSI 1234567890 a.n. Nama Pemilik")
                            ->helperText('Tampil di form pendaftaran saat QRIS (dinamis maupun statis) tidak tersedia.'),
                    ]),

                Section::make('Midtrans (lama, tidak dipakai lagi)')
                    ->description('Sudah diganti QRIS Dinamis GDC Pay di atas. Dibiarkan ada hanya untuk kompatibilitas transaksi lama yang masih berjalan.')
                    ->icon(Heroicon::OutlinedQrCode)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('midtrans_server_key')
                            ->label('Server Key')
                            ->password()
                            ->revealable(),
                        TextInput::make('midtrans_client_key')
                            ->label('Client Key'),
                        Toggle::make('midtrans_is_production')
                            ->label('Mode Production (uang sungguhan)')
                            ->helperText('Matikan untuk uji coba memakai key Sandbox.'),
                        Text::make(fn () => 'URL Notifikasi (isi di Midtrans › Settings › Payment › Notification URL): '
                            . url('/midtrans/notification')),
                    ]),

                Section::make('Meta Pixel')
                    ->description('Pelacakan iklan Facebook / Instagram.')
                    ->icon(Heroicon::OutlinedChartBar)
                    ->collapsible()
                    ->schema([
                        Toggle::make('meta_pixel_enabled')
                            ->label('Aktifkan Meta Pixel'),
                        TextInput::make('meta_pixel_id')
                            ->label('Pixel ID / Dataset ID')
                            ->placeholder('Contoh: 123456789012345')
                            ->helperText('Angka saja. Ada di Meta Events Manager › Data Sources.')
                            ->regex('/^\d{8,20}$/')
                            ->validationMessages(['regex' => 'Pixel ID harus berupa angka saja.']),
                        TextInput::make('meta_capi_token')
                            ->label('Conversions API Access Token (opsional)')
                            ->password()
                            ->revealable()
                            ->helperText('Disarankan. Mengirim event Purchase langsung dari server saat pembayaran QRIS lunas, jadi tetap tercatat walau browser memblokir pixel.'),
                        TextInput::make('meta_test_event_code')
                            ->label('Test Event Code (opsional)')
                            ->placeholder('Contoh: TEST12345')
                            ->helperText('Isi hanya saat menguji di Events Manager › Test Events. Kosongkan lagi setelah selesai.'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Simpan Pengaturan')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (static::FIELDS as $key) {
            $value = $data[$key] ?? null;

            if (is_string($value)) {
                $value = trim($value);
            }

            Setting::set($key, $value);
        }

        Notification::make()
            ->success()
            ->title('Pengaturan tersimpan')
            ->send();
    }
}
