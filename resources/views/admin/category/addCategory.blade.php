@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Category infomation</h3>
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
                    <a href="{{route('admin.categories')}}">
                        <div class="text-tiny">Categories</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Category</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.category.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Ctegory Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow @error('name') is_invalid @enderror" type="text" placeholder="Ctegory name" name="name" tabindex="0" value="{{old('name')}}" aria-required="true" required="">
                </fieldset>
                    @error('name')
                        <span class="alert alert-danger text-center" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                <fieldset class="name">
                    <div class="body-title">Ctegory Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow @error('slug') is_invalid @enderror" type="text" placeholder="Ctegory Slug" name="slug" tabindex="0" value="{{old('slug')}}" aria-required="true" required="">
                </fieldset>
                    @error('slug')
                        <span class="alert alert-danger text-center" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="upload-1.html" class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                    @error('image')
                            <span class="alert alert-danger text-center" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                    @enderror
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
            $("#imgpreview").show();
        }
    });

    $("input[name='name']").on("input", function() {
        $("input[name='slug']").val(StringToSlug($(this).val()));
    });
});

// تحويل النص إلى صيغة slug
function StringToSlug(Text) {
    return Text.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}
</script>
@endpush