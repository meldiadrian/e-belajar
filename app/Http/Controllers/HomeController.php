<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Course::where('status', 'published')
            ->with(['category', 'creator', 'tags'])
            ->withCount(['lessons', 'enrollments'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount(['courses' => fn($q) => $q->where('status', 'published')])
            ->get();

        $stats = [
            'total_students' => User::where('role', 'user')->count(),
            'total_courses' => Course::where('status', 'published')->count(),
            'total_enrollments' => Enrollment::count(),
            'total_certificates' => Certificate::count(),
        ];

        return view('home', compact('featuredCourses', 'categories', 'stats'));
    }
}
