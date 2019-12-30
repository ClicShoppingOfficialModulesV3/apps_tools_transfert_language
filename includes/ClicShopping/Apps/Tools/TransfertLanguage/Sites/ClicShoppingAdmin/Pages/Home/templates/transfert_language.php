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

  $CLICSHOPPING_TransfertLanguage = Registry::get('TransfertLanguage');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  unset($file);

  $transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
  $current_version = CLICSHOPPING::getVersion();

  // check if the archive directory exists
  $dir_ok = false;

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
//  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('text_crowdin'), null, $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage&DownloadFromCrowdin'), 'primary') . ' ';
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('icon_file_archive'), null, $CLICSHOPPING_TransfertLanguage->link('Backup'), 'info') . ' ';
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('text_upload'), null, $CLICSHOPPING_TransfertLanguage->link('UploadLocal'), 'success') . ' ';
?>
            </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <?php
    if (!FileSystem::isWritable($transfert_directory)) {
      ?>
      <div class="alert alert-danger" role="alert">
        <p><?php echo $CLICSHOPPING_Upgrade->getDef('error_directory_not_writable'); ?></p>
      </div>
      <?php
    }
  ?>
  <table class="table table-hover">
    <thead>
    <tr class="dataTableHeadingRow">
      <th><?php echo $CLICSHOPPING_TransfertLanguage->getDef('table_heading_title'); ?></th>
      <th class="text-md-right"><?php echo $CLICSHOPPING_TransfertLanguage->getDef('table_heading_file_date'); ?></th>
      <th class="text-md-right"><?php echo $CLICSHOPPING_TransfertLanguage->getDef('table_heading_file_size'); ?></th>
      <th class="text-md-right"><?php echo $CLICSHOPPING_TransfertLanguage->getDef('table_heading_action'); ?></th>
    </tr>
    </thead>
    <tbody>

    <?php
      if ($dir_ok === true) {
        $dir = dir($transfert_directory);
        $contents = [];

        while ($file = $dir->read()) {
          if (!is_dir($transfert_directory . $file) && in_array(substr($file, -3), array('zip'))) {
            $contents[] = $file;
          }
        }
        sort($contents);

        for ($i = 0, $n = count($contents); $i < $n; $i++) {
          $entry = $contents[$i];
          ?>
          <tr>
            <td><?php echo $entry; ?></td>
            <td
              class="text-md-right"><?php echo date($CLICSHOPPING_TransfertLanguage->getDef('php_date_time_format'), filemtime($transfert_directory . $entry)); ?></td>
            <td class="text-md-right"><?php echo number_format(filesize($transfert_directory . $entry)); ?> bytes</td>
            <td class="text-md-right">
              <?php
                echo '<a href="' . $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage&Download&file=' . $entry) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/file_download.gif', $CLICSHOPPING_TransfertLanguage->getDef('icon_file_download')) . '</a>';
                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_TransfertLanguage->link('Restore&file=' . $entry) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/restore.gif', $CLICSHOPPING_TransfertLanguage->getDef('icon_restore')) . '</a>';
                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_TransfertLanguage->link('Delete&file=' . $entry) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_TransfertLanguage->getDef('icon_delete')) . '</a>';
                echo '&nbsp;';
              ?>
            </td>
          </tr>
          <?php
        }

        $dir->close();
      }
    ?>
    </tbody>
  </table>
  <div><?php echo '<strong>' . $CLICSHOPPING_TransfertLanguage->getDef('text_transfert_directory') . '</strong> ' . $transfert_directory; ?></div>
</div>