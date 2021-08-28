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


  namespace ClicShopping\Apps\Tools\TransfertLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions\TransfertLanguage;

  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\Apps;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class ZipNow extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;
    protected mixed $lang;
    protected string $transfert_directory;
    protected string $current_version;

    public function __construct()
    {
      $this->app = Registry::get('TransfertLanguage');
      $this->transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
      $this->current_version = CLICSHOPPING::getVersion();
      $this->lang = Registry::get('Language');
    }

    public function getChekDirectory()
    {
      if (!is_dir($this->transfert_directory)) {
        if (!mkdir($concurrentDirectory = $this->transfert_directory, 0777, true) && !is_dir($concurrentDirectory)) {
          throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return true;
      } elseif (FileSystem::isWritable($this->transfert_directory)) {
        return true;
      } else {
        return false;
      }
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $languages = $this->lang->getLanguages();

      if ($this->getChekDirectory() === true) {

        $zip = new \ZipArchive;

        $backup_file = 'clicshopping_language_pack' . '-' . date('YmdHis') . '.zip';

        $result = $zip->open($this->transfert_directory . $backup_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($result === true) {

          $array = [
            'Shop',
            'ClicShoppingAdmin',
            'Apps'
          ];

          $apps = Apps::getAll();

          for ($i = 0, $n = \count($languages); $i < $n; $i++) {
            foreach ($array as $group) {
              if ($group != 'Apps') {
                if ($group == 'Shop') {
                  $dir = CLICSHOPPING::getConfig('dir_root', $group) . 'sources/languages/' . $languages[$i]['directory'] . '/';
                } else {
                  $dir = CLICSHOPPING::getConfig('dir_root', $group) . 'includes/languages/' . $languages[$i]['directory'] . '/';
                }

                if (is_dir($dir)) {
                  $files = FileSystem::getDirectoryContents($dir);

                  $localDir = '';

                  foreach ($files as $pathname) {
//                    $relative_path = $languages[$i]['directory'] . '/' . $this->current_version . '/' . $group . '/' . str_replace($dir, '', $pathname);
                    $relative_path = $languages[$i]['directory'] . '/' . $group . '/' . str_replace($dir, '', $pathname);

                    $path_parts = pathinfo($relative_path);

                    if ($path_parts['extension'] == 'txt' && $this->lang->detectFileEncoding($pathname) === true) {

                      $def = json_encode($this->lang->getDefinitionsFromFile($pathname), JSON_UNESCAPED_SLASHES);

                      if ($localDir != $path_parts['dirname']) {
                        $localDir = $path_parts['dirname'];
                        $zip->addEmptyDir($localDir);
                      }

                      $zip->addFromString($localDir . '/' . $path_parts['filename'] . '.json', $def);
                    }
                  }
                }
              } else {
//apps a changer
                foreach ($apps as $key) {
                  $dir = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Apps/' . $key['vendor'] . '/' . $key['app'] . '/languages/' . $languages[$i]['directory'] . '/';

                  if (is_dir($dir)) {
                    $files = FileSystem::getDirectoryContents($dir);

                    $localDir = '';
                    foreach ($files as $pathname) {

//                      $relative_path = $languages[$i]['directory'] . '/' . $this->current_version . '/' . $group . '/' . $key['vendor'] . '/' . $key['app'] . '/' . str_replace($dir, '', $pathname);
                      $relative_path = $languages[$i]['directory'] . '/' . $group . '/' . $key['vendor'] . '/' . $key['app'] . '/' . str_replace($dir, '', $pathname);

                      $path_parts = pathinfo($relative_path);

                      if ($path_parts['extension'] == 'txt' && $this->lang->detectFileEncoding($pathname) === true) {
                        $def = json_encode($this->lang->getDefinitionsFromFile($pathname), JSON_UNESCAPED_SLASHES);

                        if ($localDir != $path_parts['dirname']) {
                          $localDir = $path_parts['dirname'];
                          $zip->addEmptyDir($localDir);
                        }

                        $zip->addFromString($localDir . '/' . $path_parts['filename'] . '.json', $def);
                      }
                    }
                  }
                }
              }
            }
          }

          $zip->close();

        } else {
          error_log('ClicShopping\OM\ZipArchive::open() ' . $this->transfert_directory . $backup_file . ' file error: ');
        }

        if (isset($_POST['download'])) {
          header('Content-type: application/x-octet-stream');
          header('Content-disposition: attachment; filename=' . $backup_file);

          readfile($this->transfert_directory . $backup_file);
          unlink($this->transfert_directory . $backup_file);

          exit;
        } else {
          $this->app->redirect('TransfertLanguage');
        }
      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('error_transfert_directory_not_writeable'), 'error', 'TransfertLanguage');
        $this->app->redirect('TransfertLanguage');
      }
    }
  }