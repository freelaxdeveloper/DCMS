@include('listing.posts', [
    'title' => $category->name,
    'icon' => 'forum.category',
    'content' => $category->description,
    'url' => "./category.php?id={$category->id}",
])