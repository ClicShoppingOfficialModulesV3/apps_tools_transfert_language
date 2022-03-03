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
  $language_id = HTML::sanitize($_POST['site_language']);
// check if the archive directory exists
  $dir_ok = false;

  unset($file);

  $transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';

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

      echo HTML::form('upgrade_select', $CLICSHOPPING_TransfertLanguage->link('TransfertLanguage&UpgradeFromLanguagePack&file=' . $buInfo->file));
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
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_import'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_TransfertLanguage->getDef('button_cancel'), null, $CLICSHOPPING_TransfertLanguage->link('TransfertLanguages'), 'warning');
?>
          </span>
              </div>
            </div>
          </div>
        </div>
        <div class="separator"></div>
        <?php
          $target_language = [];

          foreach ($languages as $key) {
            if ($key['id'] === $_POST['site_language']) {
              $target_language = $key;
              break;
            }
          }

          echo HTML::hiddenField('groups', $_POST['groups']);
          echo $CLICSHOPPING_TransfertLanguage->getDef('text_info_target_language', ['target_language' => $target_language['name']]) . '<br />';
          echo $CLICSHOPPING_TransfertLanguage->getDef('text_info_source_language', ['source_group' => $_POST['groups'], 'file_name' => $file]) . '<br />';

          $content_text = '';

          $transport_file = fopen(CLICSHOPPING::BASE_DIR . 'Work/Temp/language.tmp', "w+");

          $Zip = new \ZipArchive();

          $result = $Zip->open($transfert_directory . $file);

 //         var_dump($result);
 //var_dump('-------------');

          if ($result === true) {
            $info = explode('/', $_POST['groups']);
            var_dump($info);


            if ($info[2] != 'Apps') {
              $group = $info[2] . '/';
            } else {
              $group = $info[2] . '/' . $info[3] . '/' . $info[4] . '/';
            }

            var_dump($group);
            $replace = str_replace('/', '-', $group);
            /*
                    $Qgdefinitions = $CLICSHOPPING_TransfertLanguage->db->prepare("select distinct content_group
                                                                                   from :table_languages_definitions
                                                                                   where languages_id = :languages_id
                                                                                   and content_group like '" .$replace . "%'
                                                                                 ");

                    $Qgdefinitions->bindInt(':languages_id', $language_id);
                    $Qgdefinitions->execute();
            */
            $Qgdefinitions = $CLICSHOPPING_TransfertLanguage->db->prepare("select distinct content_group
                                                                           from :table_languages_definitions
                                                                           where languages_id = :languages_id
                                                                           and content_group like 'Shop%'
                                                                         ");

            $Qgdefinitions->bindInt(':languages_id', $language_id);
            $Qgdefinitions->execute();

            while ($Qgdefinitions->fetch()) {
//          $pathname = $info[0] . '/' . $info[1] . '/' . str_replace('-', '/', $QgDefinitions->value('content_group')) . '.json';
              $pathname = $info[1] . '/' . str_replace('-', '/', $Qgdefinitions->value('content_group')) . '.json';

              $zip_file_definition_key_array = json_decode($Zip->getFromName($pathname), true);
//var_dump($zip_file_definition_key_array);
              $Qdefinitions = $CLICSHOPPING_TransfertLanguage->db->prepare('select *
                                                                      from :table_languages_definitions
                                                                      where languages_id = :languages_id
                                                                      and content_group = :content_group
                                                                     ');
              $Qdefinitions->bindInt(':languages_id', $language_id);
              $Qdefinitions->bindValue(':content_group', $Qgdefinitions->value('content_group'));
              $Qdefinitions->execute();

              $table_language_definitions_array = [];

              while ($Qdefinitions->fetch()) {
                $table_language_definitions_array[$Qdefinitions->value('definition_key')] = $Qdefinitions->value('definition_value');
              }

              if (\is_array($zip_file_definition_key_array)) {
                $diff_array_user = array_diff_assoc($table_language_definitions_array, $zip_file_definition_key_array);
                $diff_array_source = array_diff_assoc($zip_file_definition_key_array, $table_language_definitions_array);

                if (!empty($diff_array_user)) {
                  $content_text .= '<h3>' . $QgDefinitions->value('content_group') . '</h3>';

                  $content_text .= '<table class="table table-striped"><thead><tr><th width="30%;">' . $CLICSHOPPING_TransfertLanguage->getDef('table_heading_target', ['target_language' => $target_language['name']]) . '</th><th width="30%;">' . $CLICSHOPPING_TransfertLanguage->getDef('table_heading_source', ['source_group' => $_POST['groups']]) . '</th><th></th></tr></thead><tbody>';

                  foreach ($diff_array_user as $key => $value) {
                    $content_text .= '<tr><td><strong>' . $key . '</strong><br />' . htmlentities($value) . '</td>';

                    if (isset($diff_array_source[$key])) {
                      $content_text .= '<td><br />' . HTML::outputProtected($diff_array_source[$key]) . '</td><td>' . $CLICSHOPPING_TransfertLanguage->getDef('text_info_modify_definition') . '<br /><button type="button" class="btn btn-warning btn-xs">' . $CLICSHOPPING_TransfertLanguage->getDef('info_button_apply_source') . '</button>';// update
                      $sql_array = ['save' => ['table' => ':table_languages_definitions', 'data' => ['content_group' => $QgDefinitions->value('content_group'), 'definition_key' => $key, 'definition_value' => $diff_array_source[$key], 'languages_id' => $target_language['id']], 'where' => ['content_group' => $QgDefinitions->value('content_group'), 'definition_key' => $key, 'languages_id' => $target_language['id']]]];
                      fwrite($transport_file, json_encode($sql_array, JSON_UNESCAPED_SLASHES) . "\n");

                    } else {
                      // delete
                      $content_text .= '<td></td><td>' . $CLICSHOPPING_TransfertLanguage->getDef('text_info_user_added_only') . '<br /><button type="button" class="btn btn-danger btn-xs">' . $CLICSHOPPING_TransfertLanguage->getDef('info_button_clean_target') . '</button>';
                      $sql_array = ['delete' => ['table' => ':table_languages_definitions', 'where' => ['content_group' => $QgDefinitions->value('content_group'), 'definition_key' => $key, 'languages_id' => $target_language['id']]]];
                      fwrite($transport_file, json_encode($sql_array, JSON_UNESCAPED_SLASHES) . "\n");
                    }
                    $content_text .= '</td></tr>';
                  }

                  foreach ($diff_array_source as $key => $value) {
                    if (!isset($diff_array_user[$key])) {
// save as new
                      $content_text .= '<tr><td></td><td><strong>' . $key . '</strong><br />' . htmlentities($value, ENT_QUOTES | ENT_HTML5);
                      $content_text .= '</td><td>' . $CLICSHOPPING_TransfertLanguage->getDef('text_info_add_new_language_definition') . '<br /><button type="button" class="btn btn-success btn-xs">' . $CLICSHOPPING_TransfertLanguage->getDef('info_button_add_new') . '</button>';
                      $sql_array = ['save' => ['table' => ':table_languages_definitions', 'data' => ['content_group' => $QgDefinitions->value('content_group'), 'definition_key' => $key, 'definition_value' => addslashes($diff_array_source[$key]), 'languages_id' => $target_language['id']]]];
                      fwrite($transport_file, json_encode($sql_array, JSON_UNESCAPED_SLASHES) . "\n");

                      $content_text .= '</td></tr>';
                    }
                  }
                  $content_text .= '</tbody></table>';
                }
              }
            }

            $Zip->close();

          } else {
            error_log('ClicShopping\OM\Zip::open() ' . $transfert_directory . $file . ' file error: ' . $Zip->message($result));
          }

          fclose($transport_file);

          if (!empty($content_text)) {
            echo $content_text;
          }
        ?>
        </form>
      </div>
      <?php
    }
  }
?>