<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FaqCategoryController extends Controller
{
    /**
     * Display a listing of FAQ categories.
     */
    public function index(Request $request)
    {
        $search = $request->query('q');

        $query = FaqCategory::withCount('faqs')->ordered();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('admin.faq-categories.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new FAQ category.
     */
    public function create()
    {
        $nextOrder = (FaqCategory::max('order') ?? 0) + 1;

        return view('admin.faq-categories.create', compact('nextOrder'));
    }

    /**
     * Store a newly created FAQ category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:faq_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'order.required' => 'Urutan tampil wajib diisi.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category = FaqCategory::create($validated);

        ActivityLogService::log(
            action: 'faq_category_created',
            entity: $category,
            description: "Membuat kategori FAQ baru: {$category->name}",
            newValues: $category->toArray()
        );

        return redirect()->route('admin.faq-categories.index')->with('success', 'Kategori FAQ berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified FAQ category.
     */
    public function edit(FaqCategory $faqCategory)
    {
        return view('admin.faq-categories.edit', compact('faqCategory'));
    }

    /**
     * Update the specified FAQ category in storage.
     */
    public function update(Request $request, FaqCategory $faqCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('faq_categories', 'name')->ignore($faqCategory->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'order.required' => 'Urutan tampil wajib diisi.',
        ]);

        $oldValues = $faqCategory->toArray();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $faqCategory->update($validated);

        // Also update plain category string on linked faqs for consistency
        $faqCategory->faqs()->update(['category' => $faqCategory->name]);

        ActivityLogService::log(
            action: 'faq_category_updated',
            entity: $faqCategory,
            description: "Memperbarui kategori FAQ: {$faqCategory->name}",
            oldValues: $oldValues,
            newValues: $faqCategory->fresh()->toArray()
        );

        return redirect()->route('admin.faq-categories.index')->with('success', 'Kategori FAQ berhasil diperbarui.');
    }

    /**
     * Remove the specified FAQ category from storage.
     */
    public function destroy(FaqCategory $faqCategory)
    {
        $old = $faqCategory->toArray();
        $name = $faqCategory->name;

        $faqCategory->delete();

        ActivityLogService::log(
            action: 'faq_category_deleted',
            entity: $faqCategory,
            description: "Menghapus kategori FAQ: {$name}",
            oldValues: $old
        );

        return redirect()->route('admin.faq-categories.index')->with('success', 'Kategori FAQ berhasil dihapus.');
    }
}
