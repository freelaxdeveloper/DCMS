<div class="pages">
    {!! App\pages::pages_helper(1, $page, $link) !!}
    @foreach ($show_pages as $p)
        {!! App\pages::pages_helper($p, $page, $link) !!}
    @endforeach
    {!! App\pages::pages_helper($k_page, $page, $link) !!}
</div>