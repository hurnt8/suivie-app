<?php

return [

    'roles' => [
        'admin' => 'Administrator',
        'operator' => 'Operator',
    ],

    'shipment_status' => [
        'pending' => 'Oczekująca',
        'registered' => 'Przesyłka zarejestrowana',
        'picked_up' => 'Odebrana',
        'sorting_center' => 'Dotarła do centrum sortowania',
        'in_transit' => 'W transporcie',
        'customs' => 'Odprawa celna',
        'destination_center' => 'Dotarła do kraju docelowego',
        'out_for_delivery' => 'W trakcie doręczenia',
        'delivered' => 'Dostarczona',
        'delayed' => 'Opóźniona',
        'returned' => 'Zwrócona',
        'cancelled' => 'Anulowana',
    ],

    'shipment_status_description' => [
        'pending' => 'Przesyłka została utworzona i oczekuje na przetworzenie.',
        'registered' => 'Paczka została zarejestrowana w naszym systemie.',
        'picked_up' => 'Paczka została odebrana od nadawcy.',
        'sorting_center' => 'Paczka dotarła do centrum sortowania.',
        'in_transit' => 'Twoja paczka jest obecnie w transporcie do docelowego centrum logistycznego.',
        'customs' => 'Paczka przechodzi odprawę celną.',
        'destination_center' => 'Paczka dotarła do kraju docelowego.',
        'out_for_delivery' => 'Paczka jest w trakcie doręczenia do odbiorcy.',
        'delivered' => 'Paczka została pomyślnie dostarczona.',
        'delayed' => 'Dostawa paczki uległa opóźnieniu.',
        'returned' => 'Paczka została zwrócona do nadawcy.',
        'cancelled' => 'Przesyłka została anulowana.',
    ],

    'service_type' => [
        'standard' => 'Standardowa',
        'express' => 'Ekspresowa',
        'economy' => 'Ekonomiczna',
        'international' => 'Międzynarodowa',
    ],

    'shipment_type' => [
        'document' => 'Dokument',
        'parcel' => 'Paczka',
        'pallet' => 'Paleta',
        'fragile' => 'Krucha',
    ],

    'notification_status' => [
        'pending' => 'Oczekujące',
        'sent' => 'Wysłane',
        'failed' => 'Nieudane',
    ],

    'notification_channel' => [
        'mail' => 'E-mail',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];
