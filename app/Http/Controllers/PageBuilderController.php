<?php

namespace App\Http\Controllers;

use App\Models\PageBuilder\PbPage;
use App\Models\PageBuilder\PbSection;
use App\Models\PageBuilder\PbColumn;
use App\Models\PageBuilder\PbElement;
use App\Models\PageBuilder\PbElementType;
use App\Models\PageBuilder\PbTemplate;
use App\Models\PageBuilder\PbRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PageBuilderController extends Controller
{
    public function index()
    {
        $pages = PbPage::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('page-builder.index', compact('pages'));
    }

    public function editor($id = null)
    {
        $page = null;
        if ($id) {
            $page = PbPage::with([
                'sections.columns.elements',
                'assets',
                'revisions' => function($q) { $q->limit(10); }
            ])->findOrFail($id);
        }

        $elementTypes = PbElementType::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $templates = PbTemplate::orderBy('category')->get()->groupBy('category');

        // Use simple editor for now
        return view('page-builder.simple-editor', compact('page', 'elementTypes', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:pb_pages,slug',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string'
        ]);

        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $page = PbPage::create($validated);

        return response()->json([
            'success' => true,
            'page' => $page,
            'redirect' => route('page-builder.editor', $page->id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $page = PbPage::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:pb_pages,slug,' . $id,
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'sometimes|in:draft,published',
            'settings' => 'nullable|array'
        ]);

        $page->update($validated);

        return response()->json([
            'success' => true,
            'page' => $page
        ]);
    }

    public function saveContent(Request $request, $id)
    {
        $page = PbPage::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Create revision before saving
            PbRevision::create([
                'page_id' => $page->id,
                'content' => json_encode($page->sections->load('columns.elements')),
                'revision_type' => $request->input('revision_type', 'auto'),
                'created_by' => Auth::id()
            ]);

            // Delete existing content
            $page->sections()->delete();

            // Save new content
            $sections = $request->input('sections', []);
            
            foreach ($sections as $sectionIndex => $sectionData) {
                $section = $page->sections()->create([
                    'order' => $sectionIndex,
                    'type' => $sectionData['type'] ?? 'row',
                    'settings' => $sectionData['settings'] ?? [],
                    'responsive_settings' => $sectionData['responsive_settings'] ?? [],
                    'is_visible' => $sectionData['is_visible'] ?? true
                ]);

                foreach ($sectionData['columns'] ?? [] as $columnIndex => $columnData) {
                    $column = $section->columns()->create([
                        'order' => $columnIndex,
                        'width' => $columnData['width'] ?? 12,
                        'responsive_width' => $columnData['responsive_width'] ?? [],
                        'settings' => $columnData['settings'] ?? []
                    ]);

                    foreach ($columnData['elements'] ?? [] as $elementIndex => $elementData) {
                        $column->elements()->create([
                            'type' => $elementData['type'],
                            'order' => $elementIndex,
                            'content' => $elementData['content'] ?? [],
                            'settings' => $elementData['settings'] ?? [],
                            'animations' => $elementData['animations'] ?? [],
                            'responsive_settings' => $elementData['responsive_settings'] ?? [],
                            'is_visible' => $elementData['is_visible'] ?? true
                        ]);
                    }
                }
            }

            // Save custom CSS/JS if provided
            if ($request->has('custom_css') || $request->has('custom_js')) {
                $page->assets()->updateOrCreate(
                    ['page_id' => $page->id],
                    [
                        'custom_css' => $request->input('custom_css'),
                        'custom_js' => $request->input('custom_js'),
                        'external_assets' => $request->input('external_assets', [])
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Page content saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error saving page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate($id)
    {
        $page = PbPage::findOrFail($id);
        $newPage = $page->duplicate();

        return response()->json([
            'success' => true,
            'page' => $newPage,
            'redirect' => route('page-builder.editor', $newPage->id)
        ]);
    }

    public function preview($id)
    {
        $page = PbPage::with([
            'sections.columns.elements',
            'assets'
        ])->findOrFail($id);

        return view('page-builder.preview', compact('page'));
    }

    public function publish($id)
    {
        $page = PbPage::findOrFail($id);
        $page->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => 'Page published successfully'
        ]);
    }

    public function getRevisions($id)
    {
        $revisions = PbRevision::where('page_id', $id)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($revisions);
    }

    public function restoreRevision($pageId, $revisionId)
    {
        $page = PbPage::findOrFail($pageId);
        $revision = PbRevision::where('page_id', $pageId)
            ->where('id', $revisionId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Save current state as a new revision
            PbRevision::create([
                'page_id' => $page->id,
                'content' => json_encode($page->sections->load('columns.elements')),
                'revision_type' => 'before_restore',
                'created_by' => Auth::id()
            ]);

            // Clear current content
            $page->sections()->delete();

            // Restore from revision
            $content = json_decode($revision->content, true);
            
            foreach ($content as $sectionData) {
                $section = $page->sections()->create([
                    'order' => $sectionData['order'],
                    'type' => $sectionData['type'],
                    'settings' => $sectionData['settings'],
                    'responsive_settings' => $sectionData['responsive_settings'],
                    'is_visible' => $sectionData['is_visible']
                ]);

                foreach ($sectionData['columns'] ?? [] as $columnData) {
                    $column = $section->columns()->create([
                        'order' => $columnData['order'],
                        'width' => $columnData['width'],
                        'responsive_width' => $columnData['responsive_width'],
                        'settings' => $columnData['settings']
                    ]);

                    foreach ($columnData['elements'] ?? [] as $elementData) {
                        $column->elements()->create([
                            'type' => $elementData['type'],
                            'order' => $elementData['order'],
                            'content' => $elementData['content'],
                            'settings' => $elementData['settings'],
                            'animations' => $elementData['animations'],
                            'responsive_settings' => $elementData['responsive_settings'],
                            'is_visible' => $elementData['is_visible']
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Revision restored successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error restoring revision: ' . $e->getMessage()
            ], 500);
        }
    }
}