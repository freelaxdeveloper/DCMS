<div class="form">
    <select class="gradient_grey border padding radius" onchange="location = this.options[this.selectedIndex].value;">
        <?php
        foreach ($order AS $option) {
            echo '<option value="' . $option[0] . '"' . (!empty($option[2]) ? ' selected="selected"' : '') . '>' . $option[1] . '</option>';
        }
        ?>
    </select>
</div>