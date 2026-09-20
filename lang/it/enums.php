<?php

return [

    'roles' => [
        'admin' => 'Amministratore',
        'operator' => 'Operatore',
    ],

    'shipment_status' => [
        'pending' => 'In attesa',
        'registered' => 'Spedizione registrata',
        'picked_up' => 'Ritirato',
        'sorting_center' => 'Arrivato al centro di smistamento',
        'in_transit' => 'In transito',
        'customs' => 'Sdoganamento',
        'destination_center' => 'Arrivato nel paese di destinazione',
        'out_for_delivery' => 'In consegna',
        'delivered' => 'Consegnato',
        'delayed' => 'In ritardo',
        'returned' => 'Restituito',
        'cancelled' => 'Annullato',
    ],

    'shipment_status_description' => [
        'pending' => 'La spedizione è stata creata ed è in attesa di elaborazione.',
        'registered' => 'Il pacco è stato registrato nel nostro sistema.',
        'picked_up' => 'Il pacco è stato ritirato presso il mittente.',
        'sorting_center' => 'Il pacco è arrivato al centro di smistamento.',
        'in_transit' => 'Il pacco è attualmente in transito verso il centro logistico di destinazione.',
        'customs' => 'Il pacco è in fase di sdoganamento.',
        'destination_center' => 'Il pacco è arrivato nel paese di destinazione.',
        'out_for_delivery' => 'Il pacco è in consegna presso il destinatario.',
        'delivered' => 'Il pacco è stato consegnato con successo.',
        'delayed' => 'La consegna del pacco ha subito un ritardo.',
        'returned' => 'Il pacco è stato restituito al mittente.',
        'cancelled' => 'La spedizione è stata annullata.',
    ],

    'service_type' => [
        'standard' => 'Standard',
        'express' => 'Espresso',
        'economy' => 'Economy',
        'international' => 'Internazionale',
    ],

    'shipment_type' => [
        'document' => 'Documento',
        'parcel' => 'Pacco',
        'pallet' => 'Pallet',
        'fragile' => 'Fragile',
    ],

    'notification_status' => [
        'pending' => 'In attesa',
        'sent' => 'Inviata',
        'failed' => 'Fallita',
    ],

    'notification_channel' => [
        'mail' => 'E-mail',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];
