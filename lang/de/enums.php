<?php

return [

    'roles' => [
        'admin' => 'Administrator',
        'operator' => 'Bearbeiter',
    ],

    'shipment_status' => [
        'pending' => 'Ausstehend',
        'registered' => 'Sendung registriert',
        'picked_up' => 'Abgeholt',
        'sorting_center' => 'Im Sortierzentrum eingetroffen',
        'in_transit' => 'Unterwegs',
        'customs' => 'Zollabfertigung',
        'destination_center' => 'Im Zielland eingetroffen',
        'out_for_delivery' => 'In Zustellung',
        'delivered' => 'Zugestellt',
        'delayed' => 'Verzögert',
        'returned' => 'Retourniert',
        'cancelled' => 'Storniert',
    ],

    'shipment_status_description' => [
        'pending' => 'Die Sendung wurde erstellt und wartet auf die Bearbeitung.',
        'registered' => 'Das Paket wurde in unserem System registriert.',
        'picked_up' => 'Das Paket wurde beim Absender abgeholt.',
        'sorting_center' => 'Das Paket ist im Sortierzentrum eingetroffen.',
        'in_transit' => 'Ihr Paket befindet sich derzeit auf dem Weg zum Ziel-Logistikzentrum.',
        'customs' => 'Das Paket durchläuft die Zollabfertigung.',
        'destination_center' => 'Das Paket ist im Zielland eingetroffen.',
        'out_for_delivery' => 'Das Paket wird gerade an den Empfänger zugestellt.',
        'delivered' => 'Das Paket wurde erfolgreich zugestellt.',
        'delayed' => 'Die Zustellung des Pakets hat sich verzögert.',
        'returned' => 'Das Paket wurde an den Absender zurückgeschickt.',
        'cancelled' => 'Die Sendung wurde storniert.',
    ],

    'service_type' => [
        'standard' => 'Standard',
        'express' => 'Express',
        'economy' => 'Economy',
        'international' => 'International',
    ],

    'shipment_type' => [
        'document' => 'Dokument',
        'parcel' => 'Paket',
        'pallet' => 'Palette',
        'fragile' => 'Zerbrechlich',
    ],

    'notification_status' => [
        'pending' => 'Ausstehend',
        'sent' => 'Gesendet',
        'failed' => 'Fehlgeschlagen',
    ],

    'notification_channel' => [
        'mail' => 'E-Mail',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];
