<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_TransfertLanguage = Registry::get('TransfertLanguage');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  echo HTML::form('backup', $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage&ZipNow'));
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/define_language.gif', $CLICSHOPPING_TransfertLanguage->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-3 pageHeading"><?php echo $CLICSHOPPING_TransfertLanguage->getDef('heading_title'); ?></span>
          <span class="col-md-8 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_backup'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_cancel'), null, $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage'), 'warning');
?>

          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_TransfertLanguage->getDef('text_info_new_backup'); ?></strong></div>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div class="col-md-12"><?php echo $CLICSHOPPING_TransfertLanguage->getDef('text_info_upload_local'); ?></div>
      <div class="separator"></div>
      <div class="col-md-12">
        <?php echo HTML::radioField('download', 'yes', true) . ' ' . $CLICSHOPPING_TransfertLanguage->getDef('text_info_download_only') . '*<br /><br />*' . $CLICSHOPPING_TransfertLanguage->getDef('text_info_best_through_https'); ?>
      </div>
    </div>
  </div>
  </form>
</div>
