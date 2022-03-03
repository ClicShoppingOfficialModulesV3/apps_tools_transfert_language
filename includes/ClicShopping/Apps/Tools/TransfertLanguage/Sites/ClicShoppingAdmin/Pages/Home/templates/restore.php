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
  $CLICSHOPPING_Language = Registry::get('Language');

  $languages = $CLICSHOPPING_Language->getLanguages();

// check if the archive directory exists
  $dir_ok = false;

  unset($file);

  $transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
  $current_version = CLICSHOPPING::getVersion();

  if (is_dir($transfert_directory)) {
    if (FileSystem::isWritable($transfert_directory)) {
      $dir_ok = true;
    } else {
      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TransfertLanguage->getDef('error_transfert_directory_not_writeable'), 'error');
    }
  } else {
    if (!is_dir($transfert_directory)) {
      if (!mkdir($transfert_directory, 0777, true) && !is_dir($transfert_directory)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $transfert_directory));
      }

      $dir_ok = true;
      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TransfertLanguage->getDef('success_transfert_directory_created'), 'success');
    }
  }

  if (isset($_GET['file'])) {
    $file = basename($_GET['file']);

    if (is_file($transfert_directory . $file)) {
      $info = [
        'file' => $file,
        'date' => date($CLICSHOPPING_TransfertLanguage->getDef('php_date_time_format'), filemtime($transfert_directory . $file)),
        'size' => number_format(filesize($transfert_directory . $file)) . ' bytes'
      ];

      if (substr($file, -3) == 'zip') {
        $info['compression'] = 'ZIP';
      } else {
        $info['compression'] = $CLICSHOPPING_TransfertLanguage->getDef('text_no_extension');
      }

      $buInfo = new ObjectInfo($info);

      echo HTML::form('upgrade_select', $CLICSHOPPING_TransfertLanguage->link('UpgradeSelect&file=' . $buInfo->file));
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
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_import_preview'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_cancel'), null, $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage'), 'warning');
?>
          </span>
              </div>
            </div>
          </div>
        </div>
        <div class="separator"></div>
        <?php
          echo '<div class="dataTableHeadingRow">' . $CLICSHOPPING_TransfertLanguage->getDef('text_info_import') . '</div>';
          echo '<div class="adminformTitle">';

          echo $CLICSHOPPING_TransfertLanguage->getDef('text_site_current_version') . '<strong>' . $current_version . '</strong><br /><br />';

          $Zip = new \ZipArchive();

          $language_pack = [];

          $result = $Zip->open($transfert_directory . $file);

          if ($result === true) {
            $language_pack['Sites'] = [];
            $language_pack['Apps'] = [];

            for ($i = 0; $i < $Zip->numFiles; $i++) {
              $info = explode('/', dirname($Zip->getNameIndex($i)));

              if (isset($info[2]) && $info[2] != 'Apps' && !\in_array(($info[0] . '/' . $info[1] . '/' . $info[2]), $language_pack['Sites'])) {
                $language_pack['Sites'][] = $info[0] . '/' . $info[1] . '/' . $info[2];
              }
              if ((isset($info[2]) && $info[2] == 'Apps') && isset($info[4]) && !\in_array(($info[0] . '/' . $info[1] . '/' . $info[2] . '/' . $info[3] . '/' . $info[4]), $language_pack['Apps'])) {
                $language_pack['Apps'][] = $info[0] . '/' . $info[1] . '/' . $info[2] . '/' . $info[3] . '/' . $info[4];
              }
            }

            $Zip->close();

          } else {
            error_log('ClicShopping\OM\Zip::open() ' . $transfert_directory . $file . ' file error: ' . $Zip->message($result));
            $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TransfertLanguage->getDef('error_reading_zip_file'), 'warning');
          }

          if (!empty($language_pack)) {
            echo $CLICSHOPPING_TransfertLanguage->getDef('select_installed_language_to_upgrade') . '<br />';

            foreach ($languages as $key) {
              echo HTML::radioField('site_language', $key['id']) . '&nbsp;' . $key['name'] . '<br />';
            }

            echo '<br />' . $CLICSHOPPING_TransfertLanguage->getDef('select_language_site_from_zip_pack_to_upgrade') . '<br />';

            foreach ($language_pack['Sites'] as $value) {
              echo HTML::radioField('groups', $value) . '&nbsp;' . $value . '<br />';
            }

            echo '<br />' . $CLICSHOPPING_TransfertLanguage->getDef('select_language_apps_from_zip_pack_to_upgrade') . '<br />';

            foreach ($language_pack['Apps'] as $value) {
              echo HTML::radioField('groups', $value) . '&nbsp;' . $value . '<br />';
            }

          } else {
            $CLICSHOPPING_MessageStack->add($CLICSHOPPING_TransfertLanguage->getDef('error_incompatible_language_file'), 'warning');
          }
        ?>
        </form>
      </div>
      <?php
    }
  }
?>