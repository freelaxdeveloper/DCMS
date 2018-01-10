<div class="post clearfix icon @if($bottom)bottom @endif @if($time)time @endif @if($content)content @endif @if(isset($counter))counter @endif"
    data-ng-controller="ListingPostCtrl"
    data-post-url="{{$url}}">
    <div class="post_head">
        <span class="post_icon">
            <img src="{{ App\App\App::icon($icon) }}" alt="">
        </span>
        <a class="post_title" href="{{$url}}">{{$title}}</a>
        @if (!empty($counter))
            <span class="post_counter">{{$counter}}</span>
        @endif
        @if (!empty($time))
            <span class="post_time">{{ App\misc::when($time) }}</span>
        @endif
    </div>
    @if (!empty($content))
        <div class="post_content">@toOutput($content)</div>
    @endif
    @if (!empty($bottom))
        <div class="post_bottom">@toOutput($bottom)</div>
    @endif
</div>
