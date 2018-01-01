/**
 * Вставка текста по выделению
 * @param {HTMLInputElement} node
 * @param {String} Open Текст, вставляемый перед выделением
 * @param {String} Close Текст, вставляемый после выделения
 * @param {boolean = false} CursorEnd Флаг, указывающий на необходимость установки курсора после вставленного текста
 * @returns {boolean}
 */
function InputInsert(node, Open, Close, CursorEnd) {
    node.focus();
    if (window.attachEvent && navigator.userAgent.indexOf('Opera') === -1) { // IE
        var s = node.sel;
        if (s) {
            var l = s.text.length;
            s.text = Open + s.text + Close;
            s.moveEnd("character", -Close.length);
            s.moveStart("character", -l);
            s.select();
        }
    } else {
        var ss = node.scrollTop;
        var sel1 = node.value.substr(0, node.selectionStart);
        var sel2 = node.value.substr(node.selectionEnd);
        var sel = node.value.substr(node.selectionStart, node.selectionEnd - node.selectionStart);

        node.value = sel1 + Open + sel + Close + sel2;
        if (CursorEnd) {
            node.selectionStart = sel1.length + Open.length + sel.length + Close.length;
            node.selectionEnd = node.selectionStart;
        } else {
            node.selectionStart = sel1.length + Open.length;
            node.selectionEnd = node.selectionStart + sel.length;
        }
        node.scrollTop = ss;
    }
    return false;
}


$(function () {
    $(document).on('click', '.DCMS_thumb_down', function (event) {
        if (!confirm(translate.rating_down_message)){
            event.preventDefault();
            event.stopPropagation();
        }
    });
});

$(function () {
    var $user = $("#user"),
        $reg = $("#reg"),
        $login = $("#login"),
        $my_mail = $("#my_mail"),
        $my_friends = $("#my_friends"),
        $menu_user = $("#menu_user"),
        $icon_menu = $("#icon_menu");

    var ajax_timeout = 7000;
    var scope = {};

    $("#icon_menu, #container_overflow").on('click', function () {
        $("#container_overflow, #container_menu, #container_content").toggleClass('menu');
    });

    $('.form .textarea .smiles_button').on('click', function () {
        var $p = $(this).parent();

        if ($p.hasClass('smiles')) {
            $p.removeClass('smiles');
        } else {
            $p.addClass('smiles');

            if ($p.data('smilesLoaded'))
                return;

            var $smiles = $p.find('.smiles');
            $().dcmsApi.request('api_smiles', 'get', null, function (data) {
                for (var i = 0; i < data.length; i++) {
                    var $smile = $('<img class="smile" src="' + data[i].image + '" />');
                    $smile.data(data[i]);
                    $smile.appendTo($smiles);
                }
                $smiles.on('click', '.smile', function () {
                    var data = $(this).data();
                    $p.removeClass('smiles');
                    InputInsert($p.find('textarea')[0], '', ' ' + data.code, true);
                });
                $p.data('smilesLoaded', true);
            });
        }
    });

    (window.addClickEvent = function ($els) {
        $els.on('touchstart touchend touchleave touchmove mouseleave', function (event) {
            var $tg = $(event.currentTarget);
            switch (event.type) {
                case 'touchstart':
                    if ($tg.data('pressed'))
                        return;
                    $tg.data('pressed', true);

                    $tg.toggleClass('invert');
                    break;
                case 'touchend':
                case 'touchleave':
                case 'touchmove':
                case 'mouseleave':
                    if (!$tg.data('pressed'))
                        return;
                    $tg.data('pressed', false);

                    $tg.toggleClass('invert');
                    break;
            }
            event.stopPropagation();
        });
    })($('a'));


    $('form').each(function () {
        var $element = $(this);
        var url = $element.attr('data-ajax-url');
        if (!url)
            return;

        $element.on('submit', function (event) {
            event.preventDefault();

            var formNode = event.target;
            var postData = {};
            for (var i = 0; i < formNode.elements.length; i++) {
                postData[formNode.elements[i].name ] = formNode.elements[i].value;
            }

            $element.attr('disabled', 'disabled');

            $.post(url, postData)
                .success(function (data) {
                    //form.sending = false;
                    //if ($data.msg)
                    //    form.showMessage($data.msg);

                    //if ($data.err)
                    //    form.showError($data.err);

                    for (var i = 0; i < formNode.elements.length; i++) {
                        var name = formNode.elements[i].name;
                        if (typeof data.form[name] == "undefined")
                            continue;
                        formNode.elements[i].value = data.form[name];
                    }
                    $element.attr('disabled', false);
                    $(scope).trigger('form_submit', $element.attr('id')); // Уведомляем о том, что форма была отправлена. Это событие должен слушать листинг
                })
                .error(function () {
                    $element.attr('disabled', false);
                });
        });
    });

    $(".listing").each(function () {
        var $element = $(this);
        var id_form = $element.attr('data-form-id');
        var url = $element.attr('data-ajax-url');
        if (!url)
            return;
        var timeout;

        $(scope).on('form_submit', function (event, id_form_arg) {
            if (id_form_arg == id_form)
                refresh(true);
        });

        var refresh = function (forcibly) {
            clearTimeout(timeout);

            var skip_ids = [];
            $element.children().each(function () {
                skip_ids.push(this.id);
            });

            $.post(url, {skip_ids: skip_ids.join(',')})
                .success(function (data) {

                    if (data.remove && data.remove.length)
                        for (var i = 0; i < data.remove.length; i++) {
                            $('#' + data.remove[i]).remove();
                        }

                    if (data.add && data.add.length) {
                        for (var i = 0; i < data.add.length; i++) {
                            var after_id = data.add[i].after_id;
                            var $el = $(data.add[i].html).css('opacity', '0');

                            if (after_id)
                                $element.children('#' + after_id).after($el);
                            else
                                $el.prependTo($element);
                            addClickEvent($el);
                            $el.animate({opacity: 1}, 500);
                        }

                        if (!forcibly)
                            $(scope).trigger('newMessage');
                    }

                    timeout = setTimeout(refresh, ajax_timeout);
                })
                .error(function () {
                    timeout = setTimeout(refresh, 60000);
                });
        };

        timeout = setTimeout(refresh, ajax_timeout);
    });

    $(scope).on('newMessage', function () {
        if (window.navigator.vibrate)
            window.navigator.vibrate([100, 100]);
        var audio = document.querySelector("#audio_notify");
        audio.pause();
        audio.loop = false;
        audio.currentTime = 0;
        audio.play();
    });

    $(scope).on('userRefreshed', function (event, data) {
        if (data) {

            if (user.mail_new_count < data.mail_new_count)
                $(scope).trigger('newMessage');

            user = data;
        }

        $icon_menu.toggleClass('mail', !!+user.mail_new_count);
        $icon_menu.toggleClass('friends', !!+user.friend_new_count);

        if (user.id) {
            $user.text(user.login).show();
            $my_mail.text(translate.mail + (+user.mail_new_count ? ' +' + user.mail_new_count : '')).show();
            $my_mail.attr('href', +user.mail_new_count ? '/my.mail.php?only_unreaded' : '/my.mail.php')
            $my_friends.text(translate.friends + (+user.friend_new_count ? ' +' + user.friend_new_count : '')).show();
            $menu_user.text(translate.user_menu).show();
            $reg.hide();
            $login.hide();

            setTimeout(function () {
                $(scope).trigger('userRefresh');
            }, ajax_timeout);

        } else {
            $user.hide();
            $my_mail.hide();
            $my_friends.hide();
            $menu_user.hide();
            $reg.text(translate.reg).show();
            $login.text(translate.auth).show();
        }
    });

    $(scope).on('userRefresh', function () {
        $().dcmsApi.request('api_user', 'get', Object.keys(user),
            function (data) {
                if (!data)
                    return;
                ajax_timeout = 7000;
                $(scope).trigger('userRefreshed', data);
            }, function () {
                ajax_timeout = 60000;
                $(scope).trigger('userRefreshed');
            }
        );
    });

    $(scope).trigger('userRefreshed');
});