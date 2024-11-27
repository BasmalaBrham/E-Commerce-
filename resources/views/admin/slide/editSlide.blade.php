@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Slide</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.slides')}}">
                        <div class="text-tiny">Slider</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit Slide</div>
                </li>
            </ul>
        </div>
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.slide.update')}}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')
                <input type="hidden" name="id" value="{{$slide->id}}">
                <fieldset class="name">
                    <div class="body-title">Tagline <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Tagline" name="tagline" tabindex="0" value="{{$slide->tagline}}" aria-required="true" required="">
                </fieldset>
                @error('tagline')
                    <span class="alert alert-danger text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Title <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Title" name="title" tabindex="0" value="{{$slide->title}}" aria-required="true" required="">
                </fieldset>
                @error('title')
                    <span class="alert alert-danger text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Subtitle <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Subtitle" name="subtitle" tabindex="0" value="{{$slide->subtitle}}" aria-required="true" required="">
                </fieldset>
                @error('subtitle')
                    <span class="alert alert-danger text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Link <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Link" name="link" tabindex="0" value="{{$slide->link}}" aria-required="true" required="">
                </fieldset>
                @error('link')
                    <span class="alert alert-danger text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        @if ($slide->image)
                        <div class="item" id="imgpreview">
                            <img src="{{asset($slide->image)}}" class="effect8" alt="{{$slide->name}}">
                        </div>
                        @endif
                        <div class="item up-load" id="upload-file">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')
                    <span class="alert alert-danger text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <fieldset class="category">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select class="" name="status">
                            <option>Select</option>
                            <option value="1" @if ($slide->status=='1') selected @endif>Active</option>
                            <option value="0" @if ($slide->status=='0') selected @endif>Inactive</option>
                        </select>
                    </div>
                </fieldset>
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push("scripts")
<script>
$(function() {
    $("#myFile").on("change", function(e) {
    const file = this.files[0];
    if (file) {
        $("#imgpreview img").attr("src", URL.createObjectURL(file));
        $("#imgpreview").css("display", "block"); // تصحيح العرض
    }
    });
});
</script>
@endpush
