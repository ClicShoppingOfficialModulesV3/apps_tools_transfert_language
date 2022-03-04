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

  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  $CLICSHOPPING_TransfertLanguage = Registry::get('TransfertLanguage');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

// check if the archive directory exists
  $dir_ok = false;

  unset($file);

  $transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';

  if (is_dir($transfert_directory)) {
    if (FileSystem::isWritable($transfert_directory)) {
      $dir_ok = true;
    } else {
      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_transfert_directory_not_writeable'), 'error');
    }
  } else {
    if (!is_dir($transfert_directory)) {
      if (!mkdir($transfert_directory, 0777, true) && !is_dir($transfert_directory)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $transfert_directory));
      }

      $dir_ok = true;
      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_transfert_directory_created'), 'success');
    }
  }

  if (isset($_GET['file'])) {
    $file = basename($_GET['file']);

    if (is_file($transfert_directory . $file)) {
      $info = [
        'file' => $file,
        'date' => date(CLICSHOPPING::getDef('php_date_time_format'), filemtime($transfert_directory . $file)),
        'size' => number_format(filesize($transfert_directory . $file)) . ' bytes'
      ];

      if (substr($file, -3) == 'zip') {
        $info['compression'] = 'ZIP';
      } else {
        $info['compression'] = CLICSHOPPING::getDef('text_no_extension');
      }

      $buInfo = new ObjectInfo($info);

      echo HTML::form('delete', $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage&DeleteConfirm&file=' . $buInfo->file));
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
                <span class="col-md-8 text-end">
<?php
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_delete'), null, null, 'danger') . ' ';
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_cancel'), null, $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage'), 'warning');
?>
          </span>
              </div>
            </div>
          </div>
        </div>
        <div class="separator"></div>

        <div class="col-md-12 mainTitle">
          <strong><?php echo $CLICSHOPPING_TransfertLanguage->getDef('text_delete_intro'); ?></strong></div>
        <div class="adminformTitle">
          <div class="row">
            <div class="separator"></div>
            <div class="col-md-12"><?php echo '<strong>' . $buInfo->file . '</strong>'; ?></div>
            <div class="separator"></div>
            <div class="separator"></div>
          </div>
        </div>
        </form>
        <div><?php echo '<strong>' . $CLICSHOPPING_TransfertLanguage->getDef('text_transfert_directory') . '</strong> ' . $transfert_directory; ?></div>
      </div>
      <?php
    }
  }
?>