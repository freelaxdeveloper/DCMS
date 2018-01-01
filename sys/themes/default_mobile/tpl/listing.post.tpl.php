<?php
$post_time = $time ? '<span class="time">' . $time . '</span>' : '';
$post_counter = $counter ? '<span class="counter">' . $counter . '</span>' : '';
$post_actions = '<span class="actions">' . $this->section($actions, '<a href="{url}"><img src="{icon}" alt="" /></a>') . '</span>';
?>
<?= ($url ? '<a href="' . $url . '" class="' : '<div class="') . 'post' . ($highlight ? ' highlight' : '') . '" id="' . $id . '">' ?>
    <table cellspacing="0" cellpadding="0" width="100%">
        <?php if ($image) { ?>
            <tr>
                <td class="image" rowspan="4">
                    <img src="<?= $image ?>" alt=""/>
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
        <?php } elseif ($icon) { ?>
            <tr>
                <td class="icon">
                    <?php if ($icon_class) { ?>
                        <span class="<?= $icon_class ?>"></span>
                    <?php } else { ?>
                        <img src="<?= $icon ?>" alt=""/>
                    <?php } ?>
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
        <?php } else { ?>
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
        <?php } ?>

        <?php if ($content) { ?>
            <tr>
                <td class="content" colspan="10">
                    <?= $content ?>
                </td>
            </tr>
        <?php } ?>

        <?php if ($bottom) { ?>
            <tr>
                <td class="bottom" colspan="10">
                    <?= $bottom ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?=
$url ? '</a>' : '</div>'?>