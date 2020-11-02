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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Upload as UploadClass;

  class Upload extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $transfert_directory;

    public function __construct()
    {
      $this->app = Registry::get('TransfertLanguage');
      $this->transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (is_writable($this->transfert_directory) && is_dir($this->transfert_directory)) {
        $zip_package = new UploadClass('sql_file', $this->transfert_directory, '777', ['zip']);

        if (is_null($zip_package->getFilename())) return false;

        $Zip = new \ZipArchive();

        $language_pack = [];

        $result = $Zip->open($this->transfert_directory . $zip_package->getFilename());

        if ($zip_package->check() === false || $zip_package->save() === false) {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_reading_zip_file', ['error' => $Zip->message($result)]), 'warning');
        }

        if ($result === true) {
          $language_pack = array('true');
          $Zip->close();
          unlink(CLICSHOPPING::BASE_DIR . 'Work/LanguagePackages/' . $zip_package->getFilename());
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_reading_zip_file', ['error' => $Zip->message($result)]), 'warning');
        }

        if (empty($language_pack)) {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_invalid_language_package'), 'warning');
          $this->app->redirect('Transferlanguage');
        }
      }
    }
  }