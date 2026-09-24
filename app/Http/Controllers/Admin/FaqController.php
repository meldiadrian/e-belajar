<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs for management.
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $category = $request->query('category');

        $query = Faq::with('faqCategory')->ordered();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where(function ($q) use ($category) {
                $q->where('category', $category)
                  ->orWhereHas('faqCategory', fn($c) => $c->where('name', $category));
            });
        }

        $faqs = $query->paginate(15)->withQueryString();

        $categories = FaqCategory::ordered()->pluck('name');
        if ($categories->isEmpty()) {
            $categories = Faq::select('category')->distinct()->pluck('category')->filter()->values();
        }

        return view('admin.faqs.index', compact('faqs', 'categories', 'search', 'category'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create()
    {
        $categories = FaqCategory::active()->ordered()->get();
        $nextOrder = (Faq::max('order') ?? 0) + 1;

        return view('admin.faqs.create', compact('categories', 'nextOrder'));
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'faq_category_id' => ['nullable', 'exists:faq_categories,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'order' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable'],
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'answer.required' => 'Jawaban wajib diisi.',
            'order.required' => 'Urutan tampil wajib diisi.',
        ]);

        if (!empty($validated['faq_category_id'])) {
            $cat = FaqCategory::find($validated['faq_category_id']);
            $validated['category'] = $cat?->name ?? 'Umum';
        } elseif (!empty($validated['category'])) {
            $cat = FaqCategory::firstOrCreate(
                ['name' => $validated['category']],
                ['slug' => Str::slug($validated['category']), 'order' => 0, 'is_active' => true]
            );
            $validated['faq_category_id'] = $cat->id;
        } else {
            $cat = FaqCategory::firstOrCreate(
                ['name' => 'Umum'],
                ['slug' => 'umum', 'order' => 1, 'is_active' => true]
            );
            $validated['faq_category_id'] = $cat->id;
            $validated['category'] = 'Umum';
        }

        $validated['is_published'] = $request->has('is_published');

        $faq = Faq::create($validated);

        ActivityLogService::log(
            action: 'faq_created',
            entity: $faq,
            description: "Membuat FAQ baru: {$faq->question}",
            newValues: $faq->toArray()
        );

        return redirect()->route('admin.faqs.index')->with('success', 'Pertanyaan Umum (FAQ) berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(Faq $faq)
    {
        $categories = FaqCategory::ordered()->get();

        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'faq_category_id' => ['nullable', 'exists:faq_categories,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'order' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable'],
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'answer.required' => 'Jawaban wajib diisi.',
            'order.required' => 'Urutan tampil wajib diisi.',
        ]);

        if (!empty($validated['faq_category_id'])) {
            $cat = FaqCategory::find($validated['faq_category_id']);
            $validated['category'] = $cat?->name ?? $faq->category;
        } elseif (!empty($validated['category'])) {
            $cat = FaqCategory::firstOrCreate(
                ['name' => $validated['category']],
                ['slug' => Str::slug($validated['category']), 'order' => 0, 'is_active' => true]
            );
            $validated['faq_category_id'] = $cat->id;
        }

        $oldValues = $faq->toArray();
        $validated['is_published'] = $request->has('is_published');

        $faq->update($validated);

        ActivityLogService::log(
            action: 'faq_updated',
            entity: $faq,
            description: "Memperbarui FAQ: {$faq->question}",
            oldValues: $oldValues,
            newValues: $faq->fresh()->toArray()
        );

        return redirect()->route('admin.faqs.index')->with('success', 'Pertanyaan Umum (FAQ) berhasil diperbarui.');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq)
    {
        $old = $faq->toArray();
        $question = $faq->question;
        $faq->delete();

        ActivityLogService::log(
            action: 'faq_deleted',
            entity: $faq,
            description: "Menghapus FAQ: {$question}",
            oldValues: $old
        );

        return redirect()->route('admin.faqs.index')->with('success', 'Pertanyaan Umum (FAQ) berhasil dihapus.');
    }
}
