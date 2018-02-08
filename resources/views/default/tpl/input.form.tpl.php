<div class="form">
    <?=
    '<form id="' . $id . '" ng-controller="FormCtrl" ng-submit="form.onSubmit($event)" data-url="' . $ajax_url . '" ng-disabled="form.sending"' .
    ($method ? ' method="' . $method . '"' : '') .
    ($action ? ' action="' . $action . '"' : '') .
    ($files ? ' enctype="multipart/form-data"' : '') .
    ($limit_files ? ' data-limit-files="' . $limit_files . '"' : '') .
    ($limit_size ? ' data-limit-size="' . $limit_size . '"' : '')
    . '>'
    ?>

    <?php
    use App\text;
    
    foreach ($el AS $element) {
        if ($element['title'])
            echo '<div class="form_title">' . $element['title'] . ':</div>';
        switch ($element['type']) {
            case 'text':
                echo '<div class="form_text">' . $element['value'] . '</div>';
                break;
            case 'captcha':
                ?>
                <input type="hidden" name="captcha_session" value="<?= $element['session'] ?>"/>
                <img id="captcha" src="<?= route('captcha') ?>?captcha_session=<?= $element['session'] ?>"
                     alt="captcha"/><br/>
                <?= $lang->getString("Введите число с картинки") ?>:<br/>
                <input type="text" autocomplete="off" name="captcha" size="5" maxlength="5"/>
                <?php
                break;
            case 'input_text':
                echo '<input type="text"' .
                    ($element['info']['name'] ? ' name="' . $element['info']['name'] . '"' : '') .
                    ($element['info']['value'] ? ' value="' . text::toValue($element['info']['value']) . '"' : '') .
                    ($element['info']['maxlength'] ? ' maxlength="' . intval($element['info']['maxlength']) . '"' : '') .
                    ($element['info']['size'] ? ' size="' . intval($element['info']['size']) . '"' : '') .
                    ($element['info']['disabled'] ? ' disabled="disabled"' : '') .
                    ' />';
                break;
            case 'hidden':
                echo '<input type="hidden"' .
                    ($element['info']['name'] ? ' name="' . $element['info']['name'] . '"' : '') .
                    ($element['info']['value'] ? ' value="' . text::toValue($element['info']['value']) . '"' : '') .
                    ' />';
                break;
            case 'password':
                echo '<input type="password"' .
                    ($element['info']['name'] ? ' name="' . $element['info']['name'] . '"' : '') .
                    ($element['info']['value'] ? ' value="' . text::toValue($element['info']['value']) . '"' : '') .
                    ($element['info']['maxlength'] ? ' maxlength="' . intval($element['info']['maxlength']) . '"' : '') .
                    ($element['info']['size'] ? ' size="' . intval($element['info']['size']) . '"' : '') .
                    ($element['info']['disabled'] ? ' disabled="disabled"' : '') .
                    ' />';
                break;
            case 'textarea':
                echo '<textarea id="' . passgen() . '" data-bbcode="" data-ctrl-enter-submit="' . ($element['submit_ctrl_enter'] ? 'true' : 'false') . '" ng-keyDown="form.keyDown($event)" ng-scope="true" ng-initial="" class="animate msd-elastic: \n;" ng-model="form.values.' . $element['info']['name'] . '" ' .
                    ($element['info']['name'] ? ' name="' . $element['info']['name'] . '"' : '') .
                    ($element['info']['disabled'] ? ' disabled="disabled"' : '') .
                    '>' .
                    ($element['info']['value'] ? text::toValue($element['info']['value']) : '') .
                    '</textarea>';
                break;
            case 'checkbox':
                echo '<label><input type="checkbox"' .
                    ($element['info']['name'] ? ' name="' . $element['info']['name'] . '"' : '') .
                    ($element['info']['value'] ? ' value="' . text::toValue($element['info']['value']) . '"' : '') .
                    ($element['info']['checked'] ? ' checked="checked"' : '') .
                    ' />' .
                    ($element['info']['text'] ? ' ' . $element['info']['text'] : '') .
                    '</label>';
                break;
            case 'submit':
                echo '<input class="" type="submit"' .
                    ($element['info']['name'] ? ' name="' . $element['info']['name'] . '"' : '') .
                    ($element['info']['value'] ? ' value="' . text::toValue($element['info']['value']) . '"' : '') .
                    ' />';
                break;
            case 'file':
                echo '<input type="file"' .
                    ($element['info']['name'] ? ' name="' . $element['info']['name'] . '"' : '') .
                    ($element['info']['multiple'] ? ' multiple="multiple"' : '') .
                    ' />';
                break;
            case 'select':
                echo '<select name="' . $element['info']['name'] . '">';
                foreach ($element['info']['options'] AS $option) {
                    if ($option['groupstart'])
                        echo '<optgroup label="' . $option[0] . '">';
                    elseif ($option['groupend'])
                        echo '</optgroup>';
                    else
                        echo '<option' .
                            ($option[2] ? ' selected="selected"' : '') .
                            ' value="' . $option[0] . '"' .
                            '>' .
                            $option[1] .
                            '</option>';
                }
                echo '</select>';
                break;
        }

        if ($element['br'])
            echo '<br />';
    }
    ?>
    <span class="msg animate" ng-show="form.msg" ng-bind="form.msg"></span>
    <span class="err animate" ng-show="form.err" ng-bind="form.err"></span>

    <div class="waiter animate ng-hide" ng-show="form.sending"></div>
    <?php if ($refresh_url && !$ajax_url) { ?>
        <a class="refresh" title="<?= __('Обновить') ?>" href="<?= $refresh_url ?>"><img
                src="<?= $path ?>/img/refresh.png" alt=""/></a>
    <?php } ?>
    <?php
    echo '</form>';
    ?>
</div>