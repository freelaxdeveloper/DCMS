<div id="{{$id}}"
     class="post clearfix icon time @if ($bottom)bottom @endif @if ($counter)counter @endif @if ($content)content @endif"
     data-ng-controller="ListingPostCtrl"
     data-post-url="{{$url}}">

    <div class="post_image"><img src="<?= $image ?>" alt=""></div>
    <div class="post_head">
        <span class="post_icon">
            @if ($icon_class)
                <span class="{{$icon_class}}"></span>
            @else
                <img src="{{$icon}}" alt="">
            @endif
        </span>
        <a class="post_title" @if ($url) href="{{$url}}" @endif>{!! $title !!}</a>
        <span class="post_actions">
            @foreach ($actions as $action)
                <a href="{{$action->url}}"><img src="{{$action->icon}}" alt="" /></a>
            @endforeach
        </span>
        <span class="post_counter">{{$counter}}</span>
        <span class="post_time">{{$time}}</span>
    </div>
    <div class="post_content">{!! $content !!}</div>
    <div class="post_bottom">{!! $bottom !!}</div>
</div>