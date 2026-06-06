<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\CMS\Models\Testimonial;


class TestimonialController extends Controller
{
    /**
     * Display a listing of the testimonials.
     */
    public function index()
    {
        return view('cms::testimonials.index');
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create()
    {
        return view('cms::testimonials.create');
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return view('cms::testimonials.edit', compact('testimonial'));
    }


}
