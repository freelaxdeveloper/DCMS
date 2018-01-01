<div class="vote">
    <span class="vote_name"><?=$name?></span>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <? for ($i =0; $i< count($votes); $i++){?>
        <tr>
            <td colspan="2">
                <?=$votes[$i]['name']?>
                <?=$votes[$i]['count']?' ('.$votes[$i]['count'].')':'' ?>
            </td>
        </tr>
        <tr>
            <td class="votes gradient_grey invert">
                <div>
                    <div class="votes gradient_blue" style=" width:<?=$votes[$i]['pc']?>%; box-sizing: border-box;">
                        <?=$votes[$i]['pc']?>%
                    </div>

                </div>
            </td>
            <?if ($is_add){?>
            <td class="votes_add">
                <div>
                    <a class="gradient_blue" href="<?=$votes[$i]['url']?>">+</a>
                </div>
            </td>
            <?}?>
        </tr>        
        <?}?>
    </table>
</div>