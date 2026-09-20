<?php

return [

    'roles' => [
        'admin' => 'Administrator',
        'operator' => 'Operator',
    ],

    'shipment_status' => [
        'pending' => 'În așteptare',
        'registered' => 'Colet înregistrat',
        'picked_up' => 'Ridicat',
        'sorting_center' => 'Sosit la centrul de sortare',
        'in_transit' => 'În tranzit',
        'customs' => 'Vămuire',
        'destination_center' => 'Sosit în țara de destinație',
        'out_for_delivery' => 'În curs de livrare',
        'delivered' => 'Livrat',
        'delayed' => 'Întârziat',
        'returned' => 'Returnat',
        'cancelled' => 'Anulat',
    ],

    'shipment_status_description' => [
        'pending' => 'Expedierea a fost creată și așteaptă procesarea.',
        'registered' => 'Coletul a fost înregistrat în sistemul nostru.',
        'picked_up' => 'Coletul a fost ridicat de la expeditor.',
        'sorting_center' => 'Coletul a ajuns la centrul de sortare.',
        'in_transit' => 'Coletul dumneavoastră este momentan în tranzit către centrul logistic de destinație.',
        'customs' => 'Coletul este în curs de vămuire.',
        'destination_center' => 'Coletul a ajuns în țara de destinație.',
        'out_for_delivery' => 'Coletul este în curs de livrare către destinatar.',
        'delivered' => 'Coletul a fost livrat cu succes.',
        'delayed' => 'Livrarea coletului a fost întârziată.',
        'returned' => 'Coletul a fost returnat expeditorului.',
        'cancelled' => 'Expedierea a fost anulată.',
    ],

    'service_type' => [
        'standard' => 'Standard',
        'express' => 'Expres',
        'economy' => 'Economic',
        'international' => 'Internațional',
    ],

    'shipment_type' => [
        'document' => 'Document',
        'parcel' => 'Colet',
        'pallet' => 'Palet',
        'fragile' => 'Fragil',
    ],

    'notification_status' => [
        'pending' => 'În așteptare',
        'sent' => 'Trimisă',
        'failed' => 'Eșuată',
    ],

    'notification_channel' => [
        'mail' => 'E-mail',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];
