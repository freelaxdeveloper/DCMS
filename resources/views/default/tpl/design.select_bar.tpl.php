<div class="select_bar gradient_grey invert border radius">
    <?php
    foreach($select AS $option){
        if (empty($option[2]))
            echo '<a class="gradient_grey border radius padding" href="'.$option[0].'">'.$option[1].'</a>';
        else
            echo '<span class="gradient_grey border radius padding invert">'.$option[1].'</span>';
    }
    ?>
</div>
