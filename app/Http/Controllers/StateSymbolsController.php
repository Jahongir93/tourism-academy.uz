<?php

namespace App\Http\Controllers;

use App\Models\CmsContent;
use Illuminate\Http\Request;

class StateSymbolsController extends Controller
{
    /**
     * Display the state symbols page
     */
    public function index()
    {
        // Get current language
        $lang = app()->getLocale();
        $langField = 'value_' . $lang;

        // Get all state symbols content from database
        $contents = CmsContent::where('section', 'state_symbols')->orderBy('order')->get()->keyBy('key');

        // Build anthem data
        $anthem = [
            'title' => $contents->get('symbols_anthem_title')?->$langField ?? "O'zbekiston Respublikasining Davlat Madhiyasi",
            'author_lyrics' => $contents->get('symbols_anthem_author_lyrics')?->value_uz ?? 'Abdulla Oripov',
            'author_music' => $contents->get('symbols_anthem_author_music')?->value_uz ?? 'Mutal Burhonov',
            'adopted' => $contents->get('symbols_anthem_adopted')?->$langField ?? '1992-yil 10-dekabr',
            'audio' => $contents->get('symbols_anthem_audio')?->value_uz ?? null,
            'verses' => [
                [
                    'verse' => $contents->get('symbols_anthem_verse1')?->$langField ?? '',
                    'chorus' => $contents->get('symbols_anthem_chorus')?->$langField ?? ''
                ],
                [
                    'verse' => $contents->get('symbols_anthem_verse2')?->$langField ?? '',
                    'chorus' => $contents->get('symbols_anthem_chorus')?->$langField ?? ''
                ],
            ]
        ];

        // Build flag data
        $flag = [
            'image' => $contents->get('symbols_flag_image')?->value_uz ?? null,
            'adopted' => $contents->get('symbols_flag_adopted')?->$langField ?? '1991-yil 18-noyabr',
            'colors' => [],
            'elements' => []
        ];

        // Get flag colors
        for ($i = 1; $i <= 4; $i++) {
            $flag['colors'][] = [
                'name' => $contents->get("symbols_flag_color{$i}_name")?->$langField ?? '',
                'meaning' => $contents->get("symbols_flag_color{$i}_meaning")?->$langField ?? '',
                'hex' => $contents->get("symbols_flag_color{$i}_hex")?->value_uz ?? '#000000'
            ];
        }

        // Get flag elements
        for ($i = 1; $i <= 2; $i++) {
            $element = $contents->get("symbols_flag_element{$i}")?->$langField;
            if ($element) {
                $flag['elements'][] = $element;
            }
        }

        // Build emblem data
        $emblem = [
            'adopted' => $contents->get('symbols_emblem_adopted')?->$langField ?? '1992-yil 2-iyul',
            'image' => $contents->get('symbols_emblem_image')?->value_uz ?? null,
            'elements' => []
        ];

        // Get emblem elements
        for ($i = 1; $i <= 6; $i++) {
            $name = $contents->get("symbols_emblem_element{$i}_name")?->$langField;
            $meaning = $contents->get("symbols_emblem_element{$i}_meaning")?->$langField;
            if ($name) {
                $emblem['elements'][] = [
                    'name' => $name,
                    'meaning' => $meaning ?? ''
                ];
            }
        }

        // Footer data
        $footer = [
            'title' => $contents->get('symbols_footer_title')?->$langField ?? 'Davlat ramzlariga hurmat',
            'text' => $contents->get('symbols_footer_text')?->$langField ?? ''
        ];

        return view('pages.state-symbols', compact('anthem', 'flag', 'emblem', 'footer'));
    }
}
