<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Midtrans\Config;
use Midtrans\Snap;


class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('start_at', 'asc')
            ->where('is_active', 1)
            ->get();
        return view('frontend.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'EVT-' . time(),
                'gross_amount' => $event->price,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'enabled_payments' => [
                'gopay',
                'bank_transfer',
                'credit_card',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('frontend.events.show', compact('event', 'snapToken'));
    }
}
