<?php

namespace App\Http\Controllers\Admin;

use App\Banner;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = Banner::all();
        return view('admin.banner.index',compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'sequence' => 'required',
            'image' => 'required|mimes:jpeg,bmp,png,jpg'
        ]);
        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->sequence);
        if (isset($image))
        {
//            make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
//            check banner dir is exists
            /*if (!Storage::disk('public')->exists('banner'))
            {
                Storage::disk('public')->makeDirectory('banner');
            }
//            resize image for banner and upload
            $banner = Image::make($image)->resize(1600,479)->stream();
            Storage::disk('public')->put('banner/'.$imagename,$banner);*/

            if (!file_exists('uploads/banner'))
            {
                mkdir('uploads/banner',0777,true);
            }
            $image->move('uploads/banner',$imagename);

        } else {
            $imagename = "default.jpg";
        }

        $banner = new Banner();
        $banner->sequence = $request->sequence;
        $banner->image = $imagename;
        $banner->save();
        Toastr::success('Banner Successfully Saved' ,'Success');
        return redirect()->route('admin.banner.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $banner = Banner::find($id);
        return view('admin.banner.edit',compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'sequence' => 'required',
            'image' => 'mimes:jpeg,bmp,png,jpg'
        ]);
        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->sequence);
        $banner = Banner::find($id);
        if (isset($image))
        {
//            make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
//            check banner dir is exists
            /*if (!Storage::disk('public')->exists('banner'))
            {
                Storage::disk('public')->makeDirectory('banner');
            }
//            delete old image
            if (Storage::disk('public')->exists('banner/'.$banner->image))
            {
                Storage::disk('public')->delete('banner/'.$banner->image);
            }
//            resize image for banner and upload
            $bannerimage = Image::make($image)->resize(1600,479)->stream();
            Storage::disk('public')->put('banner/'.$imagename,$bannerimage);*/

            if (!file_exists('uploads/banner'))
            {
                mkdir('uploads/banner',0777,true);
            }
            $image->move('uploads/banner',$imagename);

        } else {
            $imagename = $banner->image;
        }

        $banner->sequence = $request->sequence;
        $banner->image = $imagename;
        $banner->save();
        Toastr::success('Banner Successfully Updated :)' ,'Success');
        return redirect()->route('admin.banner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        if (Storage::disk('public')->exists('banner/'.$banner->image))
        {
            Storage::disk('public')->delete('banner/'.$banner->image);
        }

        if (Storage::disk('public')->exists('banner/slider/'.$banner->image))
        {
            Storage::disk('public')->delete('banner/slider/'.$banner->image);
        }
        $banner->delete();
        Toastr::success('Banner Successfully Deleted :)','Success');
        return redirect()->back();
    }
}
