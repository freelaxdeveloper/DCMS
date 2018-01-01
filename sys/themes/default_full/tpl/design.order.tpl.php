<div class="form">
    <select onchange="location = this.options[this.selectedIndex].value;">
        <?
        foreach ($order AS $option) {
            echo '<option value="' . $option[0] . '"' . (!empty($option[2]) ? ' selected="selected"' : '') . '>' . $option[1] . '</option>';
        }
        ?>
    </select>
</div>