<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PageBuilder\PbElementType;

class PageBuilderElementsSeeder extends Seeder
{
    public function run()
    {
        $elements = [
            // Basic Elements
            [
                'name' => 'text',
                'icon' => 'fas fa-font',
                'category' => 'basic',
                'default_settings' => [
                    'content' => [
                        'text' => '<p>Enter your text here</p>'
                    ],
                    'settings' => [
                        'tag' => 'div',
                        'align' => 'left'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'text', 'type' => 'wysiwyg', 'label' => 'Text Content']
                    ],
                    'style' => [
                        ['name' => 'color', 'type' => 'color', 'label' => 'Text Color'],
                        ['name' => 'fontSize', 'type' => 'slider', 'label' => 'Font Size', 'min' => 10, 'max' => 72],
                        ['name' => 'lineHeight', 'type' => 'slider', 'label' => 'Line Height', 'min' => 1, 'max' => 3, 'step' => 0.1],
                        ['name' => 'align', 'type' => 'select', 'label' => 'Alignment', 'options' => ['left', 'center', 'right', 'justify']]
                    ]
                ]
            ],
            [
                'name' => 'heading',
                'icon' => 'fas fa-heading',
                'category' => 'basic',
                'default_settings' => [
                    'content' => [
                        'text' => 'Your Heading Here'
                    ],
                    'settings' => [
                        'tag' => 'h2',
                        'align' => 'left'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'text', 'type' => 'text', 'label' => 'Heading Text'],
                        ['name' => 'tag', 'type' => 'select', 'label' => 'Heading Tag', 'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']]
                    ]
                ]
            ],
            [
                'name' => 'button',
                'icon' => 'fas fa-square',
                'category' => 'basic',
                'default_settings' => [
                    'content' => [
                        'text' => 'Click Me',
                        'link' => '#',
                        'target' => '_self'
                    ],
                    'settings' => [
                        'style' => 'primary',
                        'size' => 'medium',
                        'fullWidth' => false,
                        'icon' => ''
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'text', 'type' => 'text', 'label' => 'Button Text'],
                        ['name' => 'link', 'type' => 'text', 'label' => 'Link URL'],
                        ['name' => 'target', 'type' => 'select', 'label' => 'Target', 'options' => ['_self', '_blank']],
                        ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon']
                    ],
                    'style' => [
                        ['name' => 'style', 'type' => 'select', 'label' => 'Button Style', 'options' => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'outline']],
                        ['name' => 'size', 'type' => 'select', 'label' => 'Size', 'options' => ['small', 'medium', 'large']],
                        ['name' => 'fullWidth', 'type' => 'toggle', 'label' => 'Full Width']
                    ]
                ]
            ],
            [
                'name' => 'divider',
                'icon' => 'fas fa-minus',
                'category' => 'basic',
                'default_settings' => [
                    'settings' => [
                        'style' => 'solid',
                        'width' => '100',
                        'height' => '1',
                        'color' => '#e0e0e0'
                    ]
                ],
                'fields_schema' => [
                    'style' => [
                        ['name' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => ['solid', 'dashed', 'dotted']],
                        ['name' => 'width', 'type' => 'slider', 'label' => 'Width (%)', 'min' => 10, 'max' => 100],
                        ['name' => 'height', 'type' => 'slider', 'label' => 'Height (px)', 'min' => 1, 'max' => 10],
                        ['name' => 'color', 'type' => 'color', 'label' => 'Color']
                    ]
                ]
            ],
            [
                'name' => 'spacer',
                'icon' => 'fas fa-arrows-alt-v',
                'category' => 'basic',
                'default_settings' => [
                    'settings' => [
                        'height' => 50
                    ]
                ],
                'fields_schema' => [
                    'style' => [
                        ['name' => 'height', 'type' => 'slider', 'label' => 'Height (px)', 'min' => 10, 'max' => 500]
                    ]
                ]
            ],

            // Media Elements
            [
                'name' => 'image',
                'icon' => 'fas fa-image',
                'category' => 'media',
                'default_settings' => [
                    'content' => [
                        'src' => 'https://via.placeholder.com/800x400',
                        'alt' => 'Image description'
                    ],
                    'settings' => [
                        'width' => 'auto',
                        'height' => 'auto',
                        'objectFit' => 'cover',
                        'link' => '',
                        'lightbox' => false
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'src', 'type' => 'media', 'label' => 'Image Source'],
                        ['name' => 'alt', 'type' => 'text', 'label' => 'Alt Text'],
                        ['name' => 'link', 'type' => 'text', 'label' => 'Link URL'],
                        ['name' => 'lightbox', 'type' => 'toggle', 'label' => 'Enable Lightbox']
                    ],
                    'style' => [
                        ['name' => 'width', 'type' => 'text', 'label' => 'Width'],
                        ['name' => 'height', 'type' => 'text', 'label' => 'Height'],
                        ['name' => 'objectFit', 'type' => 'select', 'label' => 'Object Fit', 'options' => ['cover', 'contain', 'fill', 'none']],
                        ['name' => 'borderRadius', 'type' => 'slider', 'label' => 'Border Radius', 'min' => 0, 'max' => 50]
                    ]
                ]
            ],
            [
                'name' => 'video',
                'icon' => 'fas fa-video',
                'category' => 'media',
                'default_settings' => [
                    'content' => [
                        'source' => 'youtube',
                        'url' => '',
                        'videoId' => ''
                    ],
                    'settings' => [
                        'autoplay' => false,
                        'muted' => false,
                        'loop' => false,
                        'controls' => true,
                        'aspectRatio' => '16:9'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'source', 'type' => 'select', 'label' => 'Video Source', 'options' => ['youtube', 'vimeo', 'self-hosted']],
                        ['name' => 'url', 'type' => 'text', 'label' => 'Video URL'],
                        ['name' => 'videoId', 'type' => 'text', 'label' => 'Video ID']
                    ],
                    'style' => [
                        ['name' => 'aspectRatio', 'type' => 'select', 'label' => 'Aspect Ratio', 'options' => ['16:9', '4:3', '1:1', '9:16']],
                        ['name' => 'autoplay', 'type' => 'toggle', 'label' => 'Autoplay'],
                        ['name' => 'muted', 'type' => 'toggle', 'label' => 'Muted'],
                        ['name' => 'loop', 'type' => 'toggle', 'label' => 'Loop'],
                        ['name' => 'controls', 'type' => 'toggle', 'label' => 'Show Controls']
                    ]
                ]
            ],
            [
                'name' => 'gallery',
                'icon' => 'fas fa-images',
                'category' => 'media',
                'default_settings' => [
                    'content' => [
                        'images' => []
                    ],
                    'settings' => [
                        'columns' => 3,
                        'gap' => 10,
                        'lightbox' => true,
                        'captions' => true
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'images', 'type' => 'gallery', 'label' => 'Gallery Images']
                    ],
                    'style' => [
                        ['name' => 'columns', 'type' => 'slider', 'label' => 'Columns', 'min' => 1, 'max' => 6],
                        ['name' => 'gap', 'type' => 'slider', 'label' => 'Gap (px)', 'min' => 0, 'max' => 50],
                        ['name' => 'lightbox', 'type' => 'toggle', 'label' => 'Enable Lightbox'],
                        ['name' => 'captions', 'type' => 'toggle', 'label' => 'Show Captions']
                    ]
                ]
            ],

            // Advanced Elements
            [
                'name' => 'icon',
                'icon' => 'fas fa-icons',
                'category' => 'advanced',
                'default_settings' => [
                    'content' => [
                        'icon' => 'fas fa-star'
                    ],
                    'settings' => [
                        'size' => 40,
                        'color' => '#333333',
                        'link' => ''
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'icon', 'type' => 'icon', 'label' => 'Select Icon'],
                        ['name' => 'link', 'type' => 'text', 'label' => 'Link URL']
                    ],
                    'style' => [
                        ['name' => 'size', 'type' => 'slider', 'label' => 'Size (px)', 'min' => 10, 'max' => 200],
                        ['name' => 'color', 'type' => 'color', 'label' => 'Icon Color']
                    ]
                ]
            ],
            [
                'name' => 'accordion',
                'icon' => 'fas fa-bars',
                'category' => 'advanced',
                'default_settings' => [
                    'content' => [
                        'items' => [
                            ['title' => 'Accordion Item 1', 'content' => 'Content for item 1'],
                            ['title' => 'Accordion Item 2', 'content' => 'Content for item 2']
                        ]
                    ],
                    'settings' => [
                        'multiple' => false,
                        'firstOpen' => true
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'items', 'type' => 'repeater', 'label' => 'Accordion Items', 'fields' => [
                            ['name' => 'title', 'type' => 'text', 'label' => 'Title'],
                            ['name' => 'content', 'type' => 'wysiwyg', 'label' => 'Content']
                        ]]
                    ],
                    'style' => [
                        ['name' => 'multiple', 'type' => 'toggle', 'label' => 'Allow Multiple Open'],
                        ['name' => 'firstOpen', 'type' => 'toggle', 'label' => 'First Item Open']
                    ]
                ]
            ],
            [
                'name' => 'tabs',
                'icon' => 'fas fa-folder',
                'category' => 'advanced',
                'default_settings' => [
                    'content' => [
                        'tabs' => [
                            ['title' => 'Tab 1', 'content' => 'Content for tab 1'],
                            ['title' => 'Tab 2', 'content' => 'Content for tab 2']
                        ]
                    ],
                    'settings' => [
                        'type' => 'horizontal',
                        'alignment' => 'left'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'tabs', 'type' => 'repeater', 'label' => 'Tabs', 'fields' => [
                            ['name' => 'title', 'type' => 'text', 'label' => 'Tab Title'],
                            ['name' => 'content', 'type' => 'wysiwyg', 'label' => 'Tab Content']
                        ]]
                    ],
                    'style' => [
                        ['name' => 'type', 'type' => 'select', 'label' => 'Tab Type', 'options' => ['horizontal', 'vertical']],
                        ['name' => 'alignment', 'type' => 'select', 'label' => 'Alignment', 'options' => ['left', 'center', 'right']]
                    ]
                ]
            ],
            [
                'name' => 'counter',
                'icon' => 'fas fa-sort-numeric-up',
                'category' => 'advanced',
                'default_settings' => [
                    'content' => [
                        'start' => 0,
                        'end' => 100,
                        'prefix' => '',
                        'suffix' => '%',
                        'title' => 'Counter Title'
                    ],
                    'settings' => [
                        'duration' => 2000,
                        'delay' => 0
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'start', 'type' => 'number', 'label' => 'Start Number'],
                        ['name' => 'end', 'type' => 'number', 'label' => 'End Number'],
                        ['name' => 'prefix', 'type' => 'text', 'label' => 'Prefix'],
                        ['name' => 'suffix', 'type' => 'text', 'label' => 'Suffix'],
                        ['name' => 'title', 'type' => 'text', 'label' => 'Title']
                    ],
                    'style' => [
                        ['name' => 'duration', 'type' => 'slider', 'label' => 'Animation Duration (ms)', 'min' => 500, 'max' => 5000],
                        ['name' => 'delay', 'type' => 'slider', 'label' => 'Animation Delay (ms)', 'min' => 0, 'max' => 2000]
                    ]
                ]
            ],
            [
                'name' => 'progressbar',
                'icon' => 'fas fa-tasks',
                'category' => 'advanced',
                'default_settings' => [
                    'content' => [
                        'title' => 'Progress',
                        'value' => 75
                    ],
                    'settings' => [
                        'showPercentage' => true,
                        'animated' => true,
                        'striped' => false,
                        'color' => '#007cba'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'title', 'type' => 'text', 'label' => 'Title'],
                        ['name' => 'value', 'type' => 'slider', 'label' => 'Value (%)', 'min' => 0, 'max' => 100]
                    ],
                    'style' => [
                        ['name' => 'showPercentage', 'type' => 'toggle', 'label' => 'Show Percentage'],
                        ['name' => 'animated', 'type' => 'toggle', 'label' => 'Animated'],
                        ['name' => 'striped', 'type' => 'toggle', 'label' => 'Striped'],
                        ['name' => 'color', 'type' => 'color', 'label' => 'Bar Color']
                    ]
                ]
            ],

            // Form Elements
            [
                'name' => 'form',
                'icon' => 'fas fa-envelope',
                'category' => 'forms',
                'default_settings' => [
                    'content' => [
                        'action' => '/submit',
                        'method' => 'POST',
                        'fields' => []
                    ],
                    'settings' => [
                        'layout' => 'vertical',
                        'submitText' => 'Submit',
                        'successMessage' => 'Thank you for your submission!'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'action', 'type' => 'text', 'label' => 'Form Action'],
                        ['name' => 'method', 'type' => 'select', 'label' => 'Method', 'options' => ['POST', 'GET']],
                        ['name' => 'fields', 'type' => 'form_builder', 'label' => 'Form Fields']
                    ],
                    'style' => [
                        ['name' => 'layout', 'type' => 'select', 'label' => 'Layout', 'options' => ['vertical', 'horizontal', 'inline']],
                        ['name' => 'submitText', 'type' => 'text', 'label' => 'Submit Button Text'],
                        ['name' => 'successMessage', 'type' => 'textarea', 'label' => 'Success Message']
                    ]
                ]
            ],

            // Social Elements
            [
                'name' => 'social_icons',
                'icon' => 'fas fa-share-alt',
                'category' => 'social',
                'default_settings' => [
                    'content' => [
                        'icons' => [
                            ['platform' => 'facebook', 'url' => 'https://facebook.com'],
                            ['platform' => 'twitter', 'url' => 'https://twitter.com'],
                            ['platform' => 'instagram', 'url' => 'https://instagram.com']
                        ]
                    ],
                    'settings' => [
                        'style' => 'colored',
                        'shape' => 'circle',
                        'size' => 'medium'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'icons', 'type' => 'repeater', 'label' => 'Social Icons', 'fields' => [
                            ['name' => 'platform', 'type' => 'select', 'label' => 'Platform', 'options' => ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github']],
                            ['name' => 'url', 'type' => 'text', 'label' => 'URL']
                        ]]
                    ],
                    'style' => [
                        ['name' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => ['colored', 'monochrome', 'outline']],
                        ['name' => 'shape', 'type' => 'select', 'label' => 'Shape', 'options' => ['circle', 'square', 'rounded']],
                        ['name' => 'size', 'type' => 'select', 'label' => 'Size', 'options' => ['small', 'medium', 'large']]
                    ]
                ]
            ],

            // Commerce Elements
            [
                'name' => 'pricing_table',
                'icon' => 'fas fa-dollar-sign',
                'category' => 'commerce',
                'default_settings' => [
                    'content' => [
                        'plans' => [
                            [
                                'name' => 'Basic',
                                'price' => '$9',
                                'period' => '/month',
                                'features' => ['Feature 1', 'Feature 2', 'Feature 3'],
                                'buttonText' => 'Choose Plan',
                                'buttonLink' => '#',
                                'featured' => false
                            ]
                        ]
                    ],
                    'settings' => [
                        'columns' => 3,
                        'style' => 'card'
                    ]
                ],
                'fields_schema' => [
                    'content' => [
                        ['name' => 'plans', 'type' => 'repeater', 'label' => 'Pricing Plans', 'fields' => [
                            ['name' => 'name', 'type' => 'text', 'label' => 'Plan Name'],
                            ['name' => 'price', 'type' => 'text', 'label' => 'Price'],
                            ['name' => 'period', 'type' => 'text', 'label' => 'Period'],
                            ['name' => 'features', 'type' => 'list', 'label' => 'Features'],
                            ['name' => 'buttonText', 'type' => 'text', 'label' => 'Button Text'],
                            ['name' => 'buttonLink', 'type' => 'text', 'label' => 'Button Link'],
                            ['name' => 'featured', 'type' => 'toggle', 'label' => 'Featured']
                        ]]
                    ],
                    'style' => [
                        ['name' => 'columns', 'type' => 'slider', 'label' => 'Columns', 'min' => 1, 'max' => 4],
                        ['name' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => ['card', 'minimal', 'bordered']]
                    ]
                ]
            ]
        ];

        foreach ($elements as $element) {
            PbElementType::create($element);
        }
    }
}