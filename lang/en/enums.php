<?php

return [

    'roles' => [
        'admin' => 'Administrator',
        'operator' => 'Operator',
    ],

    'shipment_status' => [
        'pending' => 'Pending',
        'registered' => 'Shipment registered',
        'picked_up' => 'Picked up',
        'sorting_center' => 'Arrived at sorting center',
        'in_transit' => 'In transit',
        'customs' => 'Customs clearance',
        'destination_center' => 'Arrived in destination country',
        'out_for_delivery' => 'Out for delivery',
        'delivered' => 'Delivered',
        'delayed' => 'Delayed',
        'returned' => 'Returned',
        'cancelled' => 'Cancelled',
    ],

    'shipment_status_description' => [
        'pending' => 'The shipment has been created and is awaiting processing.',
        'registered' => 'The shipment has been registered in our system.',
        'picked_up' => 'The package has been picked up from the sender.',
        'sorting_center' => 'The package has arrived at the sorting center.',
        'in_transit' => 'Your package is currently in transit to the destination logistics center.',
        'customs' => 'The package is going through customs clearance.',
        'destination_center' => 'The package has arrived in the destination country.',
        'out_for_delivery' => 'The package is out for delivery to the recipient.',
        'delivered' => 'The package has been successfully delivered.',
        'delayed' => 'The delivery of the package has been delayed.',
        'returned' => 'The package has been returned to the sender.',
        'cancelled' => 'The shipment has been cancelled.',
    ],

    'service_type' => [
        'standard' => 'Standard',
        'express' => 'Express',
        'economy' => 'Economy',
        'international' => 'International',
    ],

    'shipment_type' => [
        'document' => 'Document',
        'parcel' => 'Parcel',
        'pallet' => 'Pallet',
        'fragile' => 'Fragile',
    ],

    'notification_status' => [
        'pending' => 'Pending',
        'sent' => 'Sent',
        'failed' => 'Failed',
    ],

    'notification_channel' => [
        'mail' => 'Email',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
    ],

];
