<?
if ($url)
    $title = '<a href="' . $url . '">' . $title . '</a>';

$post_time = $time ? '<span class="time">' . $time . '</span>' : '';
$post_counter = $counter ? '<span class="counter">' . $counter . '</span>' : '';
$post_actions = '<span class="actions">' . $this->section($actions, '<a href="{url}"><img src="{icon}" alt="" /></a>') . '</span>';
?>
<div class="post<?= ($highlight ? ' highlight' : '') ?>">
    <table cellspacing="0" cellpadding="0" width="100%">
        <? if ($image) { ?>            
            <tr>
                <td class="image" rowspan="4">
                    <img src="<?= $image ?>" alt="" />
                </td>
                <td class="title">
                    <?= $title ?>
                    <?= $post_counter ?>
                </td>
                <td class="right">
                    <?= $post_time ?>
                    <?= $post_actions ?>
                </td>
            </tr>
        <? } elseif ($icon) { ?>            
            <tr>
                <td class="icon">
                    <img src="<?= $icon ?>" alt="" />
                </td>
                <td class="title">
                    <?= $title ?>
                    <?= $post_counter ?>
                </td>

                <td class="right">
                    <?= $post_time ?>
                    <?= $post_actions ?>
                </td>
            </tr>
        <? } else { ?>
            <tr>
                <td class="title">
                    <?= $title ?>
                    <?= $post_counter ?>
                </td>

                <td class="right">
                    <?= $post_time ?>
                    <?= $post_actions ?>
                </td>
            </tr>
        <? } ?>

        <? if ($content) { ?>
            <tr>
                <td class="content" colspan="10">
                    <?= $content ?>
                </td>
            </tr>
        <? } ?>

        <? if ($bottom) { ?>
            <tr>
                <td class="bottom" colspan="10">
                    <?= $bottom ?>
                </td>
            </tr>
        <? } ?>
    </table>
</div>