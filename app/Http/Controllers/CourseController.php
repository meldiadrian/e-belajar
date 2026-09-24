<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Tag;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['category', 'creator', 'tags'])
            ->withCount(['lessons', 'enrollments']);

        // Non-admin can only see published courses
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            $query->where('status', 'published');
        } elseif ($request->has('status') && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search title & description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category)
                    ->orWhere('id', $request->category);
            });
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag)
                    ->orWhere('id', $request->tag);
            });
        }

        // Filter by level (dinonaktifkan/dihapus sesuai instruksi)

        // Sorting
        $sort = $request->get('sort', 'newest');
        if ($sort === 'popular') {
            $query->orderByDesc('enrollments_count');
        } elseif ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        $courses = $query->paginate(9)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('courses')->get();
        $tags = Tag::all();

        if ($request->wantsJson()) {
            return response()->json($courses);
        }

        return view('courses.index', compact('courses', 'categories', 'tags'));
    }

    public function show($identifier)
    {
        $course = Course::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->with([
                'category',
                'creator',
                'tags',
                'modules' => function ($q) {
                    $q->orderBy('sort_order')->with([
                        'lessons' => function ($lq) {
                            $lq->orderBy('sort_order');
                        }
                    ]);
                },
                'quizzes',
            ])
            ->firstOrFail();

        $isEnrolled = false;
        $courseProgress = null;

        if (Auth::check()) {
            $isEnrolled = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($isEnrolled) {
                $courseProgress = $course->courseProgress()
                    ->where('user_id', Auth::id())
                    ->first();
            }
        }

        if (request()->wantsJson()) {
            return response()->json([
                'course' => $course,
                'is_enrolled' => $isEnrolled,
                'progress' => $courseProgress,
            ]);
        }

        return view('courses.show', compact('course', 'isEnrolled', 'courseProgress'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', 'in:beginner,intermediate,advanced'],
            'status' => ['required', 'in:draft,published,archived'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'certificate_enabled' => ['boolean'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'tags' => ['nullable', 'array'],
        ]);

        $slug = Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Course::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-{$counter}";
            $counter++;
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = Course::create([
            'category_id' => $validated['category_id'],
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'thumbnail' => $thumbnailPath,
            'level' => $validated['level'] ?? 'beginner',
            'status' => $validated['status'],
            'duration' => $validated['duration'] ?? 0,
            'certificate_enabled' => $request->boolean('certificate_enabled', true),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if (!empty($validated['tags'])) {
            $course->tags()->sync($validated['tags']);
        }

        ActivityLogService::log(
            action: 'course_created',
            entity: $course,
            description: "Kursus baru dibuat: '{$course->title}'",
            newValues: $course->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kursus berhasil dibuat.', 'course' => $course], 201);
        }

        return redirect()->route('admin.courses.builder', $course->id)
            ->with('success', 'Kursus berhasil dibuat. Silakan tambahkan modul dan lesson.');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', 'in:beginner,intermediate,advanced'],
            'status' => ['sometimes', 'in:draft,published,archived'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'certificate_enabled' => ['boolean'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'tags' => ['nullable', 'array'],
        ]);

        $oldValues = $course->toArray();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if (isset($validated['status']) && $validated['status'] === 'published' && !$course->published_at) {
            $validated['published_at'] = now();
        }

        $course->update($validated);

        if (isset($validated['tags'])) {
            $course->tags()->sync($validated['tags']);
        }

        ActivityLogService::log(
            action: 'course_updated',
            entity: $course,
            description: "Kursus '{$course->title}' diperbarui.",
            oldValues: $oldValues,
            newValues: $course->fresh()->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kursus berhasil diperbarui.', 'course' => $course]);
        }

        return back()->with('success', 'Informasi kursus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $courseTitle = $course->title;
        $course->delete();

        ActivityLogService::log(
            action: 'course_deleted',
            entity: $course,
            description: "Kursus '{$courseTitle}' dihapus ke arsip (soft delete)."
        );

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Kursus berhasil dihapus.']);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil dihapus.');
    }
}
