<div class="listing" ng-controller="ListingCtrl">
    @foreach ($messages as $message)
    <div id="message_{{$message->id}}"
        class="post clearfix icon time actions content"
        data-ng-controller="ListingPostCtrl"
        data-post-url="./actions.php?id={{$message->id}}">

        <div class="post_head">
            <span class="post_icon">
                <img src="/sys/images/icons/{{$message->user->icon}}.png" alt="">
            </span>
            <a class="post_title" href="./actions.php?id={{$message->id}}">{{$message->user->login}}</a>
            <span class="post_time">{{$message->time}}</span>
        </div>
        <div class="post_content">@toOutput($message->message)</div>
    </div>
    @endforeach
</div>