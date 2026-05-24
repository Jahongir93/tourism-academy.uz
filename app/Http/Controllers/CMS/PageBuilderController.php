<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageBuilderController extends Controller
{
    public function builder($id)
    {
        $page = CmsPage::findOrFail($id);
        
        return view('cms.pages.builder', compact('page'));
    }
    
    public function save(Request $request, $id)
    {
        $page = CmsPage::findOrFail($id);
        
        $request->validate([
            'content' => 'required|array',
            'styles' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);
        
        // Save builder data as JSON
        $builderData = [
            'elements' => $request->content,
            'styles' => $request->styles ?? [],
            'settings' => $request->settings ?? [],
            'version' => '1.0',
            'updated_at' => now()
        ];
        
        // Store in appropriate language field
        $lang = $request->lang ?? 'uz';
        $contentField = "content_{$lang}";
        
        $page->$contentField = $builderData;
        $page->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Sahifa muvaffaqiyatli saqlandi',
            'data' => $builderData
        ]);
    }
    
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ]);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('page-builder', 'public');
            
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Rasm yuklanmadi'
        ], 400);
    }
    
    public function getTemplates()
    {
        $templates = [
            [
                'id' => 'hero-1',
                'name' => 'Hero Section 1',
                'category' => 'hero',
                'thumbnail' => '/images/templates/hero1.jpg',
                'content' => [
                    'type' => 'section',
                    'classes' => 'hero-section',
                    'elements' => [
                        [
                            'type' => 'heading',
                            'tag' => 'h1',
                            'content' => 'Welcome to Our Website',
                            'classes' => 'display-1 text-center'
                        ],
                        [
                            'type' => 'text',
                            'content' => 'Build amazing pages with our visual builder',
                            'classes' => 'lead text-center'
                        ],
                        [
                            'type' => 'button',
                            'content' => 'Get Started',
                            'classes' => 'btn btn-primary btn-lg',
                            'href' => '#'
                        ]
                    ]
                ]
            ],
            [
                'id' => 'features-1',
                'name' => 'Features Grid',
                'category' => 'features',
                'thumbnail' => '/images/templates/features1.jpg',
                'content' => [
                    'type' => 'section',
                    'classes' => 'features-section',
                    'elements' => [
                        [
                            'type' => 'row',
                            'columns' => [
                                [
                                    'width' => 'col-md-4',
                                    'elements' => [
                                        [
                                            'type' => 'icon',
                                            'icon' => 'fas fa-rocket',
                                            'size' => '3x',
                                            'color' => '#007bff'
                                        ],
                                        [
                                            'type' => 'heading',
                                            'tag' => 'h3',
                                            'content' => 'Fast Performance'
                                        ],
                                        [
                                            'type' => 'text',
                                            'content' => 'Lightning fast page load speeds'
                                        ]
                                    ]
                                ],
                                [
                                    'width' => 'col-md-4',
                                    'elements' => [
                                        [
                                            'type' => 'icon',
                                            'icon' => 'fas fa-shield-alt',
                                            'size' => '3x',
                                            'color' => '#28a745'
                                        ],
                                        [
                                            'type' => 'heading',
                                            'tag' => 'h3',
                                            'content' => 'Secure'
                                        ],
                                        [
                                            'type' => 'text',
                                            'content' => 'Built with security in mind'
                                        ]
                                    ]
                                ],
                                [
                                    'width' => 'col-md-4',
                                    'elements' => [
                                        [
                                            'type' => 'icon',
                                            'icon' => 'fas fa-mobile-alt',
                                            'size' => '3x',
                                            'color' => '#6610f2'
                                        ],
                                        [
                                            'type' => 'heading',
                                            'tag' => 'h3',
                                            'content' => 'Responsive'
                                        ],
                                        [
                                            'type' => 'text',
                                            'content' => 'Works on all devices'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        return response()->json($templates);
    }
    
    public function preview($id)
    {
        $page = CmsPage::findOrFail($id);
        
        return view('cms.pages.preview-builder', compact('page'));
    }
    
    public function getElement($type)
    {
        $elements = [
            'text' => [
                'type' => 'text',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'classes' => '',
                'styles' => []
            ],
            'heading' => [
                'type' => 'heading',
                'tag' => 'h2',
                'content' => 'Heading Text',
                'classes' => '',
                'styles' => []
            ],
            'image' => [
                'type' => 'image',
                'src' => 'https://via.placeholder.com/600x400',
                'alt' => 'Placeholder Image',
                'classes' => 'img-fluid',
                'styles' => []
            ],
            'video' => [
                'type' => 'video',
                'provider' => 'youtube',
                'videoId' => '',
                'autoplay' => false,
                'controls' => true,
                'styles' => []
            ],
            'button' => [
                'type' => 'button',
                'content' => 'Click Me',
                'href' => '#',
                'target' => '_self',
                'classes' => 'btn btn-primary',
                'styles' => []
            ],
            'divider' => [
                'type' => 'divider',
                'style' => 'solid',
                'color' => '#dee2e6',
                'thickness' => '1px',
                'styles' => []
            ],
            'spacer' => [
                'type' => 'spacer',
                'height' => '50px',
                'styles' => []
            ],
            'icon' => [
                'type' => 'icon',
                'icon' => 'fas fa-star',
                'size' => '2x',
                'color' => '#ffc107',
                'styles' => []
            ],
            'social' => [
                'type' => 'social',
                'networks' => [
                    ['name' => 'facebook', 'url' => '#', 'icon' => 'fab fa-facebook'],
                    ['name' => 'twitter', 'url' => '#', 'icon' => 'fab fa-twitter'],
                    ['name' => 'instagram', 'url' => '#', 'icon' => 'fab fa-instagram']
                ],
                'styles' => []
            ],
            'accordion' => [
                'type' => 'accordion',
                'items' => [
                    [
                        'title' => 'Accordion Item 1',
                        'content' => 'Content for item 1',
                        'expanded' => true
                    ],
                    [
                        'title' => 'Accordion Item 2',
                        'content' => 'Content for item 2',
                        'expanded' => false
                    ]
                ],
                'styles' => []
            ],
            'tabs' => [
                'type' => 'tabs',
                'items' => [
                    [
                        'title' => 'Tab 1',
                        'content' => 'Content for tab 1',
                        'active' => true
                    ],
                    [
                        'title' => 'Tab 2',
                        'content' => 'Content for tab 2',
                        'active' => false
                    ]
                ],
                'styles' => []
            ],
            'progressbar' => [
                'type' => 'progressbar',
                'value' => 75,
                'max' => 100,
                'label' => 'Progress',
                'color' => '#007bff',
                'striped' => false,
                'animated' => false,
                'styles' => []
            ],
            'countdown' => [
                'type' => 'countdown',
                'targetDate' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'format' => 'DHMS',
                'labels' => [
                    'days' => 'Days',
                    'hours' => 'Hours',
                    'minutes' => 'Minutes',
                    'seconds' => 'Seconds'
                ],
                'styles' => []
            ],
            'chart' => [
                'type' => 'chart',
                'chartType' => 'bar',
                'data' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                    'datasets' => [
                        [
                            'label' => 'Sales',
                            'data' => [12, 19, 3, 5, 2],
                            'backgroundColor' => '#007bff'
                        ]
                    ]
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false
                ],
                'styles' => []
            ],
            'row' => [
                'type' => 'row',
                'columns' => [
                    [
                        'width' => 'col-md-6',
                        'elements' => []
                    ],
                    [
                        'width' => 'col-md-6',
                        'elements' => []
                    ]
                ],
                'styles' => []
            ],
            'column' => [
                'type' => 'column',
                'width' => 'col-md-12',
                'elements' => [],
                'styles' => []
            ]
        ];
        
        if (isset($elements[$type])) {
            return response()->json($elements[$type]);
        }
        
        return response()->json(['error' => 'Element type not found'], 404);
    }
}