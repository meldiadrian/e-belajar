<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display the public FAQs page.
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $selectedCategory = $request->query('category');

        $query = Faq::with('faqCategory')->published()->ordered();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($selectedCategory) {
            $query->where(function ($q) use ($selectedCategory) {
                $q->where('category', $selectedCategory)
                  ->orWhereHas('faqCategory', fn($c) => $c->where('name', $selectedCategory)->orWhere('slug', $selectedCategory));
            });
        }

        $faqs = $query->get();

        // Get dynamic active categories that have at least one published FAQ (or all active categories)
        $categories = FaqCategory::active()->ordered()->pluck('name');
        if ($categories->isEmpty()) {
            $categories = Faq::published()
                ->select('category')
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();
        }

        return view('faqs.index', compact('faqs', 'categories', 'selectedCategory', 'search'));
    }
}
