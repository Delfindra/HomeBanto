<?php

return [
'show_custom_fields' => true,
    'custom_fields' => [
        'diet' => [
            'type' => 'select',
            'label' => 'Diet Reference',
            'placeholder' => 'Select',
            'required' => true,
            'options' => [
                'Keto' => 'Keto',
                'Carnivore' => 'Carnivore',
                'Vegan' => 'Vegan',
                'Vegetarian' => 'Vegetarian',
            ],
        ],
        'allergy' => [
            'type' => 'text',
            'label' => 'Allergy',
            'placeholder' => 'Allergy',
            'required' => true,
            'rules' => 'required|string|max:255',
        ],

    ]
];
