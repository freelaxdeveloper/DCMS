<div class="listing" id="<?= $id ?>" ng-controller="ListingCtrl" ng-init="listing.id_form = '<?= $form ? $form->id : '' ?>'; listing.url = '<?= $ajax_url ?>'">
    <?= $content ?>
</div>